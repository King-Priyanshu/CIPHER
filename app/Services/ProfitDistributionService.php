<?php

namespace App\Services;

use App\Models\ProfitDistribution;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Models\UserProfitLog;
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
        $investors = $this->investmentService->getProjectInvestors($project);

        if (empty($investors)) {
            throw new \Exception('No investors found for this project.');
        }

        $distribution->update(['status' => ProfitDistribution::STATUS_DISTRIBUTING]);

        $profitLogs = [];

        DB::transaction(function () use ($distribution, $investors, &$profitLogs) {
            $totalDistributed = 0;

            foreach ($investors as $investor) {
                // Calculate profit share based on investment percentage
                $profitShare = ($investor['share_percentage'] / 100) * $distribution->total_profit;

                // Get user's primary investment for this project
                $investment = ProjectInvestment::where('user_id', $investor['user']->id)
                    ->where('project_id', $distribution->project_id)
                    ->active()
                    ->first();

                // Create profit log
                $profitLog = UserProfitLog::create([
                    'user_id' => $investor['user']->id,
                    'profit_distribution_id' => $distribution->id,
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
                    'distribution_id' => $distribution->id,
                ]);
            }

            // Update distribution record
            $distribution->update([
                'distributed_amount' => $totalDistributed,
                'status' => ProfitDistribution::STATUS_COMPLETED,
                'distributed_at' => now(),
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
