<?php

namespace App\Console\Commands;

use App\Models\UserProfitLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessRoyaltyMaturity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'royalties:process-maturity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending royalties that have reached maturity (11 months)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing royalty maturity...');

        // Find pending logs older than 11 months
        // Logic: 11 months from creation (profit declaration time for user)
        $maturityDate = now()->subMonths(11);

        $logs = UserProfitLog::where('status', 'pending')
            ->where('created_at', '<=', $maturityDate)
            ->get();

        if ($logs->isEmpty()) {
            $this->info('No royalties ready for maturity.');
            return 0;
        }

        $count = 0;
        foreach ($logs as $log) {
            try {
                $log->update([
                    'status' => 'credited',
                    'credited_at' => now(),
                ]);
                
                // Here we could also trigger a Notification to the user.
                
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to process royalty maturity for Log ID {$log->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$count} royalties to credited status.");
        return 0;
    }
}
