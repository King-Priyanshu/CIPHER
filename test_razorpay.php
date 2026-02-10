<?php

use App\Services\Payment\RazorpayService;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Razorpay Configuration...\n";
$key = config('services.razorpay.key');
$secret = config('services.razorpay.secret');

echo "Key: " . ($key ? substr($key, 0, 8) . '...' : 'NULL') . "\n";
echo "Secret: " . ($secret ? 'SET' : 'NULL') . "\n";

if (!$key || !$secret) {
    echo "ERROR: Razorpay keys are missing in config.\n";
    exit(1);
}

echo "Testing Razorpay API Connection (GET /plans)...\n";

try {
    $service = new RazorpayService();
    // We can't access protect method 'request' directly, but we can try to call a public method.
    // However, the service doesn't have a simple 'ping' method.
    // Let's reflect to call 'request' or just try to create a plan for a dummy model if we want to be safe,
    // OR just use Http facade directly here to see if *that* works.
    
    $response = \Illuminate\Support\Facades\Http::withBasicAuth($key, $secret)
        ->get('https://api.razorpay.com/v1/plans');

    echo "Response Status: " . $response->status() . "\n";
    
    if ($response->successful()) {
        echo "SUCCESS: Connected to Razorpay.\n";
        echo "Plans found: " . count($response->json()['items'] ?? []) . "\n";
    } else {
        echo "FAILED: API returned error.\n";
        print_r($response->json());
    }

} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
