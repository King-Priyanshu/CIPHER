<?php

namespace App\Services\Payments;

use App\Services\Payment\RazorpayService;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    protected $razorpay;

    public function __construct(RazorpayService $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    /**
     * Charge the user a specific amount.
     */
    public function charge($user, float $amount, string $currency, string $source): string
    {
        // $source here would be the razorpay_payment_id if using direct charge
        // For Razorpay, we usually create an order first. 
        // This is a simplified bridge.
        $order = $this->razorpay->createOrder((int) $amount, $currency);
        return $order['id'] ?? 'failed';
    }

    /**
     * Subscribe the user to a recurring plan.
     */
    public function subscribe($user, $plan): string
    {
        $subscription = $this->razorpay->createSubscription($user, $plan);
        return $subscription['subscription_id'] ?? 'failed';
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        return $this->razorpay->cancelSubscription($subscriptionId);
    }
}
