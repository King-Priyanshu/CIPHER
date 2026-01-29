<?php

namespace App\Console\Commands;

use App\Models\RewardPool;
use App\Models\UserSubscription;
use App\Services\RewardDistributionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DistributeRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rewards:distribute {--pool= : Specific reward pool ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and distribute rewards from active reward pools to eligible subscribers';

    /**
     * Execute the console command.
     */
    public function handle(RewardDistributionService $distributionService): int
    {
        $this->info('Starting reward distribution...');

        // Get pools to process
        $poolId = $this->option('pool');
        
        $query = RewardPool::where('status', 'active');
        if ($poolId) {
            $query->where('id', $poolId);
        }
        
        $pools = $query->get();

        if ($pools->isEmpty()) {
            $this->warn('No active reward pools found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pools->count()} active reward pool(s).");

        $totalDistributed = 0;
        $usersRewarded = 0;

        foreach ($pools as $pool) {
            $this->line("Processing pool: {$pool->name}");

            // Get eligible subscribers (active subscriptions)
            $eligibleSubscriptions = UserSubscription::where('status', 'active')
                ->with('user', 'plan')
                ->get();

            if ($eligibleSubscriptions->isEmpty()) {
                $this->line("  No eligible subscribers for this pool.");
                continue;
            }

            // Calculate share per subscriber based on their plan weight
            $totalWeight = $eligibleSubscriptions->sum(function ($sub) {
                return $sub->plan->reward_weight ?? 1;
            });

            if ($totalWeight <= 0) {
                $this->warn("  Total weight is zero, skipping pool.");
                continue;
            }

            $amountToDistribute = $pool->available_balance ?? 0;

            if ($amountToDistribute <= 0) {
                $this->line("  No funds available in pool.");
                continue;
            }

            foreach ($eligibleSubscriptions as $subscription) {
                $userWeight = $subscription->plan->reward_weight ?? 1;
                $sharePercentage = $userWeight / $totalWeight;
                $rewardAmount = $amountToDistribute * $sharePercentage;

                if ($rewardAmount > 0) {
                    try {
                        $distributionService->distributeToUser(
                            $subscription->user,
                            $pool,
                            $rewardAmount,
                            "Automated distribution from {$pool->name}"
                        );
                        
                        $totalDistributed += $rewardAmount;
                        $usersRewarded++;
                        
                        $this->line("  Distributed \${$rewardAmount} to {$subscription->user->name}");
                    } catch (\Exception $e) {
                        Log::error("Failed to distribute reward", [
                            'user_id' => $subscription->user_id,
                            'pool_id' => $pool->id,
                            'error' => $e->getMessage(),
                        ]);
                        $this->error("  Failed to reward {$subscription->user->name}: {$e->getMessage()}");
                    }
                }
            }

            // Update pool balance
            $pool->decrement('available_balance', $amountToDistribute);
        }

        $this->newLine();
        $this->info("Distribution complete!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Pools Processed', $pools->count()],
                ['Users Rewarded', $usersRewarded],
                ['Total Distributed', '$' . number_format($totalDistributed, 2)],
            ]
        );

        return Command::SUCCESS;
    }
}
