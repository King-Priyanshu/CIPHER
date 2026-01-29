<?php

namespace App\Services\Rewards;

use App\Models\RewardPool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RewardDistributionService
{
    /**
     * Finalize and "send" the rewards to users.
     */
    public function distribute(RewardPool $pool)
    {
        if ($pool->status === 'distributed') {
            return;
        }

        DB::transaction(function () use ($pool) {
            // Update all pending rewards to distributed
            $pool->rewards()->where('status', 'pending')->update([
                'status' => 'distributed',
                'distributed_at' => Carbon::now(),
            ]);

            // Update pool status
            $totalDistributed = $pool->rewards()->sum('amount');
            
            $pool->update([
                'status' => 'distributed',
                'distributed_amount' => $totalDistributed,
                'distribution_date' => Carbon::today(),
            ]);
        });
    }
}
