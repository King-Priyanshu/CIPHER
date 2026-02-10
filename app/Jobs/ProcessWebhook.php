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
use App\Models\ProjectInvestment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Services\Payment\RazorpayService;
use App\Services\Payment\StripeService;
use App\Services\WalletService;
use App\Events\PaymentSucceeded;
use App\Mail\SubscriptionActivated;
use App\Notifications\SubscriptionSuspended;
use App\Notifications\SubscriptionExpired;
use App\Notifications\PaymentFailedNotification;

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

    public function handle(RazorpayService $razorpayService, StripeService $stripeService, WalletService $walletService): void
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
            DB::transaction(function () use ($event, $payload, $webhookEvent, $walletService, $razorpayService, $stripeService) {
                // Determine handler based on gateway
                if ($webhookEvent->gateway === 'razorpay') {
                    $this->handleRazorpayEvent($event, $payload, $walletService);
                } elseif ($webhookEvent->gateway === 'stripe') {
                    $this->handleStripeEvent($event, $payload, $walletService);
                } else {
                    Log::info('Unhandled payment gateway', ['gateway' => $webhookEvent->gateway]);
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
        if (Payment::where('gateway_transaction_id', $paymentId)->exists()) {
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
            'ends_at' => $this->calculateSubscriptionEndDate($userSubscription->plan->interval),
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

    /**
     * Subscription authenticated - user has authorized the mandate.
     */
    protected function handleSubscriptionAuthenticated(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        // Subscription is authenticated but not yet charged
        if ($userSubscription->status === 'pending') {
            $userSubscription->update(['status' => 'authenticated']);
        }

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.authenticated',
            'description' => 'Subscription mandate authenticated by user.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Subscription paused by merchant or user.
     */
    protected function handleSubscriptionPaused(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        $userSubscription->update([
            'status' => 'paused',
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.paused',
            'description' => 'Subscription paused.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Subscription resumed after being paused.
     */
    protected function handleSubscriptionResumed(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        $userSubscription->update([
            'status' => 'active',
            'current_period_start' => isset($subscription['current_start']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_start']) : now(),
            'current_period_end' => isset($subscription['current_end']) ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) : now()->addMonth(),
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.resumed',
            'description' => 'Subscription resumed.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Subscription cancelled by user or merchant.
     */
    protected function handleSubscriptionCancelled(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        $endedAt = isset($subscription['ended_at'])
            ? \Carbon\Carbon::createFromTimestamp($subscription['ended_at'])
            : now();

        $userSubscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => $endedAt,
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.cancelled',
            'description' => 'Subscription cancelled.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Subscription completed its total billing cycles.
     */
    protected function handleSubscriptionCompleted(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        $userSubscription->update([
            'status' => 'expired',
            'ends_at' => now(),
        ]);

        $userSubscription->user->notify(new SubscriptionExpired($userSubscription));

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.completed',
            'description' => 'Subscription completed all billing cycles.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Subscription halted due to repeated payment failures.
     */
    protected function handleSubscriptionHalted(array $payload): void
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();
        if (!$userSubscription)
            return;

        $userSubscription->update([
            'status' => 'suspended',
            'grace_until' => null,
        ]);

        $userSubscription->user->notify(new SubscriptionSuspended($userSubscription));

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.halted',
            'description' => 'Subscription suspended due to repeated payment failures.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Payment attempt failed.
     */
    protected function handlePaymentFailed(array $payload): void
    {
        $payment = $payload['payload']['payment']['entity'];
        $subscriptionId = $payment['subscription_id'] ?? null;

        if (!$subscriptionId) {
            Log::warning('Payment failed but no subscription_id found', ['payment_id' => $payment['id']]);
            return;
        }

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$userSubscription)
            return;

        $retryCount = ($userSubscription->retry_count ?? 0) + 1;
        $gracePeriodDays = 7;
        $maxRetries = 3;

        if ($retryCount >= $maxRetries) {
            $userSubscription->update([
                'status' => 'suspended',
                'retry_count' => $retryCount,
                'grace_until' => null,
            ]);
            $userSubscription->user->notify(new SubscriptionSuspended($userSubscription));
        } else {
            $graceUntil = $userSubscription->grace_until ?? now()->addDays($gracePeriodDays);
            $userSubscription->update([
                'status' => 'past_due',
                'retry_count' => $retryCount,
                'grace_until' => $graceUntil,
            ]);
            $userSubscription->user->notify(new PaymentFailedNotification($userSubscription, $retryCount));
        }

        Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'razorpay',
            'gateway_transaction_id' => $payment['id'] ?? null,
            'amount' => ($payment['amount'] ?? 0) / 100,
            'currency' => $payment['currency'] ?? 'INR',
            'status' => 'failed',
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'payment.failed',
            'description' => "Payment failed (attempt {$retryCount}/{$maxRetries}).",
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Invoice paid successfully.
     */
    protected function handleInvoicePaid(array $payload): void
    {
        $invoiceEntity = $payload['payload']['invoice']['entity'] ?? null;
        if (!$invoiceEntity)
            return;

        $subscriptionId = $invoiceEntity['subscription_id'] ?? null;
        if (!$subscriptionId)
            return;

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$userSubscription)
            return;

        Invoice::updateOrCreate(
            ['gateway_invoice_id' => $invoiceEntity['id']],
            [
                'user_id' => $userSubscription->user_id,
                'subscription_id' => $userSubscription->id,
                'amount' => ($invoiceEntity['amount'] ?? 0) / 100,
                'currency' => $invoiceEntity['currency'] ?? 'INR',
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'invoice.paid',
            'description' => 'Invoice paid.',
            'user_agent' => 'Razorpay Webhook Job',
        ]);
    }

    /**
     * Handle Razorpay events.
     */
    protected function handleRazorpayEvent(string $event, array $payload, WalletService $walletService): void
    {
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
    }

    /**
     * Handle Stripe events.
     */
    protected function handleStripeEvent(string $event, array $payload, WalletService $walletService): void
    {
        switch ($event) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleStripeSubscriptionUpdated($payload, $walletService);
                break;
            case 'invoice.payment_succeeded':
                $this->handleStripeInvoicePaid($payload, $walletService);
                break;
            case 'payment_intent.succeeded':
                $this->handleStripePaymentSucceeded($payload, $walletService);
                break;
            case 'payment_intent.payment_failed':
                $this->handleStripePaymentFailed($payload);
                break;
            default:
                Log::info('Unhandled Stripe event', ['event' => $event]);
        }
    }

    /**
     * Handle Stripe subscription updated event.
     */
    protected function handleStripeSubscriptionUpdated(array $payload, WalletService $walletService): void
    {
        $subscription = $payload['data']['object'];
        $stripeSubId = $subscription['id'];

        $userSubscription = UserSubscription::where('stripe_subscription_id', $stripeSubId)->first();

        if (!$userSubscription) {
            Log::warning('Subscription not found for Stripe subscription', ['stripe_id' => $stripeSubId]);
            return;
        }

        $status = $subscription['status'];
        $internalStatus = $this->mapStripeSubscriptionStatus($status);

        $updateData = [
            'status' => $internalStatus,
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ];

        // If subscription is active, update dates
        if ($internalStatus === 'active') {
            $updateData['starts_at'] = now();
            $updateData['ends_at'] = $this->calculateSubscriptionEndDate($userSubscription->plan->interval);
        }

        $userSubscription->update($updateData);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.updated',
            'description' => "Subscription status updated to {$internalStatus} via Stripe webhook.",
            'user_agent' => 'Stripe Webhook Job',
        ]);
    }

    /**
     * Handle Stripe invoice paid event.
     */
    protected function handleStripeInvoicePaid(array $payload, WalletService $walletService): void
    {
        $invoice = $payload['data']['object'];
        $subscriptionId = $invoice['subscription'];
        $paymentIntentId = $invoice['payment_intent'];
        $amount = $invoice['amount_paid'] / 100;

        $userSubscription = UserSubscription::where('stripe_subscription_id', $subscriptionId)->first();

        if (!$userSubscription) {
            Log::warning('Subscription not found for Stripe invoice', ['subscription_id' => $subscriptionId]);
            return;
        }

        $paymentRecord = Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'stripe',
            'gateway_transaction_id' => $paymentIntentId,
            'stripe_payment_intent_id' => $paymentIntentId,
            'stripe_subscription_id' => $subscriptionId,
            'amount' => $amount,
            'currency' => $invoice['currency'] ?? 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        PaymentSucceeded::dispatch($paymentRecord);

        $walletService->credit(
            $userSubscription->user,
            $paymentRecord->amount,
            'subscription_payment',
            "Subscription Payment: {$paymentIntentId}",
            $paymentRecord
        );

        $userSubscription->update([
            'status' => 'active',
            'retry_count' => 0,
            'grace_until' => null,
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.charged',
            'description' => 'Recurring payment successful via Stripe.',
            'user_agent' => 'Stripe Webhook Job',
        ]);
    }

    /**
     * Calculate subscription end date based on interval.
     */
    protected function calculateSubscriptionEndDate(string $interval): \Carbon\Carbon
    {
        switch ($interval) {
            case 'quarterly':
                return now()->addMonths(3);
            case 'annual':
                return now()->addMonths(12);
            case 'monthly':
            default:
                return now()->addMonth();
        }
    }

    /**
     * Handle Stripe payment succeeded event.
     */
    protected function handleStripePaymentSucceeded(array $payload, WalletService $walletService): void
    {
        $paymentIntent = $payload['data']['object'];
        $paymentId = $paymentIntent['id'];
        $amount = $paymentIntent['amount'] / 100;
        $metadata = $paymentIntent['metadata'] ?? [];

        if (Payment::where('gateway_transaction_id', $paymentId)->exists()) {
            return;
        }

        $userSubscription = UserSubscription::where('stripe_payment_intent_id', $paymentId)->first();
        $investment = ProjectInvestment::where('stripe_payment_intent_id', $paymentId)->first();

        // Fallback: check metadata for order_id
        if (!$userSubscription && !$investment && isset($metadata['order_id'])) {
            $userSubscription = UserSubscription::where('stripe_payment_intent_id', $metadata['order_id'])->first();
            $investment = ProjectInvestment::where('stripe_payment_intent_id', $metadata['order_id'])->first();
        }

        if ($userSubscription && $userSubscription->status === 'active') {
            return;
        }

        if ($userSubscription) {
            $userSubscription->update([
                'status' => 'active',
                'stripe_payment_intent_id' => $paymentId,
                'starts_at' => now(),
                'ends_at' => $this->calculateSubscriptionEndDate($userSubscription->plan->interval),
                'allocated_amount' => $amount,
            ]);
        }

        if ($investment) {
            $investment->update(['stripe_payment_intent_id' => $paymentId]);
            app(\App\Services\InvestmentService::class)->finalizeInvestment($investment);
        }

        $paymentRecord = Payment::create([
            'user_id' => $userSubscription ? $userSubscription->user_id : $investment->user_id,
            'subscription_id' => $userSubscription?->id,
            'project_investment_id' => $investment?->id,
            'gateway' => 'stripe',
            'gateway_transaction_id' => $paymentId,
            'amount' => $amount,
            'currency' => $paymentIntent['currency'] ?? 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        $user = $userSubscription ? $userSubscription->user : $investment->user;
        $walletService->credit(
            $user,
            $amount,
            $userSubscription ? 'subscription_payment' : 'deposit',
            "Payment via Stripe: {$paymentId}",
            $paymentRecord
        );
    }

    /**
     * Handle Stripe payment failed event.
     */
    protected function handleStripePaymentFailed(array $payload): void
    {
        $paymentIntent = $payload['data']['object'];
        $paymentId = $paymentIntent['id'];
        $metadata = $paymentIntent['metadata'] ?? [];

        $userSubscription = UserSubscription::where('stripe_payment_intent_id', $paymentId)->first();
        $investment = ProjectInvestment::where('stripe_payment_intent_id', $paymentId)->first();

        // Fallback: check metadata
        if (!$userSubscription && !$investment && isset($metadata['order_id'])) {
            $userSubscription = UserSubscription::where('stripe_payment_intent_id', $metadata['order_id'])->first();
            $investment = ProjectInvestment::where('stripe_payment_intent_id', $metadata['order_id'])->first();
        }

        Payment::create([
            'user_id' => $userSubscription ? $userSubscription->user_id : $investment->user_id,
            'subscription_id' => $userSubscription?->id,
            'project_investment_id' => $investment?->id,
            'gateway' => 'stripe',
            'gateway_transaction_id' => $paymentId,
            'amount' => $paymentIntent['amount'] / 100,
            'currency' => $paymentIntent['currency'] ?? 'INR',
            'status' => 'failed',
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription ? $userSubscription->user_id : $investment->user_id,
            'action' => 'payment.failed',
            'description' => 'Payment failed via Stripe.',
            'user_agent' => 'Stripe Webhook Job',
        ]);
    }

    /**
     * Map Stripe subscription status to internal status.
     */
    protected function mapStripeSubscriptionStatus(string $status): string
    {
        $statusMap = [
            'trialing' => 'pending',
            'active' => 'active',
            'past_due' => 'pending',
            'canceled' => 'cancelled',
            'unpaid' => 'failed',
        ];

        return $statusMap[$status] ?? 'pending';
    }
}
