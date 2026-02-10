<?php

namespace App\Services\Payments;

use App\Services\Payment\StripeService;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

class StripePaymentGateway implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Charge the user a specific amount.
     *
     * @param  \App\Models\User  $user
     * @param  float  $amount
     * @param  string  $currency
     * @param  string  $source  payment method token
     * @return string  transaction_id
     */
    public function charge($user, float $amount, string $currency, string $source): string
    {
        $paymentIntent = $this->stripe->createPaymentIntent(
            (int) $amount,
            $currency,
            [
                'user_id' => $user->id,
            ]
        );

        return $paymentIntent['id'] ?? 'failed';
    }

    /**
     * Subscribe the user to a recurring plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionPlan  $plan
     * @return string  subscription_id
     */
    public function subscribe($user, $plan): string
    {
        // For Stripe, we need a payment method. This is a simplified version.
        // In a real implementation, you would collect payment method first.
        $subscription = $this->stripe->createSubscription($user, $plan, 'pm_card_visa');
        return $subscription['subscription_id'] ?? 'failed';
    }

    /**
     * Cancel a subscription.
     *
     * @param  string  $subscriptionId
     * @return bool
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        return $this->stripe->cancelSubscription($subscriptionId);
    }

    /**
     * Get the payment gateway name.
     */
    public function getGatewayName(): string
    {
        return 'stripe';
    }
}
