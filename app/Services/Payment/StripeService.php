<?php

namespace App\Services\Payment;

use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected ?string $secretKey = null;
    protected string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        // Don't load in constructor to avoid DB errors during bootstrapping/migrations
    }

    protected function ensureCredentialsLoaded(): void
    {
        if ($this->secretKey !== null) {
            return;
        }

        try {
            $this->secretKey = trim(Setting::get('stripe.secret') ?? config('services.stripe.secret') ?? '');
        } catch (\Exception $e) {
            // Fallback for migrations
            $this->secretKey = config('services.stripe.secret') ?? '';
        }
    }

    /**
     * Create a payment intent in Stripe.
     */
    public function createPaymentIntent(int $amount, string $currency = 'INR', array $metadata = []): ?array
    {
        $response = $this->request('POST', '/payment_intents', [
            'amount' => $amount * 100, // Convert to cents
            'currency' => strtolower($currency),
            'metadata' => $metadata,
        ]);

        if ($response && isset($response['id'])) {
            return $response;
        }

        return null;
    }

    /**
     * Create a subscription plan in Stripe.
     */
    public function createPlan(SubscriptionPlan $plan): ?string
    {
        $response = $this->request('POST', '/products', [
            'name' => $plan->name,
            'description' => $plan->description ?? '',
        ]);

        if ($response && isset($response['id'])) {
            $productId = $response['id'];

            $priceResponse = $this->request('POST', '/prices', [
                'unit_amount' => (int) ($plan->price * 100),
                'currency' => 'inr',
                'recurring' => [
                    'interval' => $this->mapInterval($plan->interval),
                    'interval_count' => 1,
                ],
                'product' => $productId,
            ]);

            if ($priceResponse && isset($priceResponse['id'])) {
                $plan->update(['stripe_price_id' => $priceResponse['id']]);
                $plan->update(['stripe_product_id' => $productId]);
                return $priceResponse['id'];
            }
        }

        return null;
    }

    /**
     * Create or get a Stripe customer for a user.
     */
    public function createCustomer(User $user): ?string
    {
        // Return existing customer ID if present
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $response = $this->request('POST', '/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        if ($response && isset($response['id'])) {
            $user->update(['stripe_customer_id' => $response['id']]);
            return $response['id'];
        }

        return null;
    }

    /**
     * Create a subscription for a user.
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, string $paymentMethodId, array $data = []): ?array
    {
        // 1. Ensure user has customer ID
        $customerId = $this->createCustomer($user);
        if (!$customerId) {
            Log::error('Failed to create Stripe customer', ['user_id' => $user->id]);
            return null;
        }

        // 2. Ensure plan has Stripe price ID
        if (!$plan->stripe_price_id) {
            $this->createPlan($plan);
            $plan->refresh();
        }

        if (!$plan->stripe_price_id) {
            Log::error('Failed to create Stripe plan', ['plan_id' => $plan->id]);
            return null;
        }

        $response = $this->request('POST', '/subscriptions', [
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $plan->stripe_price_id,
                ],
            ],
            'default_payment_method' => $paymentMethodId,
            'metadata' => [
                'user_id' => $user->id,
                'plan_name' => $plan->name,
            ],
        ]);

        if ($response && isset($response['id'])) {
            return [
                'subscription_id' => $response['id'],
                'status' => $response['status'] ?? 'active',
            ];
        }

        Log::error('Failed to create Stripe subscription', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'response' => $response,
        ]);

        return null;
    }

    /**
     * Cancel a subscription in Stripe.
     */
    public function cancelSubscription(string $subscriptionId, bool $cancelAtEndOfPeriod = true): bool
    {
        $params = $cancelAtEndOfPeriod ? ['cancel_at_period_end' => true] : [];
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/cancel", $params);

        return $response && isset($response['id']);
    }

    /**
     * Pause a subscription in Stripe.
     */
    public function pauseSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/pause", [
            'pause_collection' => [
                'behavior' => 'void',
            ],
        ]);

        return $response && isset($response['id']);
    }

    /**
     * Resume a paused subscription.
     */
    public function resumeSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/resume");
        return $response && isset($response['id']);
    }

    /**
     * Fetch subscription details from Stripe.
     */
    public function fetchSubscription(string $subscriptionId): ?array
    {
        return $this->request('GET', "/subscriptions/{$subscriptionId}");
    }

    /**
     * Fetch payment details from Stripe.
     */
    public function fetchPayment(string $paymentId): ?array
    {
        return $this->request('GET', "/payment_intents/{$paymentId}");
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            Log::warning('Stripe webhook secret not configured');
            return false;
        }

        try {
            \Stripe\Stripe::setApiKey($this->secretKey);
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Map internal subscription status from Stripe status.
     */
    public function mapStatus(string $stripeStatus): string
    {
        $statusMap = [
            'trialing' => 'pending',
            'active' => 'active',
            'past_due' => 'pending',
            'canceled' => 'cancelled',
            'unpaid' => 'failed',
        ];

        return $statusMap[$stripeStatus] ?? 'pending';
    }

    /**
     * Map plan interval to Stripe interval.
     */
    protected function mapInterval(string $interval): string
    {
        $intervalMap = [
            'monthly' => 'month',
            'yearly' => 'year',
            'weekly' => 'week',
            'daily' => 'day',
        ];

        return $intervalMap[$interval] ?? 'month';
    }

    /**
     * Make HTTP request to Stripe API.
     */
    protected function request(string $method, string $endpoint, array $data = []): ?array
    {
        $url = 'https://api.stripe.com/v1' . $endpoint;

        Log::info('Stripe API Request', [
            'method' => $method,
            'url' => $url,
        ]);

        $this->ensureCredentialsLoaded();
        try {
            $http = Http::withToken($this->secretKey)
                ->asForm()
                ->timeout(30);

            if (strtoupper($method) === 'GET') {
                $response = $http->get($url, $data);
            } else {
                $response = $http->post($url, $data);
            }

            if ($response->successful()) {
                return $response->json();
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();

            Log::error('Stripe API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);

            throw new \Exception('Stripe API Error: ' . $errorMessage);

        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'Stripe API')) {
                throw $e;
            }

            Log::error('Stripe API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Stripe Connection Error: ' . $e->getMessage());
        }
    }
}
