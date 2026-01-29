<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Show payment gateway settings.
     */
    public function paymentGateway()
    {
        $settings = [
            'razorpay_key' => config('services.razorpay.key'),
            'razorpay_configured' => !empty(config('services.razorpay.key')) && !empty(config('services.razorpay.secret')),
            'webhook_url' => url('/webhooks/razorpay'),
        ];

        return view('admin.settings.payment-gateway', compact('settings'));
    }

    /**
     * Update payment gateway settings.
     */
    public function updatePaymentGateway(Request $request)
    {
        $request->validate([
            'razorpay_key' => 'nullable|string',
            'razorpay_secret' => 'nullable|string',
            'razorpay_webhook_secret' => 'nullable|string',
        ]);

        // Update .env file
        $this->updateEnv([
            'RAZORPAY_KEY_ID' => $request->razorpay_key,
            'RAZORPAY_KEY_SECRET' => $request->razorpay_secret,
            'RAZORPAY_WEBHOOK_SECRET' => $request->razorpay_webhook_secret,
        ]);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'Payment gateway settings updated successfully.');
    }

    /**
     * Update .env file.
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if ($value === null) continue;

            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
