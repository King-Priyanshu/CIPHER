<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_investment_id', // Optional, if refund is for a specific investment
        'subscription_id', // Optional, if refund is for a subscription
        'amount',
        'reason',
        'status', // pending, approved, rejected, processed
        'admin_note',
        'processed_at',
        'transaction_id', // Link to wallet transaction or payment gateway refund id
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investment()
    {
        return $this->belongsTo(ProjectInvestment::class, 'project_investment_id');
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }
}
