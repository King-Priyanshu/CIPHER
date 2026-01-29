<?php

namespace App\Listeners\Payments;

use App\Events\Payments\PaymentSucceeded;
use App\Notifications\PaymentSucceededNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePaymentStatus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => $payment->user_id,
            'action' => 'payment_succeeded',
            'description' => "Payment of {$payment->amount} {$payment->currency} succeeded.",
        ]);

        // Notify user
        $payment->user->notify(new PaymentSucceededNotification($payment));
    }
}
