<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Public\ProjectController as PublicProjects;
use App\Http\Controllers\Subscriber\DashboardController;
use App\Http\Controllers\Subscriber\ProjectController;
use App\Http\Controllers\Subscriber\RewardController as SubscriberRewards;
use App\Http\Controllers\Subscriber\InvestmentController as SubscriberInvestments;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\RazorpayWebhookController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', function () {
    return view('public.faq');
})->name('faq');
Route::get('/projects', [PublicProjects::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [PublicProjects::class, 'show'])->name('projects.show');
Route::get('/page/{slug}', [PublicPageController::class, 'show'])->name('page.show');

// Webhook Routes (CSRF exempt)
Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'handleStripe'])
    ->name('webhooks.stripe');

Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])
    ->name('webhooks.razorpay');

require __DIR__ . '/auth.php';
// admin.php is loaded in bootstrap/app.php

// Subscriber Routes
Route::middleware(['auth', 'verified'])->prefix('app')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('subscriber.dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('subscriber.projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('subscriber.projects.show');
    Route::post('/projects/{project}/invest', [ProjectController::class, 'invest'])->name('subscriber.projects.invest');
    Route::get('/investments/pay/{investment}', [ProjectController::class, 'pay'])->name('subscriber.projects.pay');
    Route::post('/investments/auto', [ProjectController::class, 'autoInvest'])->name('subscriber.projects.auto_invest');
    Route::post('/checkout/investment/{investment}/create', [CheckoutController::class, 'createInvestmentOrder'])->name('checkout.investment.create');

    Route::get('/rewards', [SubscriberRewards::class, 'index'])->name('subscriber.rewards.index');
    Route::get('/referrals', [App\Http\Controllers\Subscriber\ReferralController::class, 'index'])->name('subscriber.referrals.index');

    // Investments & Profits
    Route::get('/investments', [SubscriberInvestments::class, 'index'])->name('subscriber.investments.index');
    Route::get('/profits', [SubscriberInvestments::class, 'profits'])->name('subscriber.profits.index');
    Route::post('/profits/redeem', [App\Http\Controllers\Subscriber\RedemptionController::class, 'store'])->name('subscriber.profits.redeem');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Subscriber\InvoiceController::class, 'download'])->name('subscriber.invoices.download');

    // Billing & Subscription
    Route::get('/billing', [App\Http\Controllers\Subscriber\BillingController::class, 'index'])->name('subscriber.billing.index');
    Route::get('/payments', [App\Http\Controllers\Subscriber\PaymentHistoryController::class, 'index'])->name('subscriber.payments.index');
    Route::get('/subscription', [App\Http\Controllers\Subscriber\SubscriptionController::class, 'index'])->name('subscriber.subscription.index');
    Route::post('/subscription/change-plan', [App\Http\Controllers\Subscriber\SubscriptionController::class, 'changePlan'])->name('subscriber.subscription.change-plan');
    Route::get('/card', [App\Http\Controllers\Subscriber\MembershipCardController::class, 'show'])->name('subscriber.card.show');

    // Profile & Notifications
    Route::get('/notifications', [App\Http\Controllers\Subscriber\NotificationsController::class, 'index'])->name('subscriber.notifications.index');
    Route::get('/profile', [App\Http\Controllers\Subscriber\ProfileController::class, 'index'])->name('subscriber.profile.index');
    Route::patch('/profile', [App\Http\Controllers\Subscriber\ProfileController::class, 'update'])->name('subscriber.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Subscriber\ProfileController::class, 'updatePassword'])->name('subscriber.profile.password');

    // Checkout Routes
    Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{plan}/create', [CheckoutController::class, 'createSubscription'])->name('checkout.create');
    Route::any('/payment/callback', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/status', [CheckoutController::class, 'checkStatus'])->name('checkout.status');

    // SIP Routes
    Route::get('/sip', [App\Http\Controllers\Subscriber\SipController::class, 'index'])->name('subscriber.sip.index');
    Route::get('/sip/create', [App\Http\Controllers\Subscriber\SipController::class, 'create'])->name('subscriber.sip.create');
    Route::post('/sip', [App\Http\Controllers\Subscriber\SipController::class, 'store'])->name('subscriber.sip.store');
    Route::get('/sip/{id}', [App\Http\Controllers\Subscriber\SipController::class, 'show'])->name('subscriber.sip.show');
    Route::get('/sip/{id}/edit', [App\Http\Controllers\Subscriber\SipController::class, 'edit'])->name('subscriber.sip.edit');
    Route::put('/sip/{id}', [App\Http\Controllers\Subscriber\SipController::class, 'update'])->name('subscriber.sip.update');
    Route::post('/sip/{id}/cancel', [App\Http\Controllers\Subscriber\SipController::class, 'cancel'])->name('subscriber.sip.cancel');
    Route::get('/sip/{id}/payment-schedule', [App\Http\Controllers\Subscriber\SipController::class, 'paymentSchedule'])->name('subscriber.sip.payment-schedule');
    Route::get('/sip-payment/{id}', [App\Http\Controllers\Subscriber\SipController::class, 'payment'])->name('subscriber.sip.payment');
    Route::post('/sip-payment/verify', [App\Http\Controllers\Subscriber\SipController::class, 'verify'])->name('subscriber.sip.verify');

    // ROI Simulator
    Route::get('/roi-simulator', function () {
        return view('subscriber.roi-simulator');
    })->name('subscriber.roi-simulator');

    // Refund Routes
    Route::get('/refunds', [App\Http\Controllers\Subscriber\RefundController::class, 'index'])->name('subscriber.refunds.index');
    Route::get('/refunds/create', [App\Http\Controllers\Subscriber\RefundController::class, 'create'])->name('subscriber.refunds.create');
    Route::post('/refunds', [App\Http\Controllers\Subscriber\RefundController::class, 'store'])->name('subscriber.refunds.store');
    Route::get('/refunds/{id}', [App\Http\Controllers\Subscriber\RefundController::class, 'show'])->name('subscriber.refunds.show');

    // Wallet Deposit Routes
    Route::get('/deposit/add', [App\Http\Controllers\Subscriber\DepositController::class, 'create'])->name('subscriber.deposit.create');
    Route::post('/deposit/order', [App\Http\Controllers\Subscriber\DepositController::class, 'store'])->name('subscriber.deposit.store');
    Route::post('/deposit/verify', [App\Http\Controllers\Subscriber\DepositController::class, 'verify'])->name('subscriber.deposit.verify');

});

// DEV: Reset Razorpay Mappings (Temporary)
Route::get('/dev/reset-razorpay', function () {
    \App\Models\SubscriptionPlan::query()->update(['razorpay_plan_id' => null]);
    \App\Models\User::query()->update(['razorpay_customer_id' => null]);
    return 'Razorpay Plan IDs and Customer IDs cleared. Next checkout will regenerate them.';
});
