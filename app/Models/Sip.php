<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investment_plan_id',
        'amount',
        'frequency',
        'start_date',
        'duration',
        'auto_pay',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'auto_pay' => 'boolean',
    ];

    const STATUSES = [
        'active' => 'Active',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
        'paused' => 'Paused',
    ];

    const FREQUENCIES = [
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class);
    }

    public function paymentSchedule()
    {
        return $this->hasMany(SipPaymentSchedule::class);
    }

    public function generatePaymentSchedule()
    {
        $this->paymentSchedule()->delete();

        $currentDate = $this->start_date;
        $payments = [];

        for ($i = 0; $i < $this->duration; $i++) {
            $payments[] = [
                'sip_id' => $this->id,
                'payment_date' => $currentDate,
                'amount' => $this->amount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $currentDate = $this->frequency === 'weekly' 
                ? $currentDate->addWeeks(1) 
                : $currentDate->addMonths(1);
        }

        SipPaymentSchedule::insert($payments);

        return $this;
    }

    public function getTotalInvestmentAttribute()
    {
        return $this->amount * $this->duration;
    }

    public function getCompletedPaymentsCountAttribute()
    {
        return $this->paymentSchedule()->where('status', 'paid')->count();
    }

    public function getPendingPaymentsCountAttribute()
    {
        return $this->paymentSchedule()->where('status', 'pending')->count();
    }

    public function getNextPaymentDateAttribute()
    {
        return $this->paymentSchedule()
            ->where('status', 'pending')
            ->orderBy('payment_date')
            ->first()?->payment_date;
    }
}
