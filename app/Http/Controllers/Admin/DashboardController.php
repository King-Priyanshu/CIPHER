<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\ProfitDistribution;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount'),
            'total_invested' => ProjectInvestment::active()->sum('amount'),
            'total_profits_distributed' => ProfitDistribution::where('status', 'completed')->sum('distributed_amount'),
            'active_projects' => Project::where('status', 'active')->count(),
            'pending_distributions' => ProfitDistribution::where('status', 'pending')->count(),
            'recent_payments' => Payment::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
