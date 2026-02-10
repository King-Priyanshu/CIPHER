<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'business_type',
        'royalty_model',
        'visibility_status',
        'allocation_eligibility',
        'status',
        'fund_goal',
        'roi_percentage',
        'duration_months',
        'location',
        'image_url',
        'is_featured',
        'current_fund',
        'starts_at',
        'ends_at',
        'risk_level',
        'outcome_description',
        'images',
        'documents',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'fund_goal' => 'decimal:2',
        'current_fund' => 'decimal:2',
        'images' => 'array',
        'documents' => 'array',
    ];

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function investmentPlans()
    {
        return $this->hasMany(InvestmentPlan::class);
    }

    public function fundAllocations()
    {
        return $this->hasMany(FundAllocation::class);
    }

    /**
     * Get all investments in this project.
     */
    public function investments()
    {
        return $this->hasMany(ProjectInvestment::class);
    }

    /**
     * Get total investment amount for this project.
     */
    public function getTotalInvestmentAttribute(): float
    {
        return $this->investments()->active()->sum('amount');
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Calculate funding progress percentage.
     */
    public function getFundingProgressAttribute(): float
    {
        if ($this->fund_goal <= 0) {
            return 0;
        }
        return min(100, ($this->current_fund / $this->fund_goal) * 100);
    }
}
