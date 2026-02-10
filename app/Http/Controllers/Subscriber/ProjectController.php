<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use Illuminate\Support\Facades\DB;
use App\Services\WalletService;
use App\Services\InvestmentService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $projects = Project::where('status', '!=', 'draft')
            ->where('visibility_status', 'visible')
            ->with(['investments' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        $activeSubscription = app(\App\Services\SubscriptionService::class)->getActiveSubscription($user->id);
        $availableBalance = $activeSubscription ? ($activeSubscription->amount - $activeSubscription->allocated_amount) : 0;

        return view('subscriber.projects.index', compact('projects', 'activeSubscription', 'availableBalance', 'user'));
    }

    public function show(Project $project)
    {
        // Ensure user can see this project (e.g. not draft and visible)
        if ($project->status === 'draft' || $project->visibility_status === 'hidden') {
            abort(404);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $plans = InvestmentPlan::all(); // Fetch plans for the dropdown
        $investorCount = $project->investments()->active()->distinct('user_id')->count();
        
        // Pass wallet balance instead of subscription logic (Phase 2 change)
        // We now use Wallet for transactions, subscription allocation is mostly legacy/auto-alloc logic
        // But let's keep consistent if needed. For now wallet balance is source of truth.

        // Get investors (sponsors)
        $investors = app(\App\Services\InvestmentAllocationService::class)->getProjectInvestors($project);

        // User's allocation history in this project
        $userInvestments = $project->investments()->where('user_id', $user->id)->latest()->get();
        
        // User's royalty summary in this project
        $userProfits = \App\Models\UserProfitLog::where('user_id', $user->id)
            ->whereHas('distribution', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->latest()
            ->get();

        return view('subscriber.projects.show', compact('project', 'plans', 'user', 'investorCount'));
    }

    /**
     * Handle Investment Request.
     */
    public function invest(Request $request, Project $project, InvestmentService $investmentService, ActivityLogger $logger)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . ($project->min_investment ?? 1),
            'plan_id' => 'required|exists:investment_plans,id',
        ]);

        try {
            DB::transaction(function () use ($request, $project, $investmentService, $logger) {
                $user = auth()->user();
                $plan = InvestmentPlan::findOrFail($request->plan_id);

                // Check wallet balance handled inside Service -> invest() (usually) 
                // but let's be safe or rely on service.
                // The service `invest` method we reviewed earlier handles debit and creation.
                
                $investment = $investmentService->invest(
                    $user,
                    $project,
                    $request->amount,
                    $plan
                );

                $logger->logFinancial('investment.created', "Invested ₹{$request->amount} in Project {$project->title}", $investment);
            });

            return redirect()->route('subscriber.dashboard')
                ->with('success', "Successfully invested ₹{$request->amount} in {$project->title}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Investment failed: ' . $e->getMessage());
        }
    }
}
