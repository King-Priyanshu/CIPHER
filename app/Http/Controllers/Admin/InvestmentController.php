<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Services\InvestmentAllocationService;
use App\Services\AutoAllocationService;
use App\Models\InvestmentPlan;
use App\Models\UserSubscription;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    protected InvestmentAllocationService $investmentService;
    protected AutoAllocationService $autoAllocationService;

    protected ActivityLogger $logger;

    public function __construct(InvestmentAllocationService $investmentService, AutoAllocationService $autoAllocationService, ActivityLogger $logger)
    {
        $this->investmentService = $investmentService;
        $this->autoAllocationService = $autoAllocationService;
        $this->logger = $logger;
    }

    /**
     * Display all investments.
     */
    public function index(Request $request)
    {
        $query = ProjectInvestment::with(['user', 'project', 'subscription'])
            ->orderBy('created_at', 'desc');

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->paginate(20);
        $projects = Project::orderBy('title')->get();
        // Filter Dropdown: Only users with Active/Trialing subscriptions
        $users = User::whereHas('subscriptions', function ($q) {
            $q->whereIn('status', ['active', 'trialing']);
        })->orderBy('name')->get();

        // Stats
        $stats = [
            'total_invested' => ProjectInvestment::active()->sum('amount'),
            'total_investors' => ProjectInvestment::active()->distinct('user_id')->count(),
            'active_investments' => ProjectInvestment::active()->count(),
            'pooled_funds' => \App\Models\UserSubscription::active()->get()->sum(function ($sub) {
                return $sub->amount - $sub->allocated_amount;
            }),
        ];

        return view('admin.investments.index', compact('investments', 'projects', 'users', 'stats'));
    }

    /**
     * Show investments for a specific project.
     */
    public function byProject(Project $project)
    {
        $investments = ProjectInvestment::where('project_id', $project->id)
            ->with(['user', 'subscription'])
            ->orderBy('amount', 'desc')
            ->get();

        $investors = $this->investmentService->getProjectInvestors($project);
        $totalInvestment = $this->investmentService->getProjectTotalInvestment($project);

        return view('admin.investments.project', compact('project', 'investments', 'investors', 'totalInvestment'));
    }

    /**
     * Manually allocate investment.
     */
    public function allocate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = User::findOrFail($request->user_id);
        $project = Project::findOrFail($request->project_id);

        // Find active subscription to deduct funds from
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->latest() // Get the most recent one
            ->first();

        if (!$subscription) {
            return redirect()->back()->with('error', 'Allocation Failed: User does not have an active subscription.');
        }

        try {
            $investment = $this->investmentService->allocateToProject(
                $user,
                $project,
                $request->amount,
                $subscription // Pass subscription to deduct from wallet
            );

            $this->logger->logFinancial('investment.manual_allocate', "Allocated ₹{$request->amount} for User {$user->id} to Project {$project->id}", $investment);

            return redirect()->route('admin.investments.index')
                ->with('success', "₹{$request->amount} allocated to {$user->name} for {$project->title}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Allocation Failed: ' . $e->getMessage());
        }
    }

    /**
     * Activate all investments for a project.
     */
    public function activateProject(Project $project)
    {
        $count = $this->investmentService->activateProjectInvestments($project);

        return redirect()->back()
            ->with('success', "{$count} investments activated for {$project->title}");
    }

    /**
     * Trigger auto-allocation for a user or all users.
     */
    public function autoAllocate(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'nullable|numeric|min:1',
        ]);

        try {
            if ($request->user_id) {
                $user = User::findOrFail($request->user_id);
                $investments = $this->autoAllocationService->allocateForUser($user, $request->amount, auth()->id());
                $count = count($investments);
                $msg = "{$count} investments created for {$user->name}.";
            } else {
                // Bulk allocation logic
                $count = 0;
                $users = User::whereHas('roles', function ($q) {
                    $q->where('slug', 'subscriber');
                })
                    ->where('status', 'active')
                    ->where('participation_mode', 'auto')
                    ->get();

                foreach ($users as $user) {
                    /** @var User $user */
                    try {
                        $investments = $this->autoAllocationService->allocateForUser($user, null, auth()->id());
                        $count += count($investments);
                    } catch (\Exception $e) {
                        // Log error but continue
                    }
                }
                $msg = "Auto-allocation run complete. {$count} investments created.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Allocation failed: ' . $e->getMessage());
        }
    }

    /**
     * Allocate a pending automatic investment to a project.
     */
    public function allocatePending(Request $request, ProjectInvestment $investment)
    {
        if ($investment->status !== ProjectInvestment::STATUS_PENDING_ADMIN_ALLOCATION) {
            return redirect()->back()->with('error', 'Investment is not in a pending allocation state.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($request->project_id);

        try {
            DB::transaction(function () use ($investment, $project) {
                $investment->update([
                    'project_id' => $project->id,
                    'status' => ProjectInvestment::STATUS_ACTIVE,
                    'allocated_at' => now(),
                    'admin_id' => auth()->id(),
                    'roi_start_date' => now()->addDays(1),
                    'roi_end_date' => now()->addMonths($investment->investmentPlan->duration_months ?? 12),
                ]);

                // Update project funding
                $project->increment('current_fund', (float) $investment->amount);
            });

            $this->logger->logFinancial('investment.admin_allocate', "Admin allocated Project #{$project->id} to Investment #{$investment->id}", $investment);

            return redirect()->route('admin.investments.index')
                ->with('success', "Investment allocated to project {$project->title} successfully.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Allocation failed: ' . $e->getMessage());
        }
    }
}
