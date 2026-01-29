<?php

namespace App\Services\Subscriptions;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe a user to a plan.
     */
    public function subscribe(User $user, SubscriptionPlan $plan): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan) {
            // Cancel existing active subscriptions
            $user->subscriptions()->whereIn('status', ['active', 'trialing'])->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'ends_at' => Carbon::now(), // End immediately on swap
            ]);

            // Create new subscription
            return UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active', // In real app, this might be 'pending' until payment
                'starts_at' => Carbon::now(),
                'ends_at' => $this->calculateEndDate($plan->interval),
            ]);
        });
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(UserSubscription $subscription): bool
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Helper to calculate end date based on interval.
     */
    protected function calculateEndDate(string $interval): Carbon
    {
        return match ($interval) {
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'annual' => Carbon::now()->addYear(),
            default => Carbon::now()->addMonth(),
        };
    }
}
