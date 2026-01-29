<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhooks.
     */
    public function handleStripe(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        // Verify webhook signature
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle specific event types
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'invoice.paid':
                $this->handleInvoicePaid($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionCancelled($event->data->object);
                break;

            default:
                Log::info('Stripe Webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle checkout session completion.
     */
    protected function handleCheckoutCompleted($session)
    {
        Log::info('Stripe Webhook: Checkout completed', ['session_id' => $session->id]);

        // Find or create user subscription based on metadata
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        if (!$userId || !$planId) {
            Log::warning('Stripe Webhook: Missing metadata in checkout session');
            return;
        }

        // Activate subscription
        $subscription = UserSubscription::where('user_id', $userId)
            ->where('subscription_plan_id', $planId)
            ->where('status', 'pending')
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'stripe_subscription_id' => $session->subscription,
                'starts_at' => now(),
            ]);

            Log::info('Stripe Webhook: Subscription activated', ['subscription_id' => $subscription->id]);
        }
    }

    /**
     * Handle successful invoice payment.
     */
    protected function handleInvoicePaid($invoice)
    {
        Log::info('Stripe Webhook: Invoice paid', ['invoice_id' => $invoice->id]);

        // Record payment
        $stripeCustomerId = $invoice->customer;
        $user = User::where('stripe_customer_id', $stripeCustomerId)->first();

        if ($user) {
            Payment::create([
                'user_id' => $user->id,
                'amount' => $invoice->amount_paid / 100, // Convert from cents
                'currency' => strtoupper($invoice->currency),
                'status' => 'succeeded',
                'payment_method' => 'stripe',
                'transaction_id' => $invoice->payment_intent,
                'metadata' => json_encode([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                ]),
            ]);
        }
    }

    /**
     * Handle failed payment.
     */
    protected function handlePaymentFailed($invoice)
    {
        Log::warning('Stripe Webhook: Payment failed', ['invoice_id' => $invoice->id]);

        $stripeCustomerId = $invoice->customer;
        $user = User::where('stripe_customer_id', $stripeCustomerId)->first();

        if ($user) {
            $subscription = $user->subscription;
            
            if ($subscription) {
                $retryCount = ($subscription->retry_count ?? 0) + 1;
                $maxRetries = 3;
                $gracePeriodDays = 7;

                if ($retryCount >= $maxRetries) {
                    // Max retries reached, suspend the subscription
                    $subscription->update([
                        'status' => 'suspended',
                        'retry_count' => $retryCount,
                        'grace_until' => null,
                    ]);

                    Log::info('Stripe Webhook: Subscription suspended after max retries', [
                        'user_id' => $user->id,
                        'retry_count' => $retryCount,
                    ]);

                    // TODO: Send subscription suspended notification
                } else {
                    // Set or maintain grace period
                    $graceUntil = $subscription->grace_until ?? now()->addDays($gracePeriodDays);
                    
                    $subscription->update([
                        'status' => 'past_due',
                        'retry_count' => $retryCount,
                        'grace_until' => $graceUntil,
                    ]);

                    Log::info('Stripe Webhook: Subscription marked past_due', [
                        'user_id' => $user->id,
                        'retry_count' => $retryCount,
                        'grace_until' => $graceUntil->toDateString(),
                    ]);

                    // TODO: Send payment failed notification with retry count
                }
            }
        }
    }

    /**
     * Handle subscription updates.
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        Log::info('Stripe Webhook: Subscription updated', ['subscription_id' => $stripeSubscription->id]);

        $subscription = UserSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => $this->mapStripeStatus($stripeSubscription->status),
                'ends_at' => $stripeSubscription->current_period_end 
                    ? now()->setTimestamp($stripeSubscription->current_period_end) 
                    : null,
            ]);
        }
    }

    /**
     * Handle subscription cancellation.
     */
    protected function handleSubscriptionCancelled($stripeSubscription)
    {
        Log::info('Stripe Webhook: Subscription cancelled', ['subscription_id' => $stripeSubscription->id]);

        $subscription = UserSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now(),
            ]);
        }
    }

    /**
     * Map Stripe subscription status to internal status.
     */
    protected function mapStripeStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'cancelled',
            'unpaid' => 'suspended',
            'trialing' => 'trialing',
            default => 'inactive',
        };
    }
}
