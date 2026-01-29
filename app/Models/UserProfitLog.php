<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profit_distribution_id',
        'project_investment_id',
        'amount',
        'status',
        'credited_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'credited_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CREDITED = 'credited';
    const STATUS_WITHDRAWN = 'withdrawn';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profitDistribution()
    {
        return $this->belongsTo(ProfitDistribution::class);
    }

    public function projectInvestment()
    {
        return $this->belongsTo(ProjectInvestment::class);
    }

    /**
     * Get the project through the distribution.
     */
    public function getProjectAttribute()
    {
        return $this->profitDistribution?->project;
    }

    /**
     * Scope for credited profits.
     */
    public function scopeCredited($query)
    {
        return $query->where('status', self::STATUS_CREDITED);
    }

    /**
     * Scope for pending profits.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
