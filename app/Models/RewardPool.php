<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_amount',
        'distributed_amount',
        'distribution_date',
        'status',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'total_amount' => 'decimal:2',
        'distributed_amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }
}
