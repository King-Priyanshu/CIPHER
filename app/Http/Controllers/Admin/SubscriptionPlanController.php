<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('admin.subscriptions.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscriptions.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|in:monthly,quarterly,annual',
            'trial_days' => 'integer|min:0',
        ]);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }
}
