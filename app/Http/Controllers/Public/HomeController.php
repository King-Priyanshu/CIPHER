<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Project;
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
        $projects = Project::where('status', 'active')
            ->where('is_featured', true)
            ->take(3)
            ->get();
            
        return view('public.home', compact('plans', 'projects'));
    }
}
