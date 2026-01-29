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

        // Fetch active projects (mocking 'participating' for now as all active projects)
        // ideally: $user->projects() if a relation exists, using generic active projects for availability
        $activeProjects = \App\Models\Project::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Calculate total rewards
        $totalRewards = \App\Models\Reward::where('user_id', $user->id)->sum('amount');

        // Recent Invoices (Billing Preview)
        $nextPaymentDate = null;
        if ($subscription && $subscription->isActive()) {
            $nextPaymentDate = $subscription->ends_at; 
        }

        // Recent Activity (Logs)
        $activities = \App\Models\ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('subscriber.dashboard', compact(
            'user', 
            'subscription', 
            'activeProjects', 
            'totalRewards',
            'nextPaymentDate',
            'activities'
        ));
    }
}
