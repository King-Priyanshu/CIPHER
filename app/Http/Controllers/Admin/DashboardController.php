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
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Get basic stats that should always exist
        $stats = [
            'total_users' => User::count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount') ?? 0,
            'active_projects' => Project::where('status', 'active')->count(),
            'recent_payments' => Payment::with('user')->latest()->take(5)->get(),
        ];

        // Add investment stats only if table exists
        try {
            if (Schema::hasTable('project_investments')) {
                $stats['total_invested'] = ProjectInvestment::active()->sum('amount') ?? 0;
            } else {
                $stats['total_invested'] = 0;
            }
        } catch (\Exception $e) {
            $stats['total_invested'] = 0;
        }

        // Add profit distribution stats only if table exists
        try {
            if (Schema::hasTable('profit_distributions')) {
                $stats['total_profits_distributed'] = ProfitDistribution::where('status', 'completed')->sum('distributed_amount') ?? 0;
                $stats['pending_distributions'] = ProfitDistribution::where('status', 'pending')->count();
            } else {
                $stats['total_profits_distributed'] = 0;
                $stats['pending_distributions'] = 0;
            }
        } catch (\Exception $e) {
            $stats['total_profits_distributed'] = 0;
            $stats['pending_distributions'] = 0;
        }

        return view('admin.dashboard', compact('stats'));
    }
}
