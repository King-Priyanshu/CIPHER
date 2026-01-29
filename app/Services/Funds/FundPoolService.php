<?php

namespace App\Services\Funds;

use App\Models\FundPool;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FundPoolService
{
    /**
     * Create a new fund pool for a period.
     */
    public function createPool(string $name, Carbon $periodStart, Carbon $periodEnd): FundPool
    {
        return FundPool::create([
            'name' => $name,
            'total_amount' => 0,
            'allocated_amount' => 0,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);
    }

    /**
     * Add funds to a pool.
     */
    public function addFunds(FundPool $pool, int $amount): FundPool
    {
        $pool->increment('total_amount', $amount);

        return $pool->fresh();
    }

    /**
     * Get the current active fund pool.
     */
    public function getCurrentPool(): ?FundPool
    {
        return FundPool::where('period_start', '<=', now())
            ->where('period_end', '>=', now())
            ->first();
    }

    /**
     * Get fund pools for a date range.
     */
    public function getPoolsForPeriod(Carbon $start, Carbon $end): Collection
    {
        return FundPool::where('period_start', '>=', $start)
            ->where('period_end', '<=', $end)
            ->orderBy('period_start')
            ->get();
    }

    /**
     * Calculate available funds in a pool.
     */
    public function getAvailableFunds(FundPool $pool): int
    {
        return $pool->total_amount - $pool->allocated_amount;
    }
}
