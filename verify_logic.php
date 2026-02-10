<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use App\Services\InvestmentAllocationService;

echo "\n--- Verifying Subscription Plans ---\n";
$plans = SubscriptionPlan::where('is_active', true)->get();
foreach ($plans as $plan) {
    echo "Plan: {$plan->name} ({$plan->slug}) - {$plan->currency} {$plan->price}\n";
}

echo "\n--- Verifying Maturity Logic ---\n";
try {
    // Create a dummy user if not exists
    $user = User::first();
    if (!$user) {
        $user = User::factory()->create();
    }
    
    $plan = SubscriptionPlan::where('slug', 'daily-saver')->first();
    if (!$plan) {
        echo "Error: Daily Saver plan not found!\n";
        exit(1);
    }
    
    // Test activateSubscription logic
    $service = app(SubscriptionService::class);
    // Mock the dependency inside service if needed, but for now real one is fine if DB is up
    
    echo "Activating subscription for User ID: {$user->id}...\n";
    $subscription = $service->activateSubscription($user->id, $plan->id);
    
    echo "Subscription ID: {$subscription->id}\n";
    echo "Starts At: " . $subscription->starts_at->toDateTimeString() . "\n";
    echo "Maturity Date: " . ($subscription->maturity_date ? $subscription->maturity_date->toDateTimeString() : "NULL") . "\n";
    
    $expectedMaturity = now()->addMonths(11);
    $diffInSeconds = $subscription->maturity_date->diffInSeconds($expectedMaturity);
    
    if ($diffInSeconds < 60) {
        echo "SUCCESS: Maturity date is correctly set to 11 months from now.\n";
    } else {
        echo "FAILURE: Maturity date mismatch. Expected ~{$expectedMaturity}, got {$subscription->maturity_date}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
