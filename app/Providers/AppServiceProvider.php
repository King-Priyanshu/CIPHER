<?php

namespace App\Providers;

use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind payment gateway interface to implementation
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $gateway = config('services.payment_gateway', 'stripe');

            return match ($gateway) {
                'razorpay' => $app->make(\App\Services\Payments\RazorpayPaymentGateway::class),
                default => $app->make(StripePaymentGateway::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
