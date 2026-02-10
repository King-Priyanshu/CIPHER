<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show payment gateway settings.
     */
    public function paymentGateway()
    {
        $settings = [
            'razorpay_key' => Setting::get('razorpay.key') ?? config('services.razorpay.key'),
            'razorpay_secret' => Setting::get('razorpay.secret') ?? config('services.razorpay.secret'),
            'razorpay_webhook_secret' => Setting::get('razorpay.webhook_secret') ?? config('services.razorpay.webhook_secret'),
            'razorpay_configured' => !empty(Setting::get('razorpay.key') ?? config('services.razorpay.key')),
            'webhook_url' => route('webhooks.razorpay'),
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

        if ($request->filled('razorpay_key')) {
            Setting::set('razorpay.key', $request->input('razorpay_key'));
        }

        if ($request->filled('razorpay_secret')) {
            Setting::set('razorpay.secret', $request->input('razorpay_secret'));
        }

        if ($request->filled('razorpay_webhook_secret')) {
            Setting::set('razorpay.webhook_secret', $request->input('razorpay_webhook_secret'));
        }

        return redirect()->back()->with('success', 'Payment gateway settings updated successfully.');
    }
}
