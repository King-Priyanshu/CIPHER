<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\UserProfitLog;
use App\Services\InvestmentAllocationService;
use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected InvestmentAllocationService $investmentService;
    protected \App\Services\WalletService $walletService;

    public function __construct(
        InvestmentAllocationService $investmentService,
        \App\Services\WalletService $walletService
    ) {
        $this->investmentService = $investmentService;
        $this->walletService = $walletService;
    }

    /**
     * Show the subscriber dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        // Eager load subscription and membership card
        $user->load(['subscription', 'membershipCard.perks']);
        $subscription = $user->subscription;

        // Get user's investments
        $investments = ProjectInvestment::where('user_id', $user->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total invested amount
        $totalInvested = ProjectInvestment::where('user_id', $user->id)
            ->active()
            ->sum('amount');

        // Get unique active projects the user has invested in
        $activeProjects = ProjectInvestment::where('user_id', $user->id)
            ->whereIn('status', ['allocated', 'active'])
            ->pluck('project_id')
            ->unique()
            ->count();

        // Calculate total profits (Credited - Available)
        $totalProfits = UserProfitLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->sum('amount');
            
        // Calculate accrued profits (Pending)
        $accruedProfits = UserProfitLog::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        // Get rewards
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

        // Get investment breakdown by project
        $investmentsByProject = $investments
            ->whereIn('status', ['allocated', 'active'])
            ->groupBy('project_id')
            ->map(fn($group) => [
                'project' => $group->first()->project,
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->values();

        // Calculate available balance for manual mode
        $availableBalance = 0;
        // Calculate available balance for manual mode
        $availableBalance = $this->walletService->getBalance($user);

        // Get referral info
        $referralCode = $user->referral_code;
        $referralCount = $user->referrals()->count();

        return view('subscriber.dashboard', compact(
            'user', 
            'subscription', 
            'investments',
            'investmentsByProject',
            'totalInvested',
            'activeProjects', 
            'totalProfits',
            'accruedProfits',
            'totalRewards',
            'nextPaymentDate',
            'activities',
            'availableBalance',
            'referralCode',
            'referralCount',
        ));
    }
}
