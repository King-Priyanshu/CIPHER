<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Mail\PaymentReceipt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentReceipt implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new PaymentReceipt($payment));
                Log::info('Payment receipt sent', ['user_id' => $user->id, 'payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send payment receipt', ['error' => $e->getMessage()]);
            }
        }
    }
}
