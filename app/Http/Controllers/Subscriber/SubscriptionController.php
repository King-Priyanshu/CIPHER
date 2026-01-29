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
        $subscription = $user->subscription;
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('subscriber.subscription.index', compact('subscription', 'plans'));
    }
}
