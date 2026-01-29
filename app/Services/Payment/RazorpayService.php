<?php

namespace App\Services\Payment;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected string $keyId;
    protected string $keySecret;
    protected string $baseUrl = 'https://api.razorpay.com/v1';

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key');
        $this->keySecret = config('services.razorpay.secret');
    }

    /**
     * Create a subscription plan in Razorpay.
     */
    public function createPlan(SubscriptionPlan $plan): ?string
    {
        $response = $this->request('POST', '/plans', [
            'period' => $this->mapInterval($plan->interval),
            'interval' => 1,
            'item' => [
                'name' => $plan->name,
                'amount' => (int) ($plan->price * 83 * 100), // Convert USD to INR paise
                'currency' => 'INR',
                'description' => $plan->description ?? '',
            ],
        ]);

        if ($response && isset($response['id'])) {
            $plan->update(['razorpay_plan_id' => $response['id']]);
            return $response['id'];
        }

        return null;
    }

    /**
     * Create or get a Razorpay customer for a user.
     */
    public function createCustomer(User $user): ?string
    {
        // Return existing customer ID if present
        if ($user->razorpay_customer_id) {
            return $user->razorpay_customer_id;
        }

        $response = $this->request('POST', '/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'contact' => $user->phone ?? '',
            'notes' => [
                'user_id' => $user->id,
            ],
        ]);

        if ($response && isset($response['id'])) {
            $user->update(['razorpay_customer_id' => $response['id']]);
            return $response['id'];
        }

        return null;
    }

    /**
     * Create a subscription for a user.
     */
    public function createSubscription(User $user, SubscriptionPlan $plan): ?array
    {
        // Ensure plan has Razorpay plan ID
        if (!$plan->razorpay_plan_id) {
            $this->createPlan($plan);
            $plan->refresh();
        }

        if (!$plan->razorpay_plan_id) {
            Log::error('Failed to create Razorpay plan', ['plan_id' => $plan->id]);
            return null;
        }

        // Ensure user has customer ID
        $customerId = $this->createCustomer($user);
        if (!$customerId) {
            Log::error('Failed to create Razorpay customer', ['user_id' => $user->id]);
            return null;
        }

        $response = $this->request('POST', '/subscriptions', [
            'plan_id' => $plan->razorpay_plan_id,
            'customer_id' => $customerId,
            'total_count' => 12, // 12 billing cycles
            'quantity' => 1,
            'customer_notify' => 1,
            'notes' => [
                'user_id' => $user->id,
                'plan_name' => $plan->name,
            ],
        ]);

        if ($response && isset($response['id'])) {
            return [
                'subscription_id' => $response['id'],
                'short_url' => $response['short_url'] ?? null,
                'status' => $response['status'] ?? 'created',
            ];
        }

        Log::error('Failed to create Razorpay subscription', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'response' => $response,
        ]);

        return null;
    }

    /**
     * Cancel a subscription in Razorpay.
     */
    public function cancelSubscription(string $subscriptionId, bool $cancelAtEndOfPeriod = true): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/cancel", [
            'cancel_at_cycle_end' => $cancelAtEndOfPeriod ? 1 : 0,
        ]);

        return $response && isset($response['id']);
    }

    /**
     * Pause a subscription in Razorpay.
     */
    public function pauseSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/pause", [
            'pause_at' => 'now',
        ]);

        return $response && isset($response['id']);
    }

    /**
     * Resume a paused subscription.
     */
    public function resumeSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/resume", [
            'resume_at' => 'now',
        ]);

        return $response && isset($response['id']);
    }

    /**
     * Fetch subscription details from Razorpay.
     */
    public function fetchSubscription(string $subscriptionId): ?array
    {
        return $this->request('GET', "/subscriptions/{$subscriptionId}");
    }

    /**
     * Fetch payment details from Razorpay.
     */
    public function fetchPayment(string $paymentId): ?array
    {
        return $this->request('GET', "/payments/{$paymentId}");
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        
        if (!$webhookSecret) {
            Log::warning('Razorpay webhook secret not configured');
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Map internal subscription status from Razorpay status.
     */
    public function mapStatus(string $razorpayStatus): string
    {
        $statusMap = [
            'created' => 'pending',
            'authenticated' => 'pending',
            'active' => 'active',
            'paused' => 'paused',
            'cancelled' => 'cancelled',
            'completed' => 'expired',
            'expired' => 'expired',
            'halted' => 'suspended',
            'pending' => 'pending',
        ];

        return $statusMap[$razorpayStatus] ?? 'pending';
    }

    /**
     * Map plan interval to Razorpay period.
     */
    protected function mapInterval(string $interval): string
    {
        $intervalMap = [
            'monthly' => 'monthly',
            'yearly' => 'yearly',
            'weekly' => 'weekly',
            'daily' => 'daily',
        ];

        return $intervalMap[$interval] ?? 'monthly';
    }

    /**
     * Make HTTP request to Razorpay API.
     */
    protected function request(string $method, string $endpoint, array $data = []): ?array
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->timeout(30)
                ->$method($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Razorpay API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
