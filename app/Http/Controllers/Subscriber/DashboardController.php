<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the subscriber dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        // Fetch active projects
        $activeProjects = \App\Models\Project::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('subscriber.dashboard', compact('user', 'subscription', 'activeProjects'));
    }
}
