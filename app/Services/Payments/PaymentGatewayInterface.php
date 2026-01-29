<?php

namespace App\Services\Payments;

interface PaymentGatewayInterface
{
    /**
     * Charge the user a specific amount.
     *
     * @param  \App\Models\User  $user
     * @param  float  $amount
     * @param  string  $currency
     * @param  string  $source  payment method token
     * @return string  transaction_id
     */
    public function charge($user, float $amount, string $currency, string $source): string;

    /**
     * Subscribe the user to a recurring plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionPlan  $plan
     * @return string  subscription_id
     */
    public function subscribe($user, $plan): string;

    /**
     * Cancel a subscription.
     *
     * @param  string  $subscriptionId
     * @return bool
     */
    public function cancelSubscription(string $subscriptionId): bool;
}
