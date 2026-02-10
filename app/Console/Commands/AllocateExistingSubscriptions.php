<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Services\InvestmentAllocationService;
use Illuminate\Console\Command;

class AllocateExistingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'investments:allocate-existing 
                            {--dry-run : Show what would be allocated without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Allocate investments for existing active subscriptions that have no allocations';

    /**
     * Execute the console command.
     */
    public function handle(InvestmentAllocationService $investmentService): int
    {
        $this->info('Finding active subscriptions without allocations...');

        // Get active subscriptions that have no investments
        $subscriptions = UserSubscription::where('status', 'active')
            ->whereDoesntHave('investments')
            ->with(['user', 'plan'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions need allocation.');
            return Command::SUCCESS;
        }

        $this->info("Found {$subscriptions->count()} subscriptions to process.");

        $totalAllocations = 0;
        $totalAmount = 0;

        foreach ($subscriptions as $subscription) {
            $this->line("Processing: {$subscription->user->name} ({$subscription->plan->name})");

            if ($this->option('dry-run')) {
                $this->comment("  Would allocate: ₹{$subscription->plan->price}");
                continue;
            }

            $allocations = $investmentService->allocateFromSubscription($subscription);
            
            if (count($allocations) > 0) {
                $amount = collect($allocations)->sum('amount');
                $this->info("  ✓ Allocated ₹{$amount} across " . count($allocations) . " projects");
                $totalAllocations += count($allocations);
                $totalAmount += $amount;
            } else {
                $this->warn("  ⚠ No projects available for allocation");
            }
        }

        if (!$this->option('dry-run')) {
            $this->newLine();
            $this->info("Summary:");
            $this->info("  Total Allocations: {$totalAllocations}");
            $this->info("  Total Amount: ₹{$totalAmount}");
        }

        return Command::SUCCESS;
    }
}
