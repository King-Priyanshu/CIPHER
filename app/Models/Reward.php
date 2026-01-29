<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_pool_id',
        'amount',
        'status',
        'distributed_at',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rewardPool()
    {
        return $this->belongsTo(RewardPool::class);
    }
}
