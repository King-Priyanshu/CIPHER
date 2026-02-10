<?php

namespace App\Services;

use App\Models\ProfitDistribution;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Models\UserProfitLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitDistributionService
{
    protected InvestmentAllocationService $investmentService;

    public function __construct(InvestmentAllocationService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    /**
     * Declare profit for a project.
     */
    public function declareProfit(Project $project, float $amount, User $declaredBy, ?string $notes = null): ProfitDistribution
    {
        return ProfitDistribution::create([
            'project_id' => $project->id,
            'total_profit' => $amount,
            'distributed_amount' => 0,
            'status' => ProfitDistribution::STATUS_PENDING,
            'declared_at' => now(),
            'declared_by' => $declaredBy->id,
            'notes' => $notes,
        ]);
    }

    /**
     * Distribute profit to all investors.
     */
    public function distributeProfit(ProfitDistribution $distribution): array
    {
        if ($distribution->isComplete()) {
            throw new \Exception('Distribution already completed.');
        }

        $project = $distribution->project;
        // Use Time-Weighted logic relative to the declaration date
        $investors = $this->investmentService->getProjectInvestorsForDistribution($project, $distribution->declared_at);

        if (empty($investors)) {
            throw new \Exception('No investors found for this project.');
        }

        $distribution->update(['status' => ProfitDistribution::STATUS_DISTRIBUTING]);

        $profitLogs = [];

        DB::transaction(function () use ($distribution, $investors, &$profitLogs) {
            // LOCKING: Prevent race conditions
            $lockedDistribution = ProfitDistribution::where('id', $distribution->id)->lockForUpdate()->first();

            if ($lockedDistribution->status === ProfitDistribution::STATUS_COMPLETED) {
                throw new \Exception('Distribution already completed (race condition detected).');
            }

            $totalDistributed = 0;

            foreach ($investors as $investor) {
                // MATH: Explicit rounding for currency safety
                $rawShare = ($investor['share_percentage'] / 100) * $lockedDistribution->total_profit;
                $profitShare = round($rawShare, 2);

                // Get user's primary investment for this project
                $investment = ProjectInvestment::where('user_id', $investor['user']->id)
                    ->where('project_id', $lockedDistribution->project_id)
                    ->active()
                    ->first();

                // Create profit log
                $profitLog = UserProfitLog::create([
                    'user_id' => $investor['user']->id,
                    'profit_distribution_id' => $lockedDistribution->id,
                    'project_investment_id' => $investment?->id,
                    'amount' => $profitShare,
                    'status' => UserProfitLog::STATUS_CREDITED,
                    'credited_at' => now(),
                ]);

                $profitLogs[] = $profitLog;
                $totalDistributed += $profitShare;

                Log::info('Profit distributed to user', [
                    'user_id' => $investor['user']->id,
                    'amount' => $profitShare,
                    'distribution_id' => $lockedDistribution->id,
                ]);
            }

            // Update distribution record
            $lockedDistribution->update([
                'distributed_amount' => $totalDistributed,
                'status' => ProfitDistribution::STATUS_COMPLETED,
                'distributed_at' => now(),
            ]);
            
            // AUDIT: Log the bulk action
            ActivityLog::create([
                'user_id' => auth()->id() ?? $lockedDistribution->declared_by, // Fallback if job
                'action' => 'profit.distributed',
                'description' => "Distributed ₹{$totalDistributed} to " . count($investors) . " investors.",
                'entity_type' => ProfitDistribution::class,
                'entity_id' => $lockedDistribution->id,
            ]);
        });

        return $profitLogs;
    }

    /**
     * Get user's total profits.
     */
    public function getUserTotalProfits(User $user): float
    {
        return UserProfitLog::where('user_id', $user->id)
            ->credited()
            ->sum('amount');
    }

    /**
     * Get user's profit history.
     */
    public function getUserProfitHistory(User $user, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return UserProfitLog::where('user_id', $user->id)
            ->with(['profitDistribution.project'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending distributions for admin.
     */
    public function getPendingDistributions(): \Illuminate\Database\Eloquent\Collection
    {
        return ProfitDistribution::where('status', ProfitDistribution::STATUS_PENDING)
            ->with(['project', 'declaredBy'])
            ->orderBy('declared_at', 'desc')
            ->get();
    }

    /**
     * Get distribution history for a project.
     */
    public function getProjectDistributionHistory(Project $project): \Illuminate\Database\Eloquent\Collection
    {
        return ProfitDistribution::where('project_id', $project->id)
            ->with('declaredBy')
            ->orderBy('declared_at', 'desc')
            ->get();
    }

    /**
     * Get platform-wide profit statistics.
     */
    public function getPlatformStats(): array
    {
        return [
            'total_declared' => ProfitDistribution::sum('total_profit'),
            'total_distributed' => ProfitDistribution::where('status', ProfitDistribution::STATUS_COMPLETED)->sum('distributed_amount'),
            'pending_distributions' => ProfitDistribution::where('status', ProfitDistribution::STATUS_PENDING)->count(),
            'total_profit_logs' => UserProfitLog::count(),
        ];
    }
}
