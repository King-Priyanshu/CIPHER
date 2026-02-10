<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPlan;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ignore pending subscriptions (stale checkouts) so user can retry
        $subscription = $user->subscriptions()->active()->latest()->first();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('subscriber.subscription.index', compact('subscription', 'plans'));
    }

    /**
     * Change subscription plan (upgrade/downgrade).
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = Auth::user();
        $subscription = $user->subscription;
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);

        if (!$subscription) {
            return redirect()->route('checkout.show', $newPlan->slug)
                ->with('info', 'You do not have an active subscription. Please subscribe first.');
        }

        $currentPlan = $subscription->plan;

        if ($currentPlan->id === $newPlan->id) {
            return redirect()->back()->with('info', 'You are already on this plan.');
        }

        // Determine if upgrade or downgrade
        $isUpgrade = $newPlan->price > $currentPlan->price;
        $priceDifference = abs($newPlan->price - $currentPlan->price);

        // For simplicity: Immediate plan change, effective next billing cycle
        // Pro-rata calculation could be added here for immediate effect
        $subscription->update([
            'plan_id' => $newPlan->id,
            'amount' => $newPlan->price,
            // 'next_plan_id' => $newPlan->id, // If we want effective next cycle
        ]);

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => $isUpgrade ? 'plan_upgraded' : 'plan_downgraded',
            'description' => "Changed plan from {$currentPlan->name} to {$newPlan->name}",
            'entity_type' => \App\Models\UserSubscription::class,
            'entity_id' => $subscription->id,
        ]);

        $message = $isUpgrade 
            ? "Upgraded to {$newPlan->name}! Your new rate is ₹{$newPlan->price}/month."
            : "Downgraded to {$newPlan->name}. Your new rate is ₹{$newPlan->price}/month.";

        return redirect()->back()->with('success', $message);
    }
}
