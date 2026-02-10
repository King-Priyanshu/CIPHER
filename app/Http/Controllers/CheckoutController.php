<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Services\Payment\RazorpayService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected RazorpayService $razorpayService;
    protected WalletService $walletService;
    protected SubscriptionService $subscriptionService;

    public function __construct(
        RazorpayService $razorpayService,
        WalletService $walletService,
        SubscriptionService $subscriptionService
    ) {
        $this->razorpayService = $razorpayService;
        $this->walletService = $walletService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show the checkout page for a specific plan.
     */
    public function show(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        // Basic check for existing active subscription
        $existingSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->first();

        // If strict single-subscription rule applies:
        if ($existingSubscription) {
             return redirect()->route('subscriber.subscription.index')
                ->with('info', 'You already have an active subscription.');
        }

        return view('checkout.index', compact('plan'));
    }

    /**
     * Create order (Razorpay).
     */
    public function createSubscription(Request $request, SubscriptionPlan $plan)
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
            $order = $this->razorpayService->createOrder($plan->price);

            if (!$order) {
                throw new \Exception('Failed to generate order ID from payment gateway.');
            }

            // Record Pending Subscription/Order
            UserSubscription::create([
                'user_id' => $user->id,
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
                'description' => $plan->name,
                'prefill' => [
                    'name' => $request->input('billing_name'),
                    'email' => $request->input('billing_email'),
                    'contact' => $request->input('billing_phone'),
                ],
                'notes' => [
                    'plan_id' => $plan->id,
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
     * Handle successful checkout (Callback).
     */
    public function success(Request $request)
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

        // Wait... verifyWebhookSignature uses webhook secret. 
        // For frontend callback, it uses key_secret hmac. 
        // Let's implement local signature check here using razorpay.secret (key secret)
        
        $keySecret = \App\Models\Setting::get('razorpay.secret') ?? config('services.razorpay.secret');
        $generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $keySecret);

        if ($generatedSignature !== $razorpaySignature) {
            return redirect()->route('subscriber.dashboard')->with('error', 'Payment verification failed.');
        }

        // Signature Good. Process Payment Idempotently.
        try {
            $this->processPaymentSuccess($razorpayPaymentId, $razorpayOrderId);
            return redirect()->route('subscriber.dashboard')->with('success', 'Payment successful! Subscription active.');
        } catch (\Exception $e) {
            Log::error('Payment Processing Error', ['error' => $e->getMessage()]);
            // Even if processing fails here, Webhook might pick it up.
            return redirect()->route('subscriber.dashboard')->with('info', 'Payment received. Account update in progress.');
        }
    }

    protected function processPaymentSuccess($paymentId, $orderId)
    {
        DB::transaction(function () use ($paymentId, $orderId) {
            // Check if already processed
            if (Payment::where('gateway_payment_id', $paymentId)->exists()) {
                return;
            }

            $subscription = UserSubscription::where('razorpay_order_id', $orderId)->first();

            if (!$subscription) {
                Log::error("Subscription not found for Order ID: $orderId");
                return;
            }

            // Fetch payment details to get exact amount (or trust plan price?)
            // Trusting plan price/subscription amount for now to save API call latency, 
            // but fetching is safer.
            $paymentDetails = $this->razorpayService->fetchPayment($paymentId);
            $amount = ($paymentDetails['amount'] / 100);

            // 1. Activate Subscription
            $subscription->update([
                'status' => 'active',
                'razorpay_payment_id' => $paymentId,
                'starts_at' => now(),
                'ends_at' => now()->addMonths($subscription->plan->duration_months ?? 1),
            ]);

            // 2. Create Payment Record
            $payment = Payment::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'gateway' => 'razorpay',
                'gateway_transaction_id' => $paymentId,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // 3. Credit Wallet & Ledger (One-step via Service)
            $this->walletService->credit(
                $subscription->user,
                $amount,
                'deposit',
                "Deposit via Razorpay ({$paymentId})",
                $payment
            );

            // 4. Record Invoice? (Optional for Phase 1, but good practice)
        });
    }

    public function checkStatus(Request $request)
    {
         // Keep existing logic or simple check
         return response()->json(['status' => 'unknown']);
    }
}
