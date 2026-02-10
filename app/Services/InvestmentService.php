<?php

namespace App\Services;

use App\Models\ProjectInvestment;
use App\Models\InvestmentPlan;
use App\Models\User;
use App\Models\Project;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Services\ReferralService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvestmentService
{
    protected WalletService $walletService;
    protected ReferralService $referralService;

    public function __construct(WalletService $walletService, ReferralService $referralService)
    {
        $this->walletService = $walletService;
        $this->referralService = $referralService;
    }

    /**
     * create a new investment.
     */
    public function invest(User $user, Project $project, InvestmentPlan $plan, float $amount): ProjectInvestment
    {
        return DB::transaction(function () use ($user, $project, $plan, $amount) {
            
            // Debit Wallet
            $this->walletService->debit(
                $user,
                $amount,
                'investment',
                "Investment in {$project->title} ({$plan->name})",
                $project
            );

            // Create Investment Record
            $investment = ProjectInvestment::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'investment_plan_id' => $plan->id,
                'amount' => $amount,
                'status' => 'active',
                'allocated_at' => now(),
                'roi_start_date' => now()->addDays(1), // Starts next day?
                'roi_end_date' => now()->addMonths($plan->duration_months ?? 12),
            ]);

            // Distribute Referral Bonus
            $this->referralService->distributeBonus($investment);

            return $investment;
        });
    }

    /**
     * Calculate and distribute daily ROI for all active investments.
     */
    public function processDailyRoi(): void
    {
        $activeInvestments = ProjectInvestment::where('status', 'active')
            ->whereHas('project', function($q) {
                $q->where('status', 'active');
            })
            ->where('roi_start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('roi_end_date')->orWhere('roi_end_date', '>=', now());
            })
            ->with(['investmentPlan', 'user', 'project'])
            ->get();

        foreach ($activeInvestments as $investment) {
            $this->distributeRoi($investment);
        }
    }

    protected function distributeRoi(ProjectInvestment $investment)
    {
        $plan = $investment->investmentPlan;
        if (!$plan) return;

        // ROI Logic
        // Simple Example: Annual Return / 365 * Amount
        // Adjust based on Plan Frequency (daily, monthly)
        
        $annualReturnPercent = $plan->expected_return_percentage ?? 0;
        $dailyReturnRate = ($annualReturnPercent / 100) / 365;
        $payoutAmount = $investment->amount * $dailyReturnRate;

        if ($payoutAmount <= 0) return;

        try {
            DB::transaction(function () use ($investment, $payoutAmount) {
                $this->walletService->credit(
                    $investment->user,
                    $payoutAmount,
                    'roi_payout',
                    "Daily ROI for Investment #{$investment->id}",
                    $investment
                );

                $investment->increment('total_roi_earned', $payoutAmount);
                
                Log::info("ROI Payout: $payoutAmount to User {$investment->user_id}");
            });
        } catch (\Exception $e) {
            Log::error("ROI Payout Failed for Investment {$investment->id}: " . $e->getMessage());
        }
    }
}
