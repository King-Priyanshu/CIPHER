<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get referral statistics
        $totalReferrals = $user->referrals()->count();
        $activeReferrals = $user->referrals()->whereHas('subscription', function ($query) {
            $query->where('status', 'active');
        })->count();
        
        // Calculate total referral earnings
        $totalEarnings = $user->walletTransactions()
            ->where('type', 'referral_bonus')
            ->sum('amount');
            
        // Get recent referrals
        $recentReferrals = $user->referrals()
            ->with('subscription')
            ->latest()
            ->take(5)
            ->get();
            
        // Get referral link
        $referralLink = route('register') . '?ref=' . $user->referral_code;
        
        // Get earnings data for chart
        $earningsData = $user->walletTransactions()
            ->where('type', 'referral_bonus')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Format data for Chart.js
        $chartData = [
            'labels' => $earningsData->pluck('date'),
            'datasets' => [
                [
                    'label' => 'Referral Earnings',
                    'data' => $earningsData->pluck('amount'),
                    'backgroundColor' => 'rgba(0, 191, 166, 0.1)',
                    'borderColor' => 'rgba(0, 191, 166, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];

        return view('subscriber.referrals.index', compact(
            'totalReferrals',
            'activeReferrals',
            'totalEarnings',
            'recentReferrals',
            'referralLink',
            'chartData'
        ));
    }
}
