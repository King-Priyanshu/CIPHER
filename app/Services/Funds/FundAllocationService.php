<?php

namespace App\Services\Funds;

use App\Models\FundAllocation;
use App\Models\FundPool;
use App\Models\Project;
use Illuminate\Support\Collection;

class FundAllocationService
{
    public function __construct(
        protected FundPoolService $fundPoolService
    ) {}

    /**
     * Allocate funds from a pool to a project.
     */
    public function allocate(FundPool $pool, Project $project, int $amount): FundAllocation
    {
        $available = $this->fundPoolService->getAvailableFunds($pool);

        if ($amount > $available) {
            throw new \InvalidArgumentException(
                "Insufficient funds. Available: {$available}, Requested: {$amount}"
            );
        }

        $allocation = FundAllocation::create([
            'fund_pool_id' => $pool->id,
            'project_id' => $project->id,
            'amount' => $amount,
            'allocated_at' => now(),
        ]);

        $pool->increment('allocated_amount', $amount);
        $project->increment('current_fund', $amount);

        return $allocation;
    }

    /**
     * Get all allocations for a project.
     */
    public function getProjectAllocations(Project $project): Collection
    {
        return FundAllocation::where('project_id', $project->id)
            ->with('fundPool')
            ->orderBy('allocated_at', 'desc')
            ->get();
    }

    /**
     * Get all allocations from a fund pool.
     */
    public function getPoolAllocations(FundPool $pool): Collection
    {
        return FundAllocation::where('fund_pool_id', $pool->id)
            ->with('project')
            ->orderBy('allocated_at', 'desc')
            ->get();
    }

    /**
     * Calculate total allocated to a project.
     */
    public function getTotalAllocatedToProject(Project $project): int
    {
        return FundAllocation::where('project_id', $project->id)->sum('amount');
    }
}
