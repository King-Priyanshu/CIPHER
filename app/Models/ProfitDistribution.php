<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'month',
        'total_profit',
        'distributed_amount',
        'status',
        'declared_at',
        'distributed_at',
        'declared_by',
        'notes',
        'supporting_documents',
    ];

    protected $casts = [
        'total_profit' => 'decimal:2',
        'distributed_amount' => 'decimal:2',
        'declared_at' => 'datetime',
        'distributed_at' => 'datetime',
        'month' => 'date',
        'supporting_documents' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_DISTRIBUTING = 'distributing';
    const STATUS_COMPLETED = 'completed';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function declaredBy()
    {
        return $this->belongsTo(User::class, 'declared_by');
    }

    public function profitLogs()
    {
        return $this->hasMany(UserProfitLog::class);
    }

    /**
     * Check if distribution is complete.
     */
    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get remaining amount to distribute.
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_profit - $this->distributed_amount;
    }

    /**
     * Get percentage distributed.
     */
    public function getDistributionProgressAttribute(): float
    {
        if ($this->total_profit <= 0) {
            return 0;
        }

        return ($this->distributed_amount / $this->total_profit) * 100;
    }
}
