<?php

namespace App\Http\Controllers;

use App\Services\MockPaymentService;
use App\Services\SubscriptionService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestPaymentController extends Controller
{
    protected $mockPaymentService;
    protected $subscriptionService;

    public function __construct(MockPaymentService $mockPaymentService, SubscriptionService $subscriptionService)
    {
        $this->mockPaymentService = $mockPaymentService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle a test payment.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        if (!app()->environment('local')) {
            abort(403, 'This route is only available in local environment.');
        }

        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'subscriptionPlanId' => 'required|exists:subscription_plans,id',
            'amount' => 'required|numeric|min:0',
            'forceStatus' => 'required|in:success,failed,pending,random',
        ]);

        $result = $this->mockPaymentService->processPayment(
            $validated['userId'],
            $validated['subscriptionPlanId'],
            $validated['amount'],
            $validated['forceStatus']
        );

        // Record payment in database
        $payment = Payment::create([
            'user_id' => $validated['userId'],
            'amount' => $validated['amount'],
            'gateway' => 'mock',
            'gateway_transaction_id' => $result['transaction_id'],
            'status' => $result['status'],
            'currency' => $result['currency'],
            'paid_at' => $result['success'] ? now() : null,
        ]);

        if ($result['success']) {
            $subscription = $this->subscriptionService->activateSubscription(
                $validated['userId'],
                $validated['subscriptionPlanId']
            );
            $payment->update(['subscription_id' => $subscription->id]);
        }

        return response()->json([
            'paymentStatus' => $result['status'],
            'paymentId' => $result['transaction_id'],
            'message' => $result['message'],
        ]);
    }
}
