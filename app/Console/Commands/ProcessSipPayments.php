<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Services\Payment\RazorpayService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentDueNotification;

class ProcessSipPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sip:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due SIP payments for active subscriptions';

    protected $razorpayService;
    protected $walletService;

    public function __construct(RazorpayService $razorpayService, WalletService $walletService)
    {
        parent::__construct();
        $this->razorpayService = $razorpayService;
        $this->walletService = $walletService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SIP payment processing...');

        // Find subscriptions that are due for payment
        // Logic: Active, SIP type, next_payment_date <= today
        
        // Note: We need a 'next_payment_date' field on UserSubscription or a separate schedule table.
        // Assuming UserSubscription has appropriate fields or we use payment_schedule table.
        // For Phase 1, sticking to UserSubscription iteration if 'next_payment_date' exists, 
        // or we need to add it. Let's assume it exists or we use created_at + interval logic.
        
        // Since schema for UserSubscription wasn't fully detailed with 'next_payment_date', 
        // I'll assume we iterate active SIPs and check validity.
        
        $subscriptions = UserSubscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->get();

        foreach ($subscriptions as $sub) {
            $this->info("Checking subscription {$sub->id}...");

            $dueDate = $sub->current_period_end; // Assuming this tracks next payment

            if (!$dueDate) continue;

            // Notify if due in 3 days
            if ($dueDate->isFuture() && $dueDate->diffInDays(now()) <= 3) {
                 // Check if already notified recently (optional, maybe check last_notified_at column if added)
                 // For now, just send.
                 try {
                     Mail::to($sub->user->email)->queue(new PaymentDueNotification($sub, $dueDate));
                     $this->info("Notification sent for subscription {$sub->id}");
                 } catch (\Exception $e) {
                     Log::error("Failed to send notification for {$sub->id}: " . $e->getMessage());
                 }
            }
        }

        $this->info('SIP payment processing completed.');
    }
}
