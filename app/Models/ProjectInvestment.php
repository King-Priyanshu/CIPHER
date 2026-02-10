<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'investment_plan_id',
        'amount',
        'allocation_type',
        'admin_id',
        'status',
        'allocated_at',
        'razorpay_order_id',
        'razorpay_payment_id',
        'stripe_payment_intent_id',
        'roi_start_date',
        'roi_end_date',
        'total_roi_earned',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PENDING_ADMIN_ALLOCATION = 'pending_admin_allocation';
    const STATUS_ALLOCATED = 'allocated';
    const STATUS_ACTIVE = 'active';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function profitLogs()
    {
        return $this->hasMany(UserProfitLog::class);
    }

    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id');
    }

    /**
     * Scope for active investments.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ALLOCATED, self::STATUS_ACTIVE]);
    }

    /**
     * Calculate user's share percentage in a project.
     */
    public function getSharePercentageAttribute(): float
    {
        $totalInvestment = self::where('project_id', $this->project_id)
            ->active()
            ->sum('amount');

        if ($totalInvestment <= 0) {
            return 0;
        }

        return ($this->amount / $totalInvestment) * 100;
    }
}
