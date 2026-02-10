<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\ActivityLog;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find active subscriptions where ends_at is in the past
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            
            ActivityLog::create([
                'user_id' => $subscription->user_id,
                'action' => 'subscription.expired',
                'description' => 'Subscription expired automatically.',
                'user_agent' => 'System',
            ]);

            $count++;
        }

        $this->info("Expired {$count} subscriptions.");
    }
}
