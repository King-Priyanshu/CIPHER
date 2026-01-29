<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Subscriber\DashboardController as SubscriberDashboard;
use App\Http\Controllers\Subscriber\ProjectController as SubscriberProjects;
use App\Http\Controllers\Subscriber\RewardController as SubscriberRewards;
use App\Http\Controllers\Subscriber\InvestmentController as SubscriberInvestments;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\RazorpayWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', function () { return view('public.faq'); })->name('faq');
Route::get('/page/{slug}', [PublicPageController::class, 'show'])->name('page.show');

// Webhook Routes (CSRF exempt)
Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'handleStripe'])
    ->name('webhooks.stripe')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])
    ->name('webhooks.razorpay')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

// Subscriber Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SubscriberDashboard::class, 'index'])->name('subscriber.dashboard');
    Route::get('/projects', [SubscriberProjects::class, 'index'])->name('subscriber.projects.index');
    Route::get('/rewards', [SubscriberRewards::class, 'index'])->name('subscriber.rewards.index');
    
    // Investments & Profits
    Route::get('/investments', [SubscriberInvestments::class, 'index'])->name('subscriber.investments.index');
    Route::get('/profits', [SubscriberInvestments::class, 'profits'])->name('subscriber.profits.index');
    
    // Billing & Subscription
    Route::get('/billing', [App\Http\Controllers\Subscriber\BillingController::class, 'index'])->name('subscriber.billing.index');
    Route::get('/subscription', [App\Http\Controllers\Subscriber\SubscriptionController::class, 'index'])->name('subscriber.subscription.index');
    
    // Profile & Notifications
    Route::get('/notifications', [App\Http\Controllers\Subscriber\NotificationsController::class, 'index'])->name('subscriber.notifications.index');
    Route::get('/profile', [App\Http\Controllers\Subscriber\ProfileController::class, 'index'])->name('subscriber.profile.index');
    Route::patch('/profile', [App\Http\Controllers\Subscriber\ProfileController::class, 'update'])->name('subscriber.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Subscriber\ProfileController::class, 'updatePassword'])->name('subscriber.profile.password');

    // Checkout Routes
    Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{plan}/create', [CheckoutController::class, 'createSubscription'])->name('checkout.create');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/status', [CheckoutController::class, 'checkStatus'])->name('checkout.status');
});
