<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_amount',
        'allocated_amount',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
    ];

    public function allocations()
    {
        return $this->hasMany(FundAllocation::class);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->allocated_amount;
    }
}
