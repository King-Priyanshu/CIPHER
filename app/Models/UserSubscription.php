<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_plan_id', // Alias for compatibility
        'stripe_subscription_id',
        'razorpay_subscription_id',
        'razorpay_customer_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'grace_until',
        'retry_count',
        'current_period_start',
        'current_period_end',
        'cancel_reason',
        'cancel_at_period_end',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'grace_until' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'retry_count' => 'integer',
        'cancel_at_period_end' => 'boolean',
    ];

    /**
     * Subscription States: pending, active, past_due, cancelled, expired, suspended
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Check if subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'trialing';
    }

    /**
     * Check if subscription is past due (payment failed but in grace).
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Check if subscription is expired (ended or grace period passed).
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        // If ends_at is set and in the past, and not in grace period
        if ($this->ends_at && $this->ends_at->isPast() && !$this->isInGracePeriod()) {
            return true;
        }

        return false;
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if subscription is suspended (after max retry failures).
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if currently in grace period after payment failure.
     */
    public function isInGracePeriod(): bool
    {
        return $this->grace_until && now()->lt($this->grace_until);
    }

    /**
     * Check if user should have access to subscription features.
     * Returns true if active OR in grace period.
     */
    public function hasAccess(): bool
    {
        if ($this->isActive()) {
            return true;
        }

        // Allow access during grace period even if past_due
        if ($this->isPastDue() && $this->isInGracePeriod()) {
            return true;
        }

        return false;
    }

    /**
     * Get the number of days remaining in grace period.
     */
    public function graceDaysRemaining(): int
    {
        if (!$this->isInGracePeriod()) {
            return 0;
        }

        return now()->diffInDays($this->grace_until);
    }
}
