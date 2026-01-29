<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fund_pool_id',
        'project_id',
        'amount',
        'allocated_at',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function fundPool()
    {
        return $this->belongsTo(FundPool::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
