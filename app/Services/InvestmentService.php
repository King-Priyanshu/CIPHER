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
     * Create a pending investment (Manual Allocation).
     */
    public function createPendingInvestment(User $user, Project $project, InvestmentPlan $plan, float $amount): ProjectInvestment
    {
        return ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'investment_plan_id' => $plan->id,
            'amount' => $amount,
            'status' => ProjectInvestment::STATUS_PENDING_PAYMENT,
            'allocation_type' => 'manual',
        ]);
    }

    /**
     * Create a pending investment (Automatic Allocation).
     */
    public function createPendingAutoInvestment(User $user, InvestmentPlan $plan, float $amount): ProjectInvestment
    {
        return ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => null, // Project not assigned yet
            'investment_plan_id' => $plan->id,
            'amount' => $amount,
            'status' => ProjectInvestment::STATUS_PENDING_PAYMENT,
            'allocation_type' => 'auto',
        ]);
    }

    /**
     * Finalize an investment after successful payment.
     */
    public function finalizeInvestment(ProjectInvestment $investment): ProjectInvestment
    {
        return DB::transaction(function () use ($investment) {
            $user = $investment->user;
            $project = $investment->project;
            $plan = $investment->investmentPlan;
            $amount = (float) $investment->amount;

            // Update status based on allocation type
            if ($investment->allocation_type === 'manual') {
                $investment->status = ProjectInvestment::STATUS_ACTIVE;
                $investment->allocated_at = now();

                // Update project funding if manual
                if ($project) {
                    $project->increment('current_fund', $amount);
                }
            } else {
                $investment->status = ProjectInvestment::STATUS_PENDING_ADMIN_ALLOCATION;
                // No allocated_at yet until admin assigns project
            }

            // Set ROI dates if active
            if ($investment->status === ProjectInvestment::STATUS_ACTIVE) {
                $investment->roi_start_date = now()->addDays(1);
                $investment->roi_end_date = now()->addMonths($plan->duration_months ?? 12);
            }

            $investment->save();

            // Note: Wallet debit usually happens when funding the wallet or if paying via wallet.
            // If the payment was external (Razorpay), we don't necessarily debit the internal wallet
            // unless we want to reflect the flow in the wallet history too.
            // For consistency with existing invest() method:

            $this->walletService->credit($user, $amount, 'deposit', "Deposit for Investment #{$investment->id}");
            $this->walletService->debit(
                $user,
                $amount,
                'investment',
                "Investment in " . ($project ? $project->title : 'Automatic Portfolio') . " ({$plan->name})",
                $investment
            );

            // Distribute Referral Bonus
            $this->referralService->distributeBonus($investment);

            return $investment;
        });
    }

    /**
     * Legacy/Immediate investment (uses wallet balance).
     */
    public function invest(User $user, Project $project, InvestmentPlan $plan, float $amount): ProjectInvestment
    {
        $investment = $this->createPendingInvestment($user, $project, $plan, $amount);
        return $this->finalizeInvestment($investment);
    }

    /**
     * Calculate and distribute daily ROI for all active investments.
     */
    public function processDailyRoi(): void
    {
        $activeInvestments = ProjectInvestment::where('status', 'active')
            ->whereHas('project', function ($q) {
                $q->where('status', 'active');
            })
            ->where('roi_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('roi_end_date')->orWhere('roi_end_date', '>=', now());
            })
            ->with(['investmentPlan', 'user', 'project'])
            ->get();

        foreach ($activeInvestments as $investment) {
            /** @var ProjectInvestment $investment */
            $this->distributeRoi($investment);
        }
    }

    protected function distributeRoi(ProjectInvestment $investment)
    {
        $plan = $investment->investmentPlan;
        if (!$plan)
            return;

        // ROI Logic
        // Simple Example: Annual Return / 365 * Amount
        // Adjust based on Plan Frequency (daily, monthly)

        $annualReturnPercent = $plan->expected_return_percentage ?? 0;
        $dailyReturnRate = ($annualReturnPercent / 100) / 365;
        $payoutAmount = $investment->amount * $dailyReturnRate;

        if ($payoutAmount <= 0)
            return;

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
