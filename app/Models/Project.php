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
        'status',
        'fund_goal',
        'current_fund',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'fund_goal' => 'decimal:2',
        'current_fund' => 'decimal:2',
    ];

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function fundAllocations()
    {
        return $this->hasMany(FundAllocation::class);
    }
}
