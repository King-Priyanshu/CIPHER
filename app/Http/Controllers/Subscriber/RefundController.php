<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\SubscriptionService;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected AdminAuditService $auditService;

    public function __construct(SubscriptionService $subscriptionService, AdminAuditService $auditService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->auditService = $auditService;
    }

    public function index()
    {
        $refunds = Auth::user()->refunds()->with('subscription')->latest()->get();
        
        return view('subscriber.refunds.index', [
            'refunds' => $refunds,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $subscription = $this->subscriptionService->getActiveSubscription($user->id);

        if (!$subscription) {
            return redirect()->route('subscriber.dashboard')
                ->with('error', 'No active subscription found.');
        }

        return view('subscriber.refunds.create', [
            'subscription' => $subscription,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'confirm' => 'required|accepted',
        ]);

        $user = Auth::user();
        $subscription = $this->subscriptionService->getActiveSubscription($user->id);

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Check if eligible for refund (e.g. before 11 months)
        // Directive says "anytime before 11 months", so we check if not expired.
        // Actually, logic is in service, but let's call service.

        if ($this->subscriptionService->requestCancellationWithRefund($user, $subscription, $request->reason)) {
            return redirect()->route('subscriber.refunds.index')
                ->with('status', 'Refund request submitted successfully. Your subscription is now pending cancellation.');
        }

        return back()->with('error', 'Unable to process refund request.');
    }

    public function show($id)
    {
        $refund = Auth::user()->refunds()->with('subscription')->findOrFail($id);
        
        return view('subscriber.refunds.show', [
            'refund' => $refund,
        ]);
    }
}
