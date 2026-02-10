<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'project_investment_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_subscription_id',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'receipt',
        'amount',
        'currency',
        'status',
        'paid_at',
        'failure_reason',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function projectInvestment()
    {
        return $this->belongsTo(ProjectInvestment::class, 'project_investment_id');
    }
}
