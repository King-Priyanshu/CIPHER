<?php

namespace App\Services;

use App\Models\UserSubscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class SubscriptionService
{
    protected InvestmentAllocationService $investmentService;

    public function __construct(InvestmentAllocationService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    /**
     * Activate a subscription for a user.
     *
     * @param string $userId
     * @param string $planId
     * @return UserSubscription
     */
    public function activateSubscription(string $userId, string $planId): UserSubscription
    {
        // Deactivate any existing active subscriptions
        UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Get the plan to determine interval
        $plan = \App\Models\SubscriptionPlan::find($planId);
        $interval = $plan?->interval ?? 'monthly';
        
        $now = now();
        // Fixed 11-month cycle for all plans
        $maturityDate = $now->copy()->addMonths(11); // 11 months from start
        
        // Calculate end date based on interval
        $endsAt = match($interval) {
            'yearly' => $now->copy()->addYear(),
            'quarterly' => $now->copy()->addMonths(3),
            default => $now->copy()->addMonth(),
        };

        // Create new active subscription
        $subscription = UserSubscription::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'amount' => $plan->price, // Store the amount from the plan
            'allocated_amount' => 0,
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => $endsAt,
            'maturity_date' => $maturityDate,
        ]);

        // Load relationships
        $subscription->load('user', 'plan');

        // Check user participation mode
        $user = \App\Models\User::find($userId);
        
        // ALL funds go to pool first - Admin allocates to projects
        // Funds sit in 'amount' - 'allocated_amount' (0) = full balance available
        Log::info('Subscription activated - Funds pooled for Admin allocation', [
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'participation_mode' => $user->participation_mode,
            'amount' => $subscription->amount,
        ]);

        return $subscription;
    }

    /**
     * Renew an existing subscription.
     */
    public function renewSubscription(UserSubscription $subscription): UserSubscription
    {
        // Check if subscription has reached maturity (11-month cycle)
        if ($subscription->maturity_date && $subscription->ends_at && $subscription->ends_at->greaterThanOrEqualTo($subscription->maturity_date)) {
            Log::info('Subscription reached maturity, not renewing', ['id' => $subscription->id]);
            // Optionally update status to expired or generic 'cancelled' for now as we don't have 'matured' enum
            $subscription->update(['status' => 'expired']); 
            return $subscription;
        }

        // Proceed with renewal
        // Ensure we continue from the last end date if it's in the future/recent, else use now()
        $newStart = $subscription->ends_at && $subscription->ends_at->isFuture() ? $subscription->ends_at : now();
        $newEnd = $newStart->copy()->addMonth();

        // Enforce cap at maturity date
        if ($subscription->maturity_date && $newEnd->greaterThan($subscription->maturity_date)) {
             $newEnd = $subscription->maturity_date;
        }

        // Determine new amount based on current plan price (price might have changed)
        $currentPlanPrice = $subscription->plan->price ?? $subscription->amount;

        // Reset allocated amount for the new period? 
        // Logic: Should the unused balance carry over? 
        // "Subscription is monthly participation". Usually implies fresh cycle.
        // But for Manual mode, if they didn't invest, accumulating balance is nice.
        // For now, let's ADD the new amount to the existing record?
        // Wait, `amount` is per record. `starts_at` / `ends_at` updates on the SAME record.
        // If we reuse the same record, we must INCREMENT `amount`.
        
        $subscription->update([
            'status' => 'active',
            'amount' => $subscription->amount + $currentPlanPrice, // Add new period's funds
            'starts_at' => $newStart, 
            'ends_at' => $newEnd,
        ]);

        // Reload relationships
        $subscription->load('user', 'plan');
        $user = $subscription->user;

        // ALL funds go to pool - Admin allocates to projects
        Log::info('Subscription renewed - Funds added to pool for Admin allocation', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'participation_mode' => $user->participation_mode,
            'added_amount' => $currentPlanPrice,
        ]);

        return $subscription;
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(UserSubscription $subscription): UserSubscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return $subscription;
    }

    /**
     * Request cancellation with refund.
     */
    public function requestCancellationWithRefund(\App\Models\User $user, \App\Models\UserSubscription $subscription, string $reason): bool
    {
        // 1. Mark subscription as cancelling? Or just status quo until approved?
        // Let's keep subscription active but maybe flag it? 
        // For simple MVP: Create Refund record. Admin will cancel subscription upon approval.
        
        \App\Models\Refund::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->amount, // Request full amount refund as per policy
            'status' => 'pending',
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get active subscription for a user.
     */
    public function getActiveSubscription(int $userId): ?UserSubscription
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->first();
    }
}
