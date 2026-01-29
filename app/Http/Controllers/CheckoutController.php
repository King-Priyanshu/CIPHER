<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show the checkout page for a specific plan.
     */
    public function show(SubscriptionPlan $plan)
    {
        return view('checkout.index', compact('plan'));
    }

    /**
     * Process the subscription checkout.
     */
    public function process(Request $request, SubscriptionPlan $plan)
    {
        // Validate input (payment details would be validated by gateway in real app)
        $request->validate([
            'payment_method' => 'required|in:stripe,razorpay',
            'card_holder_name' => 'required|string',
            // In a real integration, we'd handle payment intent tokens here
        ]);

        $user = Auth::user();

        try {
            // Simulator: Assume payment is successful
            // In real app: $this->paymentGateway->charge($user, $plan, $request->token);

            // Create subscription
            $this->subscriptionService->subscribe($user, $plan);

            return redirect()->route('subscriber.dashboard')
                ->with('success', "You have successfully subscribed to the {$plan->name} plan!");

        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
