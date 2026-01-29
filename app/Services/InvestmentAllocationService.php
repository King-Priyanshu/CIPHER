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
    /**
     * Allocate subscription funds to active projects.
     */
    public function allocateFromSubscription(UserSubscription $subscription): array
    {
        $user = $subscription->user;
        $plan = $subscription->plan;
        
        // Get active projects for allocation
        $activeProjects = Project::where('status', 'active')
            ->where('is_funded', false)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($activeProjects->isEmpty()) {
            Log::info('No active projects for allocation', ['user_id' => $user->id]);
            return [];
        }

        $amountPerProject = $plan->price / $activeProjects->count();
        $allocations = [];

        DB::transaction(function () use ($user, $subscription, $activeProjects, $amountPerProject, &$allocations) {
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

                Log::info('Investment allocated', [
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'amount' => $amountPerProject,
                ]);
            }
        });

        return $allocations;
    }

    /**
     * Manually allocate investment to a specific project.
     */
    public function allocateToProject(User $user, Project $project, float $amount, ?UserSubscription $subscription = null): ProjectInvestment
    {
        return ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'subscription_id' => $subscription?->id,
            'amount' => $amount,
            'status' => ProjectInvestment::STATUS_ALLOCATED,
            'allocated_at' => now(),
        ]);
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
}
