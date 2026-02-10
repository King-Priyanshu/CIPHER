<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'slug',
        'type',
        'min_investment',
        'max_investment',
        'frequency',
        'duration_months',
        'expected_return_percentage',
        'refund_rule',
        'description',
        'is_active',
        'tiers', // JSON field for tier configuration
    ];

    protected $casts = [
        'min_investment' => 'decimal:2',
        'max_investment' => 'decimal:2',
        'expected_return_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'tiers' => 'array',
    ];

    /**
     * Get the project that owns the investment plan.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get tier configuration.
     */
    public function getTiersAttribute($value)
    {
        $defaultTiers = [
            [
                'name' => 'Silver',
                'min_amount' => 1000,
                'max_amount' => 10000,
                'roi' => $this->expected_return_percentage,
                'benefits' => ['Standard support', 'Basic analytics']
            ],
            [
                'name' => 'Gold',
                'min_amount' => 10001,
                'max_amount' => 50000,
                'roi' => $this->expected_return_percentage + 2,
                'benefits' => ['Priority support', 'Advanced analytics', 'Quarterly reports']
            ],
            [
                'name' => 'Platinum',
                'min_amount' => 50001,
                'max_amount' => null,
                'roi' => $this->expected_return_percentage + 4,
                'benefits' => ['24/7 support', 'Premium analytics', 'Monthly reports', 'Dedicated account manager']
            ]
        ];

        return $value ? json_decode($value, true) : $defaultTiers;
    }

    /**
     * Set tier configuration.
     */
    public function setTiersAttribute($value)
    {
        $this->attributes['tiers'] = json_encode($value);
    }

    /**
     * Calculate ROI based on investment amount.
     */
    public function calculateRoi($amount)
    {
        foreach ($this->tiers as $tier) {
            $min = $tier['min_amount'];
            $max = $tier['max_amount'];
            
            if ($max === null) {
                if ($amount >= $min) {
                    return $tier['roi'];
                }
            } else {
                if ($amount >= $min && $amount <= $max) {
                    return $tier['roi'];
                }
            }
        }

        return $this->expected_return_percentage;
    }

    /**
     * Get tier for a specific investment amount.
     */
    public function getTierForAmount($amount)
    {
        foreach ($this->tiers as $tier) {
            $min = $tier['min_amount'];
            $max = $tier['max_amount'];
            
            if ($max === null) {
                if ($amount >= $min) {
                    return $tier;
                }
            } else {
                if ($amount >= $min && $amount <= $max) {
                    return $tier;
                }
            }
        }

        return null;
    }
}
