<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\ProjectInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class AnalyticsController extends Controller
{
    /**
     * Show analytics dashboard.
     */
    public function index()
    {
        // Key metrics
        $metrics = [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount') ?? 0,
            'conversion_rate' => $this->calculateConversionRate(),
            'user_growth' => $this->calculateGrowthRate('users'),
            'new_users_growth' => $this->calculateGrowthRate('new_users'),
            'revenue_growth' => $this->calculateGrowthRate('revenue'),
            'conversion_rate_change' => $this->calculateGrowthRate('conversion'),
        ];

        // Revenue data for chart (last 30 days)
        $revenueData = Payment::select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'succeeded')
            ->where('paid_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueLabels = $revenueData->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('M d');
        });
        $revenueValues = $revenueData->pluck('total');

        // User acquisition data (last 30 days)
        $userAcquisitionData = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $userAcquisitionLabels = $userAcquisitionData->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('M d');
        });
        $userAcquisitionValues = $userAcquisitionData->pluck('count');

        // Investment distribution by project
        $investmentsByProject = ProjectInvestment::select(
                'projects.title',
                DB::raw('SUM(project_investments.amount) as total')
            )
            ->join('projects', 'projects.id', '=', 'project_investments.project_id')
            ->where('project_investments.status', 'active')
            ->groupBy('projects.title')
            ->get();

        $investmentDistributionLabels = $investmentsByProject->pluck('title');
        $investmentDistributionValues = $investmentsByProject->pluck('total');

        // User activity data
        $userActivityData = $this->calculateUserActivity();

        // Subscription plan data
        $subscriptionData = $this->getSubscriptionPlanData();

        // Payment methods data
        $paymentMethodsData = $this->getPaymentMethodsData();

        // User demographics data
        $demographicsData = $this->getUserDemographics();

        return view('admin.analytics.dashboard', [
            'metrics' => $metrics,
            'revenueData' => [
                'labels' => $revenueLabels,
                'data' => $revenueValues
            ],
            'userAcquisitionData' => [
                'labels' => $userAcquisitionLabels,
                'data' => $userAcquisitionValues
            ],
            'investmentDistributionData' => [
                'labels' => $investmentDistributionLabels,
                'data' => $investmentDistributionValues
            ],
            'userActivityData' => $userActivityData,
            'subscriptionData' => $subscriptionData,
            'paymentMethodsData' => $paymentMethodsData,
            'demographicsData' => $demographicsData
        ]);
    }

    /**
     * Calculate conversion rate.
     */
    private function calculateConversionRate()
    {
        $totalUsers = User::count();
        $payingUsers = User::whereHas('payments', function($query) {
            $query->where('status', 'succeeded');
        })->count();

        return $totalUsers > 0 ? round(($payingUsers / $totalUsers) * 100, 2) : 0;
    }

    /**
     * Calculate growth rate.
     */
    private function calculateGrowthRate($metric)
    {
        switch ($metric) {
            case 'users':
                $current = User::where('created_at', '>=', now()->subDays(30))->count();
                $previous = User::whereBetween('created_at', [
                    now()->subDays(60),
                    now()->subDays(31)
                ])->count();
                break;
            case 'new_users':
                $current = User::where('created_at', '>=', now()->startOfMonth())->count();
                $previous = User::whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ])->count();
                break;
            case 'revenue':
                $current = Payment::where('status', 'succeeded')
                    ->where('paid_at', '>=', now()->startOfMonth())
                    ->sum('amount');
                $previous = Payment::where('status', 'succeeded')
                    ->whereBetween('paid_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ])
                    ->sum('amount');
                break;
            case 'conversion':
                $currentRate = $this->calculateConversionRate();
                $previousRate = $this->calculateConversionRate(30);
                return $previousRate > 0 ? round((($currentRate - $previousRate) / $previousRate) * 100, 2) : 0;
            default:
                return 0;
        }

        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
    }

    /**
     * Calculate user activity.
     */
    private function calculateUserActivity()
    {
        $totalUsers = User::count();
        
        $activeUsers = User::whereHas('payments', function($query) {
            $query->where('status', 'succeeded')
                  ->where('paid_at', '>=', now()->subDays(30));
        })->count();

        $newUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        $churnedUsers = User::whereDoesntHave('payments', function($query) {
            $query->where('status', 'succeeded')
                  ->where('paid_at', '>=', now()->subDays(30));
        })->whereHas('payments', function($query) {
            $query->where('status', 'succeeded')
                  ->whereBetween('paid_at', [
                      now()->subDays(60),
                      now()->subDays(31)
                  ]);
        })->count();

        return [
            'labels' => ['Active Users', 'New Users', 'Churned Users', 'Total Users'],
            'data' => [
                round(($activeUsers / $totalUsers) * 100, 0),
                round(($newUsers / $totalUsers) * 100, 0),
                round(($churnedUsers / $totalUsers) * 100, 0),
                100
            ]
        ];
    }

    /**
     * Get subscription plan data.
     */
    private function getSubscriptionPlanData()
    {
        $plans = SubscriptionPlan::withCount('userSubscriptions')->get();
        $totalSubscriptions = UserSubscription::count();

        return $plans->map(function($plan) use ($totalSubscriptions) {
            return [
                'name' => $plan->name,
                'count' => $plan->user_subscriptions_count,
                'percentage' => $totalSubscriptions > 0 ? round(($plan->user_subscriptions_count / $totalSubscriptions) * 100, 1) : 0
            ];
        });
    }

    /**
     * Get payment methods data.
     */
    private function getPaymentMethodsData()
    {
        $payments = Payment::select('payment_method', DB::raw('COUNT(*) as count'))
            ->where('status', 'succeeded')
            ->groupBy('payment_method')
            ->get();

        $totalPayments = Payment::where('status', 'succeeded')->count();

        return $payments->map(function($payment) use ($totalPayments) {
            return [
                'name' => ucfirst($payment->payment_method),
                'count' => $payment->count,
                'percentage' => $totalPayments > 0 ? round(($payment->count / $totalPayments) * 100, 1) : 0
            ];
        });
    }

    /**
     * Get user demographics data.
     */
    private function getUserDemographics()
    {
        // This is a placeholder. In a real application, you would get this from user profiles
        return [
            [
                'category' => '18-24',
                'count' => 120,
                'percentage' => 24
            ],
            [
                'category' => '25-34',
                'count' => 180,
                'percentage' => 36
            ],
            [
                'category' => '35-44',
                'count' => 150,
                'percentage' => 30
            ],
            [
                'category' => '45+',
                'count' => 50,
                'percentage' => 10
            ]
        ];
    }

    /**
     * Export Report (CSV Example).
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'revenue');
        
        switch ($type) {
            case 'revenue':
                $fileName = 'revenue_report-' . now()->format('Y-m-d') . '.csv';
                $query = Payment::with('user')->where('status', 'succeeded')->latest();
                
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ];

                $callback = function() use ($query) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Date', 'Payment ID', 'User', 'Amount', 'Method']);

                    $query->chunk(100, function($payments) use ($file) {
                        foreach ($payments as $payment) {
                            fputcsv($file, [
                                $payment->created_at->format('Y-m-d H:i:s'),
                                $payment->id,
                                $payment->user->name ?? 'N/A',
                                $payment->amount,
                                $payment->payment_method
                            ]);
                        }
                    });

                    fclose($file);
                };
                
                return response()->stream($callback, 200, $headers);

            default:
                return back()->with('error', 'Invalid report type');
        }
    }
}
