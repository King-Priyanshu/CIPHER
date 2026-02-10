<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\SubscriptionPlan;
use App\Models\ProjectInvestment;
use Illuminate\Support\Facades\DB;
use App\Services\WalletService;
use App\Services\InvestmentService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Check active subscription
        $activeSubscription = \App\Models\UserSubscription::where('user_id', $user->id)
            ->active()
            ->with('plan')
            ->latest()
            ->first();

        // Projects available for new manual investment
        $availableProjects = Project::where('status', 'active')
            ->where('visibility_status', 'visible')
            ->whereIn('allocation_eligibility', ['manual_only', 'both'])
            ->orderBy('created_at', 'desc')
            ->get();

        // User's active investments with project details
        $myInvestments = $user->investments()
            ->with('project', 'investmentPlan')
            ->active()
            ->latest()
            ->get();

        $myProjects = $myInvestments->pluck('project')->filter()->unique('id');

        // Investment statistics (for subscribed users)
        $totalInvested = $myInvestments->sum('amount');
        $totalProfits = \App\Models\UserProfitLog::where('user_id', $user->id)->sum('amount');
        $liveProjectCount = $myProjects->count();

        // Per-project investment breakdown
        $projectStats = $myProjects->map(function ($project) use ($user) {
            $projectInvestments = $project->investments()
                ->where('user_id', $user->id)
                ->active()
                ->get();

            $userProfit = \App\Models\UserProfitLog::where('user_id', $user->id)
                ->whereHas('profitDistribution', fn($q) => $q->where('project_id', $project->id))
                ->sum('amount');

            return (object) [
                'project' => $project,
                'total_invested' => $projectInvestments->sum('amount'),
                'investment_count' => $projectInvestments->count(),
                'user_profit' => $userProfit,
                'latest_investment' => $projectInvestments->first(),
            ];
        });

        return view('subscriber.projects.index', compact(
            'availableProjects',
            'myProjects',
            'user',
            'activeSubscription',
            'myInvestments',
            'totalInvested',
            'totalProfits',
            'liveProjectCount',
            'projectStats'
        ));
    }

    public function show(Project $project)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Check if user has an active investment in this project
        $hasInvestment = $project->investments()
            ->where('user_id', $user->id)
            ->active()
            ->exists();

        // Allow viewing if it's available for new manual investment OR if user already invested
        $isAvailable = $project->status === 'active'
            && $project->visibility_status === 'visible'
            && in_array($project->allocation_eligibility, ['manual_only', 'both']);

        if (!$hasInvestment && !$isAvailable) {
            abort(403, 'You do not have access to this project statistics.');
        }

        $plans = SubscriptionPlan::where('is_active', true)->get();
        $investorCount = $project->investments()->active()->distinct('user_id')->count();

        // Get investors (sponsors) - Only show if user has invested? 
        // Spec: "view statistics ONLY for projects they invested in"
        $investors = $hasInvestment
            ? app(\App\Services\InvestmentAllocationService::class)->getProjectInvestors($project)
            : [];

        $userInvestments = $project->investments()->where('user_id', $user->id)->latest()->get();

        $userProfits = \App\Models\UserProfitLog::where('user_id', $user->id)
            ->whereHas('profitDistribution', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->latest()
            ->get();

        return view('subscriber.projects.show', compact('project', 'plans', 'user', 'investorCount', 'hasInvestment', 'investors', 'userProfits'));
    }

    /**
     * Handle Manual Investment selection and redirect to payment.
     */
    public function invest(Request $request, Project $project, InvestmentService $investmentService, ActivityLogger $logger)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . ($project->min_investment ?? 1),
            'plan_id' => 'required|exists:investment_plans,id',
            'payment_method' => 'required|in:wallet,gateway',
        ]);

        try {
            $user = auth()->user();
            $plan = InvestmentPlan::findOrFail($request->plan_id);

            // 1. Create Pending Investment
            $investment = $investmentService->createPendingInvestment(
                $user,
                $project,
                $plan,
                (float) $request->amount
            );

            // 2. Handle Payment Method
            if ($request->payment_method === 'wallet') {
                $investmentService->finalizeInvestment($investment);
                return redirect()->route('subscriber.investments.index')
                    ->with('success', "Successfully invested ₹{$request->amount} in {$project->title} via Wallet.");
            } else {
                // Gateway: Redirect to a payment initiation route or return JSON for Alpine/JS to handle Razorpay
                return redirect()->route('subscriber.projects.pay', ['investment' => $investment]);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Investment failed: ' . $e->getMessage());
        }
    }

    /**
     * Show Payment Initiation Page.
     */
    public function pay(ProjectInvestment $investment)
    {
        if ($investment->user_id !== auth()->id() || $investment->status !== ProjectInvestment::STATUS_PENDING_PAYMENT) {
            abort(403);
        }

        return view('subscriber.projects.pay', compact('investment'));
    }

    /**
     * Handle Automatic Investment request.
     */
    public function autoInvest(Request $request, InvestmentService $investmentService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'plan_id' => 'required|exists:investment_plans,id',
            'payment_method' => 'required|in:wallet,gateway',
        ]);

        try {
            $user = auth()->user();
            $plan = InvestmentPlan::findOrFail($request->plan_id);

            // 1. Create Pending Auto Investment
            $investment = $investmentService->createPendingAutoInvestment(
                $user,
                $plan,
                (float) $request->amount
            );

            // 2. Handle Payment
            if ($request->payment_method === 'wallet') {
                $investmentService->finalizeInvestment($investment);
                return redirect()->route('subscriber.investments.index')
                    ->with('success', "Successfully initiated auto-allocation of ₹{$request->amount} via Wallet.");
            } else {
                return redirect()->route('subscriber.projects.pay', ['investment' => $investment]);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Auto-investment failed: ' . $e->getMessage());
        }
    }
}
