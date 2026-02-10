<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\ProjectInvestment;
use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Services\Payment\RazorpayService;
use App\Services\Payment\StripeService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected RazorpayService $razorpayService;
    protected StripeService $stripeService;
    protected WalletService $walletService;
    protected SubscriptionService $subscriptionService;

    public function __construct(
        RazorpayService $razorpayService,
        StripeService $stripeService,
        WalletService $walletService,
        SubscriptionService $subscriptionService
    ) {
        $this->razorpayService = $razorpayService;
        $this->stripeService = $stripeService;
        $this->walletService = $walletService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show the checkout page for a specific plan.
     */
    public function show(Request $request, SubscriptionPlan $plan)
    {
        $user = Auth::user();
        $projectId = $request->input('project');

        if (!$projectId) {
            return redirect()->route('subscriber.projects.index')
                ->with('error', 'Please select a project to subscribe to.');
        }

        $project = \App\Models\Project::findOrFail($projectId);

        // Basic check for existing active subscription (per project?)
        // User says: "user invests per month in only one project" - so maybe they can have multiple subscriptions if they subscribe to different projects?
        // But also "one id one plan". 
        // Let's assume for now a user can have multiple subscriptions if they are for DIFFERENT projects.
        // But the current system might be designed for single subscription. 
        // "user invests {selected plan} per month in only one project" -> maybe implies TOTAL one project?
        // "in onle one project one id one plan" -> sounds like Strict Single Subscription linked to Single Project.

        $existingSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->first();

        // If strict single-subscription rule applies:
        if ($existingSubscription) {
            return redirect()->route('subscriber.subscription.index')
                ->with('info', 'You already have an active subscription.');
        }

        $enabledGateways = self::getEnabledGateways();

        return view('checkout.index', compact('plan', 'enabledGateways', 'project'));
    }

    /**
     * Get enabled gateways from admin settings.
     */
    public static function getEnabledGateways(): array
    {
        $setting = \App\Models\Setting::get('payment.enabled_gateways');
        if ($setting) {
            return is_array($setting) ? $setting : explode(',', $setting);
        }
        // Default: all gateways enabled
        return ['razorpay', 'stripe', 'test_payment'];
    }

    /**
     * Create payment intent for subscription.
     * Routes to the selected gateway: razorpay, stripe, or test_payment.
     */
    public function createSubscription(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'nullable|string|max:20',
            'project_id' => 'required|exists:projects,id',
        ]);

        $user = Auth::user();
        $gateway = $request->input('gateway', 'razorpay');
        $project = \App\Models\Project::findOrFail($request->input('project_id'));

        // Validate gateway is enabled
        $enabled = self::getEnabledGateways();
        if (!in_array($gateway, $enabled)) {
            return response()->json(['success' => false, 'message' => 'This payment method is not available.'], 422);
        }

        try {
            // Update phone if missing
            if ($request->input('billing_phone') && !$user->phone) {
                $user->update(['phone' => $request->input('billing_phone')]);
            }

            if ($gateway === 'test_payment') {
                return $this->processTestPaymentSubscription($user, $plan, $project);
            } elseif ($gateway === 'stripe') {
                return $this->createStripeSubscriptionIntent($request, $plan, $project);
            }

            // Default: Razorpay
            return $this->createRazorpaySubscriptionOrder($request, $plan, $project);

        } catch (\Exception $e) {
            Log::error('Checkout Create Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Test Payment: auto-succeed subscription without gateway API.
     */
    protected function processTestPaymentSubscription($user, SubscriptionPlan $plan, $project)
    {
        $testPaymentId = 'test_' . uniqid() . '_' . time();

        DB::transaction(function () use ($user, $plan, $project, $testPaymentId) {
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonths($plan->duration_in_months ?? 1),
            ]);

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'gateway' => 'test_payment',
                'gateway_transaction_id' => $testPaymentId,
                'amount' => $plan->price,
                'currency' => 'INR',
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);

            $this->walletService->credit(
                $user,
                (float) $plan->price,
                'deposit',
                "Subscription (Project: {$project->title}) via Test Payment ({$testPaymentId})",
                $payment
            );
        });

        return response()->json([
            'success' => true,
            'mock_success' => true,
            'redirect_url' => route('subscriber.dashboard') . '?success=' . urlencode("Subscribed to {$project->title} successfully!"),
        ]);
    }

    /**
     * Create Razorpay order for subscription.
     */
    protected function createRazorpaySubscriptionOrder(Request $request, SubscriptionPlan $plan, $project)
    {
        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        try {
            // Update phone if missing
            if ($request->input('billing_phone') && !$user->phone) {
                $user->update(['phone' => $request->input('billing_phone')]);
            }

            // Create Order
            $order = $this->razorpayService->createOrder((int) $plan->price);

            if (!$order) {
                throw new \Exception('Failed to generate order ID from payment gateway.');
            }

            // Record Pending Subscription/Order
            UserSubscription::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'pending',
                'razorpay_order_id' => $order['id'],
                'razorpay_payment_id' => null,
                // dates set on success
            ]);

            $keyId = \App\Models\Setting::get('razorpay.key') ?? config('services.razorpay.key');

            return response()->json([
                'success' => true,
                'key_id' => $keyId,
                'order_id' => $order['id'],
                'amount' => $plan->price * 100,
                'name' => config('app.name'),
                'description' => "Subscription: {$plan->name}",
                'prefill' => [
                    'name' => $request->input('billing_name'),
                    'email' => $request->input('billing_email'),
                    'contact' => $request->input('billing_phone'),
                ],
                'notes' => [
                    'plan_id' => $plan->id,
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'description' => 'one_time_payment'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout Create Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Stripe payment intent for subscription.
     */
    protected function createStripeSubscriptionIntent(Request $request, SubscriptionPlan $plan, $project)
    {
        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        try {
            // Update phone if missing
            if ($request->input('billing_phone') && !$user->phone) {
                $user->update(['phone' => $request->input('billing_phone')]);
            }

            // Create Payment Intent via Service
            $paymentIntent = $this->stripeService->createPaymentIntent(
                (int) ($plan->price * 100), // Amount in cents
                'inr',
                [
                    'integration_check' => 'accept_a_payment',
                    'plan_id' => $plan->id,
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'type' => 'subscription'
                ]
            );

            // Record Pending Subscription
            UserSubscription::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent['id'],
                // dates set on success
            ]);

            $key = \App\Models\Setting::get('stripe.key') ?? config('services.stripe.key');

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent['client_secret'],
                'key' => $key,
                'name' => $user->name,
                'email' => $user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Intent Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle successful checkout (Callback).
     */
    public function success(Request $request)
    {
        $gateway = $request->input('gateway', 'razorpay');

        if ($gateway === 'stripe') {
            return $this->handleStripeSuccess($request);
        }

        // Default to Razorpay
        return $this->handleRazorpaySuccess($request);
    }

    /**
     * Handle Razorpay success callback.
     */
    protected function handleRazorpaySuccess(Request $request)
    {
        $input = $request->all();
        $razorpayPaymentId = $input['razorpay_payment_id'] ?? null;
        $razorpayOrderId = $input['razorpay_order_id'] ?? null;
        $razorpaySignature = $input['razorpay_signature'] ?? null;

        if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpaySignature) {
            return redirect()->route('subscriber.dashboard')->with('error', 'Invalid payment response.');
        }

        $valid = $this->razorpayService->verifyWebhookSignature(
            $razorpayOrderId . '|' . $razorpayPaymentId,
            $razorpaySignature
        );

        // For frontend callback, use key_secret hmac
        $keySecret = \App\Models\Setting::get('razorpay.secret') ?? config('services.razorpay.secret');
        $generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $keySecret);

        if ($generatedSignature !== $razorpaySignature) {
            return redirect()->route('subscriber.dashboard')->with('error', 'Payment verification failed.');
        }

        // Signature Good. Process Payment Idempotently.
        try {
            $this->processPaymentSuccess($razorpayPaymentId, $razorpayOrderId, 'razorpay');
            return redirect()->route('subscriber.dashboard')->with('success', 'Payment successful! Subscription active.');
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Processing Error', ['error' => $e->getMessage()]);
            return redirect()->route('subscriber.dashboard')->with('info', 'Payment received. Account update in progress.');
        }
    }

    /**
     * Handle Stripe success callback.
     */
    protected function handleStripeSuccess(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent');
        $paymentIntentSecret = $request->input('payment_intent_client_secret');
        $redirectStatus = $request->input('redirect_status');

        if (!$paymentIntentId || $redirectStatus !== 'succeeded') {
            return redirect()->route('subscriber.dashboard')->with('error', 'Invalid payment response.');
        }

        // Signature Good. Process Payment Idempotently.
        try {
            $this->processPaymentSuccess($paymentIntentId, $paymentIntentId, 'stripe');
            return redirect()->route('subscriber.dashboard')->with('success', 'Payment successful! Subscription active.');
        } catch (\Exception $e) {
            Log::error('Stripe Payment Processing Error', ['error' => $e->getMessage()]);
            return redirect()->route('subscriber.dashboard')->with('info', 'Payment received. Account update in progress.');
        }
    }

    protected function processPaymentSuccess($paymentId, $orderId, $gateway = 'razorpay')
    {
        DB::transaction(function () use ($paymentId, $orderId, $gateway) {
            // Check if already processed
            if (Payment::where('gateway_transaction_id', $paymentId)->exists()) {
                return;
            }

            $subscription = null;
            $investment = null;
            $amount = 0;

            if ($gateway === 'razorpay') {
                $subscription = UserSubscription::where('razorpay_order_id', $orderId)->first();
                $investment = ProjectInvestment::where('razorpay_order_id', $orderId)->first();

                $paymentDetails = $this->razorpayService->fetchPayment($paymentId);
                $amount = ($paymentDetails['amount'] / 100);

                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'razorpay_payment_id' => $paymentId,
                        'starts_at' => now(),
                        'ends_at' => now()->addMonths($subscription->plan->duration_months ?? 1),
                    ]);
                }

                if ($investment) {
                    $investment->update(['razorpay_payment_id' => $paymentId]);
                    app(\App\Services\InvestmentService::class)->finalizeInvestment($investment);
                }
            } elseif ($gateway === 'stripe') {
                $subscription = UserSubscription::where('stripe_payment_intent_id', $orderId)->first();
                $investment = ProjectInvestment::where('stripe_payment_intent_id', $orderId)->first();

                $paymentDetails = $this->stripeService->fetchPayment($paymentId);
                $amount = ($paymentDetails['amount'] / 100);

                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'stripe_payment_intent_id' => $paymentId,
                        'starts_at' => now(),
                        'ends_at' => now()->addMonths($subscription->plan->duration_months ?? 1),
                    ]);
                }

                if ($investment) {
                    $investment->update(['stripe_payment_intent_id' => $paymentId]);
                    app(\App\Services\InvestmentService::class)->finalizeInvestment($investment);
                }
            }

            if (!$subscription && !$investment) {
                Log::error("No subscription or investment found for Order ID: $orderId");
                return;
            }

            // 2. Create Payment Record
            $paymentData = [
                'user_id' => $subscription ? $subscription->user_id : $investment->user_id,
                'subscription_id' => $subscription?->id,
                'project_investment_id' => $investment?->id,
                'gateway' => $gateway,
                'gateway_transaction_id' => $paymentId,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'succeeded',
                'paid_at' => now(),
            ];

            // Store gateway-specific IDs
            if ($gateway === 'razorpay') {
                $paymentData['razorpay_payment_id'] = $paymentId;
                $paymentData['razorpay_order_id'] = $orderId;
                if ($subscription) {
                    $paymentData['razorpay_subscription_id'] = $subscription->razorpay_subscription_id;
                }
            } elseif ($gateway === 'stripe') {
                $paymentData['stripe_payment_intent_id'] = $paymentId;
                if ($subscription) {
                    $paymentData['stripe_subscription_id'] = $subscription->stripe_subscription_id;
                }
            }

            $payment = Payment::create($paymentData);

            // 3. Credit Wallet & Ledger (One-step via Service)
            $user = $subscription ? $subscription->user : $investment->user;
            $this->walletService->credit(
                $user,
                $amount,
                'deposit',
                "Deposit via {$gateway} ({$paymentId})",
                $payment
            );
        });
    }

    /**
     * Create order for Investment.
     * Routes to the selected gateway: razorpay, stripe, or test_payment.
     */
    public function createInvestmentOrder(Request $request, ProjectInvestment $investment)
    {
        $gateway = $request->input('gateway', 'razorpay');
        $user = Auth::user();

        if ($investment->user_id !== $user->id) {
            abort(403);
        }

        // Validate gateway is enabled
        $enabled = self::getEnabledGateways();
        if (!in_array($gateway, $enabled)) {
            return response()->json(['success' => false, 'message' => 'This payment method is not available.'], 422);
        }

        try {
            if ($gateway === 'test_payment') {
                return $this->processTestPaymentInvestment($user, $investment);
            } elseif ($gateway === 'stripe') {
                return $this->createStripeInvestmentIntent($request, $investment);
            }

            // Default: Razorpay
            return $this->createRazorpayInvestmentOrder($request, $investment);

        } catch (\Exception $e) {
            Log::error($gateway . ' Investment Checkout Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Test Payment: auto-succeed investment without gateway API.
     */
    protected function processTestPaymentInvestment($user, ProjectInvestment $investment)
    {
        $testPaymentId = 'test_' . uniqid() . '_' . time();

        DB::transaction(function () use ($user, $investment, $testPaymentId) {
            // Finalize investment
            app(\App\Services\InvestmentService::class)->finalizeInvestment($investment);

            $payment = Payment::create([
                'user_id' => $user->id,
                'project_investment_id' => $investment->id,
                'gateway' => 'test_payment',
                'gateway_transaction_id' => $testPaymentId,
                'amount' => $investment->amount,
                'currency' => 'INR',
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);

            $this->walletService->credit(
                $user,
                (float) $investment->amount,
                'deposit',
                "Investment via Test Payment ({$testPaymentId})",
                $payment
            );
        });

        return response()->json([
            'success' => true,
            'mock_success' => true,
            'redirect_url' => route('subscriber.dashboard') . '?success=' . urlencode('Payment successful! Investment is now active.'),
        ]);
    }

    /**
     * Create Razorpay order for investment.
     */
    protected function createRazorpayInvestmentOrder(Request $request, ProjectInvestment $investment)
    {
        $user = Auth::user();

        // Create Razorpay Order
        $order = $this->razorpayService->createOrder((int) $investment->amount);

        if (!$order) {
            throw new \Exception('Failed to generate order ID from Razorpay.');
        }

        // Link Order to Investment
        $investment->update([
            'razorpay_order_id' => $order['id']
        ]);

        $keyId = \App\Models\Setting::get('razorpay.key') ?? config('services.razorpay.key');

        return response()->json([
            'success' => true,
            'key_id' => $keyId,
            'order_id' => $order['id'],
            'amount' => $investment->amount * 100,
            'name' => config('app.name'),
            'description' => "Investment Payment",
            'prefill' => [
                'name' => $user->name,
                'email' => $user->email,
                'contact' => $user->phone,
            ],
            'notes' => [
                'investment_id' => $investment->id,
                'user_id' => $user->id,
                'description' => 'investment_payment'
            ]
        ]);
    }

    /**
     * Create Stripe payment intent for investment.
     */
    protected function createStripeInvestmentIntent(Request $request, ProjectInvestment $investment)
    {
        $user = Auth::user();

        // Create Payment Intent
        $paymentIntent = $this->stripeService->createPaymentIntent(
            (int) $investment->amount,
            'INR',
            [
                'investment_id' => $investment->id,
                'user_id' => $user->id,
                'description' => 'investment_payment',
            ]
        );

        if (!$paymentIntent) {
            throw new \Exception('Failed to generate payment intent from Stripe.');
        }

        // Link Payment Intent to Investment
        $investment->update([
            'stripe_payment_intent_id' => $paymentIntent['id']
        ]);

        $keyId = \App\Models\Setting::get('stripe.key') ?? config('services.stripe.key');

        return response()->json([
            'success' => true,
            'key_id' => $keyId,
            'payment_intent_id' => $paymentIntent['id'],
            'client_secret' => $paymentIntent['client_secret'],
            'amount' => $investment->amount * 100,
            'name' => config('app.name'),
            'description' => "Investment Payment",
            'currency' => 'INR',
        ]);
    }

    /**
     * Handle payment status updates (success/failure reporting from frontend).
     */
    public function checkStatus(Request $request)
    {
        $gateway = $request->input('gateway', 'razorpay');
        $orderId = $request->input('order_id');
        $status = $request->input('status', 'unknown');

        if ($status === 'failed' && $orderId) {
            return $this->processPaymentFailure($request, $gateway, $orderId);
        }

        return response()->json(['status' => $status]);
    }

    /**
     * Process and record a payment failure.
     */
    protected function processPaymentFailure(Request $request, string $gateway, string $orderId)
    {
        $errorCode = $request->input('error_code', 'unknown');
        $errorDescription = $request->input('error_description', 'Payment failed');

        DB::transaction(function () use ($gateway, $orderId, $errorCode, $errorDescription) {
            $subscription = null;
            $investment = null;

            if ($gateway === 'razorpay') {
                $subscription = UserSubscription::where('razorpay_order_id', $orderId)->first();
                $investment = ProjectInvestment::where('razorpay_order_id', $orderId)->first();
            } elseif ($gateway === 'stripe') {
                $subscription = UserSubscription::where('stripe_payment_intent_id', $orderId)->first();
                $investment = ProjectInvestment::where('stripe_payment_intent_id', $orderId)->first();
            }

            // Update subscription/investment status
            if ($subscription && $subscription->status === 'pending') {
                $subscription->update(['status' => 'failed']);
            }

            if ($investment && $investment->status === ProjectInvestment::STATUS_PENDING_PAYMENT) {
                $investment->update(['status' => ProjectInvestment::STATUS_FAILED]);
            }

            // Create failed payment record
            $userId = $subscription?->user_id ?? $investment?->user_id ?? auth()->id();
            if ($userId) {
                $paymentData = [
                    'user_id' => $userId,
                    'subscription_id' => $subscription?->id,
                    'project_investment_id' => $investment?->id,
                    'gateway' => $gateway,
                    'gateway_transaction_id' => $orderId,
                    'amount' => $subscription?->amount ?? $investment?->amount ?? 0,
                    'currency' => 'INR',
                    'status' => 'failed',
                    'failure_reason' => "{$errorCode}: {$errorDescription}",
                ];

                if ($gateway === 'razorpay') {
                    $paymentData['razorpay_order_id'] = $orderId;
                } elseif ($gateway === 'stripe') {
                    $paymentData['stripe_payment_intent_id'] = $orderId;
                }

                Payment::create($paymentData);
            }

            Log::warning('Payment failed', [
                'gateway' => $gateway,
                'order_id' => $orderId,
                'error' => "{$errorCode}: {$errorDescription}",
            ]);
        });

        return response()->json(['status' => 'recorded', 'message' => 'Failure recorded.']);
    }
}
