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
        'subscription_id',
        'amount',
        'allocation_type',
        'admin_id',
        'status',
        'allocated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    const STATUS_ALLOCATED = 'allocated';
    const STATUS_ACTIVE = 'active';
    const STATUS_WITHDRAWN = 'withdrawn';

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
