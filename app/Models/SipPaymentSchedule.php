<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipPaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'sip_id',
        'payment_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ];

    public function sip()
    {
        return $this->belongsTo(Sip::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'sip_payment_schedule_id');
    }
}
