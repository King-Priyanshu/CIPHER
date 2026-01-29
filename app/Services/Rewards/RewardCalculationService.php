<?php

namespace App\Services\Rewards;

use App\Models\RewardPool;
use App\Models\UserSubscription;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class RewardCalculationService
{
    /**
     * Calculate potential rewards for a given pool.
     * This is a simplified logic: Distribute evenly among active subscribers.
     * In a real systems, this would be based on tiers, points, or contribution duration.
     */
    public function calculateForPool(RewardPool $pool)
    {
        // 1. Identify eligible users (active subscribers)
        $eligibleSubscriptions = UserSubscription::where('status', 'active')->get();

        if ($eligibleSubscriptions->isEmpty()) {
            return;
        }

        $totalSubscribers = $eligibleSubscriptions->count();
        $amountPerUser = $pool->total_amount / $totalSubscribers;

        DB::transaction(function () use ($pool, $eligibleSubscriptions, $amountPerUser) {
            foreach ($eligibleSubscriptions as $sub) {
                Reward::create([
                    'user_id' => $sub->user_id,
                    'reward_pool_id' => $pool->id,
                    'amount' => $amountPerUser,
                    'status' => 'pending',
                ]);
            }
            
            $pool->update(['status' => 'calculating']);
        });
    }
}
