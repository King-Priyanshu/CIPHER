<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Show the checkout page for a specific plan.
     */
    public function show(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        // Check if user already has an active subscription
        $existingSubscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if ($existingSubscription) {
            return redirect()->route('subscriber.subscription.index')
                ->with('info', 'You already have an active subscription.');
        }

        return view('checkout.index', compact('plan'));
    }

    /**
     * Create Razorpay subscription and return subscription ID.
     */
    public function createSubscription(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();

        // Update user phone if provided
        if ($request->input('billing_phone') && !$user->phone) {
            $user->update(['phone' => $request->input('billing_phone')]);
        }

        try {
            // Create Razorpay subscription
            $result = $this->razorpayService->createSubscription($user, $plan);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create subscription. Please try again.',
                ], 500);
            }

            // Create pending subscription record in database
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'razorpay_subscription_id' => $result['subscription_id'],
                'razorpay_customer_id' => $user->razorpay_customer_id,
                'status' => 'pending',
            ]);

            Log::info('Razorpay subscription created', [
                'user_id' => $user->id,
                'subscription_id' => $result['subscription_id'],
            ]);

            return response()->json([
                'success' => true,
                'subscription_id' => $result['subscription_id'],
                'key_id' => config('services.razorpay.key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout subscription creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle successful checkout (redirect from Razorpay).
     */
    public function success(Request $request)
    {
        $subscriptionId = $request->query('subscription_id');
        
        return view('checkout.processing', [
            'subscription_id' => $subscriptionId,
        ]);
    }

    /**
     * Check subscription status (polled by processing page).
     */
    public function checkStatus(Request $request)
    {
        $razorpaySubId = $request->input('subscription_id');

        $subscription = UserSubscription::where('razorpay_subscription_id', $razorpaySubId)->first();

        if (!$subscription) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status' => $subscription->status,
            'activated' => $subscription->status === 'active',
        ]);
    }
}
