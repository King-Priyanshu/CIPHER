<?php

namespace App\Services\Subscriptions;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe a user to a plan (new subscription).
     */
    public function subscribe(User $user, SubscriptionPlan $plan): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan) {
            // Cancel existing active subscriptions
            $user->subscriptions()->whereIn('status', ['active', 'trialing'])->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'ends_at' => Carbon::now(),
            ]);

            // Create new subscription (pending until payment confirmed)
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'starts_at' => null,
                'ends_at' => null,
            ]);

            $this->logActivity($user, 'subscription.created', "Subscribed to {$plan->name} plan (pending payment).");

            return $subscription;
        });
    }

    /**
     * Activate a subscription after successful payment.
     */
    public function activate(UserSubscription $subscription): UserSubscription
    {
        $subscription->update([
            'status' => 'active',
            'starts_at' => Carbon::now(),
            'ends_at' => $this->calculateEndDate($subscription->plan->interval),
            'retry_count' => 0,
            'grace_until' => null,
        ]);

        $this->logActivity($subscription->user, 'subscription.activated', "Subscription activated for {$subscription->plan->name}.");

        return $subscription;
    }

    /**
     * Upgrade a subscription to a higher plan (applies immediately).
     */
    public function upgrade(User $user, SubscriptionPlan $newPlan): UserSubscription
    {
        $currentSubscription = $user->subscription;

        return DB::transaction(function () use ($user, $newPlan, $currentSubscription) {
            // Cancel current subscription immediately
            if ($currentSubscription) {
                $currentSubscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => Carbon::now(),
                    'ends_at' => Carbon::now(),
                ]);
            }

            // Create new subscription with immediate start
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'status' => 'active',
                'starts_at' => Carbon::now(),
                'ends_at' => $this->calculateEndDate($newPlan->interval),
            ]);

            $this->logActivity($user, 'subscription.upgraded', "Upgraded to {$newPlan->name} plan (effective immediately).");

            return $subscription;
        });
    }

    /**
     * Downgrade a subscription to a lower plan (applies at next billing cycle).
     */
    public function downgrade(User $user, SubscriptionPlan $newPlan): UserSubscription
    {
        $currentSubscription = $user->subscription;

        if (!$currentSubscription) {
            throw new \Exception('No active subscription to downgrade.');
        }

        // Schedule the downgrade for end of current period
        $currentSubscription->update([
            'scheduled_plan_id' => $newPlan->id,
        ]);

        $this->logActivity($user, 'subscription.downgrade_scheduled', "Downgrade to {$newPlan->name} scheduled for {$currentSubscription->ends_at->format('M d, Y')}.");

        return $currentSubscription;
    }

    /**
     * Cancel a subscription (access remains until end of billing period).
     */
    public function cancel(UserSubscription $subscription): bool
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            // ends_at remains unchanged - access until period ends
        ]);

        $this->logActivity($subscription->user, 'subscription.cancelled', "Subscription cancelled. Access until {$subscription->ends_at->format('M d, Y')}.");

        return true;
    }

    /**
     * Suspend a subscription after max payment retries.
     */
    public function suspend(UserSubscription $subscription): bool
    {
        $subscription->update([
            'status' => 'suspended',
            'grace_until' => null,
        ]);

        $this->logActivity($subscription->user, 'subscription.suspended', "Subscription suspended due to payment failures.");

        return true;
    }

    /**
     * Renew a subscription after successful recurring payment.
     */
    public function renew(UserSubscription $subscription): UserSubscription
    {
        $subscription->update([
            'status' => 'active',
            'starts_at' => Carbon::now(),
            'ends_at' => $this->calculateEndDate($subscription->plan->interval),
            'retry_count' => 0,
            'grace_until' => null,
        ]);

        $this->logActivity($subscription->user, 'subscription.renewed', "Subscription renewed for another billing cycle.");

        return $subscription;
    }

    /**
     * Mark subscription as past due and start grace period.
     */
    public function markPastDue(UserSubscription $subscription, int $graceDays = 7): UserSubscription
    {
        $retryCount = ($subscription->retry_count ?? 0) + 1;

        $subscription->update([
            'status' => 'past_due',
            'retry_count' => $retryCount,
            'grace_until' => $subscription->grace_until ?? Carbon::now()->addDays($graceDays),
        ]);

        $this->logActivity($subscription->user, 'subscription.past_due', "Payment failed (attempt {$retryCount}). Grace period until {$subscription->grace_until->format('M d, Y')}.");

        return $subscription;
    }

    /**
     * Mark subscription as expired (grace period ended).
     */
    public function expire(UserSubscription $subscription): bool
    {
        $subscription->update([
            'status' => 'expired',
            'grace_until' => null,
        ]);

        $this->logActivity($subscription->user, 'subscription.expired', "Subscription expired after grace period.");

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
            'yearly', 'annual' => Carbon::now()->addYear(),
            default => Carbon::now()->addMonth(),
        };
    }

    /**
     * Log subscription activity.
     */
    protected function logActivity(User $user, string $action, string $description): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

