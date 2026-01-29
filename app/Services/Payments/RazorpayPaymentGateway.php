<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserSubscription;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Create a new subscription for a user.
     */
    public function createSubscription(User $user, string $planId, array $options = []): UserSubscription
    {
        // TODO: Implement Razorpay subscription creation
        throw new \RuntimeException('Razorpay subscription creation not implemented');
    }

    /**
     * Cancel an existing subscription.
     */
    public function cancelSubscription(UserSubscription $subscription): bool
    {
        // TODO: Implement Razorpay subscription cancellation
        throw new \RuntimeException('Razorpay subscription cancellation not implemented');
    }

    /**
     * Process a one-time payment.
     */
    public function processPayment(User $user, int $amount, string $currency = 'INR', array $options = []): Payment
    {
        // TODO: Implement Razorpay payment processing
        throw new \RuntimeException('Razorpay payment processing not implemented');
    }

    /**
     * Handle incoming webhook from Razorpay.
     */
    public function handleWebhook(array $payload): void
    {
        // TODO: Implement Razorpay webhook handling
        // Handle events like:
        // - subscription.activated
        // - subscription.charged
        // - subscription.cancelled
        // - payment.captured
        // - payment.failed
    }

    /**
     * Get the payment gateway name.
     */
    public function getGatewayName(): string
    {
        return 'razorpay';
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        // TODO: Implement Razorpay webhook signature verification
        return false;
    }
}
