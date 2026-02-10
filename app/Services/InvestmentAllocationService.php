<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvestmentAllocationService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    /**
     * Allocate subscription funds to active projects.
     */
    public function allocateFromSubscription(UserSubscription $subscription): array
    {
        $user = $subscription->user;
        $plan = $subscription->plan;
        
        if (!$plan) {
            Log::warning('Cannot allocate: no plan found', ['subscription_id' => $subscription->id]);
            return [];
        }
        
        // Get active projects that are not fully funded
        $activeProjects = Project::where('status', 'active')
            ->whereRaw('current_fund < fund_goal')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($activeProjects->isEmpty()) {
            // If no unfunded projects, allocate to all active projects
            $activeProjects = Project::where('status', 'active')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        if ($activeProjects->isEmpty()) {
            Log::info('No active projects for allocation', ['user_id' => $user->id]);
            return [];
        }

        // Calculate available funds to allocate
        $availableAmount = $subscription->amount - $subscription->allocated_amount;
        
        if ($availableAmount <= 0) {
            Log::warning('No funds available for allocation', ['subscription_id' => $subscription->id]);
            return [];
        }

        $amountPerProject = $availableAmount / $activeProjects->count();
        $allocations = [];

        DB::transaction(function () use ($user, $subscription, $activeProjects, $amountPerProject, $availableAmount, &$allocations) {
            foreach ($activeProjects as $project) {
                $investment = ProjectInvestment::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $amountPerProject,
                    'status' => ProjectInvestment::STATUS_ALLOCATED,
                    'allocated_at' => now(),
                ]);

                $allocations[] = $investment;



                // DATA SYNC: Update Project current_fund
                $project->increment('current_fund', $amountPerProject);

                // THRESHOLD CHECK: Update status if goal met
                $this->checkFundingStatus($project->fresh());
            }
            
            // Mark the funds as allocated
            $subscription->increment('allocated_amount', $availableAmount);
        });

        return $allocations;
    }

    /**
     * Manually allocate investment to a specific project.
     */
    public function allocateToProject(User $user, Project $project, float $amount, ?UserSubscription $subscription = null): ProjectInvestment
    {
        return DB::transaction(function () use ($user, $project, $amount, $subscription) {
            $investment = ProjectInvestment::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'subscription_id' => $subscription?->id,
                'amount' => $amount,
                'status' => ProjectInvestment::STATUS_ALLOCATED,
                'allocated_at' => now(),
            ]);

            // Debit user wallet
            $this->walletService->debit(
                $user, 
                $amount, 
                'investment', 
                "Investment in {$project->title}", 
                $investment
            );

            // DATA SYNC: Update Project current_fund
            $project->increment('current_fund', $amount);

            // THRESHOLD CHECK: Update status if goal met
            $this->checkFundingStatus($project->fresh());

            return $investment;
        });
    }

    /**
     * Get total investment in a project.
     */
    public function getProjectTotalInvestment(Project $project): float
    {
        return ProjectInvestment::where('project_id', $project->id)
            ->active()
            ->sum('amount');
    }

    /**
     * Get user's total investments.
     */
    public function getUserTotalInvestment(User $user): float
    {
        return ProjectInvestment::where('user_id', $user->id)
            ->active()
            ->sum('amount');
    }

    /**
     * Get user's investments by project.
     */
    public function getUserInvestmentsByProject(User $user): array
    {
        return ProjectInvestment::where('user_id', $user->id)
            ->active()
            ->with('project')
            ->get()
            ->groupBy('project_id')
            ->map(fn($investments) => [
                'project' => $investments->first()->project,
                'total_amount' => $investments->sum('amount'),
                'investments' => $investments,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Activate allocated investments for a project.
     */
    public function activateProjectInvestments(Project $project): int
    {
        return ProjectInvestment::where('project_id', $project->id)
            ->where('status', ProjectInvestment::STATUS_ALLOCATED)
            ->update(['status' => ProjectInvestment::STATUS_ACTIVE]);
    }

    /**
     * Get investors for a project with their shares.
     */
    /**
     * Get investors for a project with their shares.
     * Default uses simple amount-based calculation for display.
     */
    public function getProjectInvestors(Project $project): array
    {
        $totalInvestment = $this->getProjectTotalInvestment($project);

        return ProjectInvestment::where('project_id', $project->id)
            ->active()
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($investments) use ($totalInvestment) {
                $userTotal = $investments->sum('amount');
                return [
                    'user' => $investments->first()->user,
                    'total_investment' => $userTotal,
                    'share_percentage' => $totalInvestment > 0 
                        ? ($userTotal / $totalInvestment) * 100 
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get investors with Time-Weighted shares for profit distribution.
     * Formula: Share = (Amount * Days Active) / Total Weighted Investment
     */
    public function getProjectInvestorsForDistribution(Project $project, \Carbon\Carbon $distributionDate): array
    {
        $investments = ProjectInvestment::where('project_id', $project->id)
            ->active()
            ->where('allocated_at', '<=', $distributionDate)
            ->with('user')
            ->get();

        if ($investments->isEmpty()) {
            return [];
        }

        $weightedInvestments = $investments->map(function ($investment) use ($distributionDate) {
            // Calculate duration in days, minimum 1 day
            $daysActive = max(1, $investment->allocated_at->diffInDays($distributionDate));
            $weightedAmount = $investment->amount * $daysActive;
            
            return [
                'user' => $investment->user,
                'user_id' => $investment->user_id,
                'investment' => $investment,
                'amount' => $investment->amount,
                'days_active' => $daysActive,
                'weighted_amount' => $weightedAmount,
            ];
        });

        // Group by user to aggregate multiple investments
        $groupedByUser = $weightedInvestments->groupBy('user_id');
        
        $totalWeightedInvestment = $weightedInvestments->sum('weighted_amount');

        if ($totalWeightedInvestment <= 0) {
            return [];
        }

        return $groupedByUser->map(function ($userInvestments) use ($totalWeightedInvestment) {
            $userTotalWeighted = $userInvestments->sum('weighted_amount');
            $userTotalAmount = $userInvestments->sum('amount');
            
            return [
                'user' => $userInvestments->first()['user'],
                'total_investment' => $userTotalAmount,
                'total_weighted_investment' => $userTotalWeighted,
                'share_percentage' => ($userTotalWeighted / $totalWeightedInvestment) * 100,
                'investments_count' => $userInvestments->count(),
            ];
        })->values()->toArray();
    }
    /**
     * Check if project funding goal is met and update status.
     */
    protected function checkFundingStatus(Project $project): void
    {
        // Floating point safety margin
        if ($project->current_fund >= ($project->fund_goal - 0.01)) {
            if ($project->status !== 'completed' && $project->status !== 'active') {
                 // Conceptually 'active' means 'collecting' in this codebase,
                 // but diagram says 'Activated' implies running.
                 // We will mark it 'completed' (Funding Completed) to stop further allocation
                 // OR we keep it 'active' but relying on the < fund_goal check.
                 //
                 // Given the diagram: "Funding Threshold Met? -> Yes -> Project Activated"
                 // And currently "active" allows funding.
                 // To strict match diagram: "Activated" meant EXECUTION.
                 // Use custom stats or 'completed'.
                 
                 // Decision: Mark as 'completed' (Funding Phase Complete)
                 // This effectively moves it to Execution phase in the Admin's eyes.
                 $project->update(['status' => 'completed']);
                 
                 Log::info('Project fully funded and activated', ['project_id' => $project->id]);
            }
        }
    }
}
