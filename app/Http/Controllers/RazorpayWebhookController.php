<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RazorpayWebhookController extends Controller
{
    protected RazorpayService $razorpayService;

    // Track processed events for idempotency
    protected static array $processedEvents = [];

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Handle incoming Razorpay webhook.
     */
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        // Verify webhook signature
        if (!$this->razorpayService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Razorpay webhook signature verification failed');
            return response('Invalid signature', 401);
        }

        $event = $request->input('event');
        $eventId = $request->input('account_id', '') . '_' . $request->input('payload.subscription.entity.id', time());

        // Idempotency check
        if ($this->isEventProcessed($eventId)) {
            Log::info('Razorpay webhook event already processed', ['event_id' => $eventId]);
            return response('OK', 200);
        }

        Log::info('Razorpay webhook received', [
            'event' => $event,
            'event_id' => $eventId,
        ]);

        try {
            match ($event) {
                'subscription.activated' => $this->handleSubscriptionActivated($request),
                'subscription.charged' => $this->handleSubscriptionCharged($request),
                'subscription.authenticated' => $this->handleSubscriptionAuthenticated($request),
                'subscription.paused' => $this->handleSubscriptionPaused($request),
                'subscription.resumed' => $this->handleSubscriptionResumed($request),
                'subscription.cancelled' => $this->handleSubscriptionCancelled($request),
                'subscription.completed' => $this->handleSubscriptionCompleted($request),
                'subscription.halted' => $this->handleSubscriptionHalted($request),
                'payment.captured' => $this->handlePaymentCaptured($request),
                'payment.failed' => $this->handlePaymentFailed($request),
                'invoice.paid' => $this->handleInvoicePaid($request),
                default => Log::info('Unhandled Razorpay event', ['event' => $event]),
            };

            $this->markEventProcessed($eventId);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Razorpay webhook processing error', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle subscription.activated event.
     */
    protected function handleSubscriptionActivated(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if (!$userSubscription) {
            Log::warning('Subscription not found for activation', ['razorpay_id' => $razorpaySubId]);
            return;
        }

        $userSubscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'current_period_start' => isset($subscription['current_start']) 
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_start']) 
                : now(),
            'current_period_end' => isset($subscription['current_end']) 
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) 
                : now()->addMonth(),
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.activated',
            'description' => 'Subscription activated via Razorpay webhook.',
            'user_agent' => 'Razorpay Webhook',
        ]);

        Log::info('Subscription activated', ['subscription_id' => $userSubscription->id]);
    }

    /**
     * Handle subscription.charged event (recurring payment).
     */
    protected function handleSubscriptionCharged(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $payment = $request->input('payload.payment.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if (!$userSubscription) {
            Log::warning('Subscription not found for charge', ['razorpay_id' => $razorpaySubId]);
            return;
        }

        // Record the payment
        Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'razorpay',
            'gateway_transaction_id' => $payment['id'] ?? null,
            'razorpay_payment_id' => $payment['id'] ?? null,
            'razorpay_subscription_id' => $razorpaySubId,
            'amount' => ($payment['amount'] ?? 0) / 100, // Convert paise to rupees
            'currency' => $payment['currency'] ?? 'INR',
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        // Extend subscription period
        $userSubscription->update([
            'status' => 'active',
            'retry_count' => 0,
            'grace_until' => null,
            'current_period_start' => isset($subscription['current_start']) 
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_start']) 
                : now(),
            'current_period_end' => isset($subscription['current_end']) 
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) 
                : now()->addMonth(),
            'ends_at' => isset($subscription['current_end']) 
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_end']) 
                : null,
        ]);

        ActivityLog::create([
            'user_id' => $userSubscription->user_id,
            'action' => 'subscription.charged',
            'description' => 'Recurring payment successful.',
            'user_agent' => 'Razorpay Webhook',
        ]);

        Log::info('Subscription charged', ['subscription_id' => $userSubscription->id]);
    }

    /**
     * Handle subscription.authenticated event.
     */
    protected function handleSubscriptionAuthenticated(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update(['status' => 'pending']);
        }
    }

    /**
     * Handle subscription.paused event.
     */
    protected function handleSubscriptionPaused(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update(['status' => 'paused']);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.paused',
                'description' => 'Subscription paused.',
                'user_agent' => 'Razorpay Webhook',
            ]);
        }
    }

    /**
     * Handle subscription.resumed event.
     */
    protected function handleSubscriptionResumed(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update(['status' => 'active']);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.resumed',
                'description' => 'Subscription resumed.',
                'user_agent' => 'Razorpay Webhook',
            ]);
        }
    }

    /**
     * Handle subscription.cancelled event.
     */
    protected function handleSubscriptionCancelled(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.cancelled',
                'description' => 'Subscription cancelled.',
                'user_agent' => 'Razorpay Webhook',
            ]);
        }
    }

    /**
     * Handle subscription.completed event.
     */
    protected function handleSubscriptionCompleted(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update(['status' => 'expired']);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.completed',
                'description' => 'Subscription completed all billing cycles.',
                'user_agent' => 'Razorpay Webhook',
            ]);
        }
    }

    /**
     * Handle subscription.halted event (payment failures exceeded).
     */
    protected function handleSubscriptionHalted(Request $request): void
    {
        $subscription = $request->input('payload.subscription.entity');
        $razorpaySubId = $subscription['id'];

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if ($userSubscription) {
            $userSubscription->update(['status' => 'suspended']);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.halted',
                'description' => 'Subscription halted due to payment failures.',
                'user_agent' => 'Razorpay Webhook',
            ]);
        }
    }

    /**
     * Handle payment.captured event.
     */
    protected function handlePaymentCaptured(Request $request): void
    {
        $payment = $request->input('payload.payment.entity');
        $paymentId = $payment['id'];

        // Check if payment already recorded
        $existingPayment = Payment::where('razorpay_payment_id', $paymentId)->first();
        if ($existingPayment) {
            $existingPayment->update(['status' => 'succeeded', 'paid_at' => now()]);
            return;
        }

        // This might be a one-time payment or initial subscription payment
        Log::info('Payment captured', ['payment_id' => $paymentId]);
    }

    /**
     * Handle payment.failed event.
     */
    protected function handlePaymentFailed(Request $request): void
    {
        $payment = $request->input('payload.payment.entity');
        $subscriptionId = $payment['subscription_id'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();

        if (!$userSubscription) {
            return;
        }

        $retryCount = $userSubscription->retry_count + 1;
        $maxRetries = 3;

        if ($retryCount >= $maxRetries) {
            // Max retries exceeded - suspend
            $userSubscription->update([
                'status' => 'suspended',
                'retry_count' => $retryCount,
            ]);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'subscription.suspended',
                'description' => "Subscription suspended after {$maxRetries} failed payment attempts.",
                'user_agent' => 'Razorpay Webhook',
            ]);
        } else {
            // Set grace period on first failure
            $graceDays = 7;
            $graceUntil = $userSubscription->grace_until ?? now()->addDays($graceDays);

            $userSubscription->update([
                'status' => 'past_due',
                'retry_count' => $retryCount,
                'grace_until' => $graceUntil,
            ]);

            ActivityLog::create([
                'user_id' => $userSubscription->user_id,
                'action' => 'payment.failed',
                'description' => "Payment failed (attempt {$retryCount}/{$maxRetries}). Grace period until " . $graceUntil->format('M d, Y'),
                'user_agent' => 'Razorpay Webhook',
            ]);
        }

        // Record the failed payment
        Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->id,
            'gateway' => 'razorpay',
            'gateway_transaction_id' => $payment['id'],
            'razorpay_payment_id' => $payment['id'],
            'razorpay_subscription_id' => $subscriptionId,
            'amount' => ($payment['amount'] ?? 0) / 100,
            'currency' => $payment['currency'] ?? 'INR',
            'status' => 'failed',
        ]);
    }

    /**
     * Handle invoice.paid event.
     */
    protected function handleInvoicePaid(Request $request): void
    {
        $invoice = $request->input('payload.invoice.entity');
        $razorpayInvoiceId = $invoice['id'];
        $subscriptionId = $invoice['subscription_id'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();

        if (!$userSubscription) {
            return;
        }

        // Create invoice record
        Invoice::create([
            'razorpay_invoice_id' => $razorpayInvoiceId,
            'user_id' => $userSubscription->user_id,
            'payment_id' => null, // Will be linked when payment record found
            'invoice_number' => 'INV-' . strtoupper(substr($razorpayInvoiceId, -8)),
            'amount' => ($invoice['amount'] ?? 0) / 100,
            'tax' => ($invoice['tax_amount'] ?? 0) / 100,
            'total' => ($invoice['amount'] ?? 0) / 100,
            'status' => 'paid',
            'issued_at' => now(),
            'paid_at' => now(),
        ]);

        Log::info('Invoice created', ['invoice_id' => $razorpayInvoiceId]);
    }

    /**
     * Check if event was already processed (idempotency).
     */
    protected function isEventProcessed(string $eventId): bool
    {
        // In production, use Redis or database for persistence
        return in_array($eventId, self::$processedEvents);
    }

    /**
     * Mark event as processed.
     */
    protected function markEventProcessed(string $eventId): void
    {
        self::$processedEvents[] = $eventId;
    }
}
