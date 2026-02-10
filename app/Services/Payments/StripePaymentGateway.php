<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserSubscription;

class StripePaymentGateway implements PaymentGatewayInterface
{
    /**
     * Create a new subscription for a user.
     */
    public function createSubscription(User $user, string $planId, array $options = []): UserSubscription
    {
        // TODO: Implement Stripe subscription creation
        // 1. Create or retrieve Stripe customer
        // 2. Create Stripe subscription
        // 3. Store subscription details locally
        throw new \RuntimeException('Stripe subscription creation not implemented');
    }

    /**
     * Cancel an existing subscription.
     */
    public function cancelSubscription(UserSubscription $subscription): bool
    {
        // TODO: Implement Stripe subscription cancellation
        throw new \RuntimeException('Stripe subscription cancellation not implemented');
    }

    /**
     * Process a one-time payment.
     */
    public function processPayment(User $user, int $amount, string $currency = 'INR', array $options = []): Payment
    {
        // TODO: Implement Stripe payment processing
        throw new \RuntimeException('Stripe payment processing not implemented');
    }

    /**
     * Handle incoming webhook from Stripe.
     */
    public function handleWebhook(array $payload): void
    {
        // TODO: Implement Stripe webhook handling
        // Handle events like:
        // - invoice.payment_succeeded
        // - invoice.payment_failed
        // - customer.subscription.updated
        // - customer.subscription.deleted
    }

    /**
     * Get the payment gateway name.
     */
    public function getGatewayName(): string
    {
        return 'stripe';
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        // TODO: Implement Stripe webhook signature verification
        return false;
    }
}
