<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the public home page.
     */
    public function index()
    {
        // Typically load active plans for the pricing section
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('public.home', compact('plans'));
    }
}
