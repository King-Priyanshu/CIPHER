<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireGracePeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire-grace-periods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions where the grace period has ended';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for expired grace periods...');

        // Find subscriptions where grace period has ended
        $expiredSubscriptions = UserSubscription::where('status', 'past_due')
            ->whereNotNull('grace_until')
            ->where('grace_until', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired grace periods found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredSubscriptions->count()} subscription(s) to expire.");

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
                'grace_until' => null,
            ]);

            // Log the expiration
            ActivityLog::create([
                'user_id' => $subscription->user_id,
                'action' => 'subscription.expired',
                'description' => 'Subscription expired after grace period ended.',
                'ip_address' => null,
                'user_agent' => 'System Scheduler',
            ]);

            Log::info('Subscription expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);

            $this->line("Expired subscription #{$subscription->id} for user #{$subscription->user_id}");

            // TODO: Send expiration notification to user
        }

        $this->info("Expired {$expiredSubscriptions->count()} subscription(s).");

        return Command::SUCCESS;
    }
}
