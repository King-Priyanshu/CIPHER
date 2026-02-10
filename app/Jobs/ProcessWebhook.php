<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\WebhookEvent;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Services\Payment\RazorpayService;
use App\Services\WalletService;
use App\Events\PaymentSucceeded;
use App\Mail\SubscriptionActivated;

class ProcessWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $eventId;

    public $tries = 5;

    public function backoff()
    {
        return [60, 300, 3600];
    }

    public function __construct(string $eventId)
    {
        $this->eventId = $eventId;
    }

    public function handle(RazorpayService $razorpayService, WalletService $walletService): void
    {
        $webhookEvent = WebhookEvent::where('event_id', $this->eventId)->first();

        if (!$webhookEvent) {
            Log::error("Webhook Event record not found for Job: {$this->eventId}");
            return;
        }

        if ($webhookEvent->status === 'processed') {
            return;
        }

        $payload = $webhookEvent->payload;
        $event = $webhookEvent->event_type;

        Log::info("Processing Webhook Job: {$event} ({$this->eventId})");

        try {
            DB::transaction(function () use ($event, $payload, $webhookEvent, $walletService) {
                // Determine handler method
                switch ($event) {
                    case 'subscription.activated':
                        $this->handleSubscriptionActivated($payload, $walletService);
                        break;
                    case 'subscription.charged':
                        $this->handleSubscriptionCharged($payload, $walletService);
                        break;
                    case 'subscription.authenticated':
                        $this->handleSubscriptionAuthenticated($payload);
                        break;
                    case 'subscription.paused':
                        $this->handleSubscriptionPaused($payload);
                        break;
                    case 'subscription.resumed':
                        $this->handleSubscriptionResumed($payload);
                        break;
                    case 'subscription.cancelled':
                        $this->handleSubscriptionCancelled($payload);
                        break;
                    case 'subscription.completed':
                        $this->handleSubscriptionCompleted($payload);
                        break;
                    case 'subscription.halted':
                        $this->handleSubscriptionHalted($payload);
                        break;
                    case 'payment.captured':
                        $this->handlePaymentCaptured($payload, $walletService);
                        break;
                    case 'payment.failed':
                        $this->handlePaymentFailed($payload);
                        break;
                    case 'invoice.paid':
                        $this->handleInvoicePaid($payload);
                        break;
                    default:
                        Log::info('Unhandled Razorpay event', ['event' => $event]);
                }

                $webhookEvent->update(['status' => 'processed']);
            });

        } catch (\Exception $e) {
            Log::error('Razorpay webhook job failed', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    // --- Handlers (Migrated from Controller & Refined) ---

    protected function handleSubscriptionActivated(array $payload, WalletService $walletService): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if (!$userSubscription) {
            Log::warning('Subscription not found for activation', ['razorpay_id' => $razorpaySubId]);
            return;
        }

        $userSubscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'current_period_start' => isset($subscription['current_start']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_start']) : now(),
            'current_period_end' => isset($subscription['current_end']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) : now()->addMonth(),
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.activated',
            'description' => 'Subscription activated via Razorpay webhook.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);

        try {
            Mail::to($userSubscription->user->email)->queue(new SubscriptionActivated($userSubscription));
        } catch (\Exception $e) {
            Log::error('Failed to send activation email', ['error' => $e->getMessage()]);
        }
    }

    protected function handleSubscriptionCharged(array $payload, WalletService $walletService): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $payment = $payload['payload']['payment']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if (!$userSubscription) {
            return;
        }

        $paymentRecord = Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'razorpay',
            'gateway_transaction_id' => $payment['id'] ?? null,
            'razorpay_payment_id' => $payment['id'] ?? null,
            'razorpay_subscription_id' => $razorpaySubId,
            'amount' => ($payment['amount'] ?? 0) / 100,
            'currency' => $payment['currency'] ?? 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        PaymentSucceeded::dispatch($paymentRecord);

        // Credit Wallet (Ledger Integration)
        $walletService->credit(
            $userSubscription->user,
            $paymentRecord->amount,
            'subscription_payment',
            "Subscription Payment: {$payment['id']}",
            $paymentRecord
        );

        $userSubscription->update([
            'status' => 'active',
            'retry_count' => 0,
            'grace_until' => null,
            'current_period_start' => isset($subscription['current_start']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_start']) : now(),
            'current_period_end' => isset($subscription['current_end']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) : now()->addMonth(),
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.charged',
            'description' => 'Recurring payment successful.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    protected function handlePaymentCaptured(array $payload, WalletService $walletService): void
    {
        $payment = $payload['payload']['payment']['entity'];
        $paymentId = $payment['id'];
        $orderId = $payment['order_id'] ?? null;
        $amount = ($payment['amount'] ?? 0) / 100;

        // Check if payment already recorded
        if (Payment::where('gateway_payment_id', $paymentId)->exists()) {
            return;
        }

        $userSubscription = UserSubscription::where('razorpay_order_id', $orderId)->first();

        if (!$userSubscription) {
            // Might be a direct payment not linked to sub?
            return;
        }

        if ($userSubscription->status === 'active') {
             return;
        }

        $userSubscription->update([
            'status' => 'active',
            'razorpay_payment_id' => $paymentId,
            'starts_at' => now(),
            'ends_at' => now()->addMonths($userSubscription->plan->duration_in_months ?? 1),
            'allocated_amount' => $amount,
        ]);

        $paymentRecord = Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'razorpay',
            'gateway_transaction_id' => $paymentId,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        $walletService->credit(
            $userSubscription->user,
            $amount,
            'deposit',
            "Subscription Payment: {$paymentId}",
            $paymentRecord
        );
    }
    
    // ... Implement other methods similarly (paused, resumed, cancelled, etc.) ...
    protected function handleSubscriptionAuthenticated(array $payload) {}
    protected function handleSubscriptionPaused(array $payload) {}
    protected function handleSubscriptionResumed(array $payload) {}
    protected function handleSubscriptionCancelled(array $payload) {}
    protected function handleSubscriptionCompleted(array $payload) {}
    protected function handleSubscriptionHalted(array $payload) {}
    protected function handlePaymentFailed(array $payload) {}
    protected function handleInvoicePaid(array $payload) {}
}
