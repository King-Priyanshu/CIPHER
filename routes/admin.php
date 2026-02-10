<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\FundPoolController;
use App\Http\Controllers\Admin\RewardPoolController;
use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\ProfitDistributionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\InvestmentPlanController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\AnalyticsController;

use App\Http\Controllers\Admin\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for the administrative panel.
|
*/

// Admin Authentication (Guest)
Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [AdminLoginController::class, 'store']);
});

// Admin Panel (Authenticated)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UserController::class);
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::post('users/{user}/wallet/adjust', [WalletController::class, 'store'])->name('users.wallet.adjust');
    Route::resource('projects', ProjectController::class);
    Route::resource('plans', SubscriptionPlanController::class);
    Route::resource('pools', FundPoolController::class);
    Route::resource('reward-pools', RewardPoolController::class);
    Route::resource('pages', ContentPageController::class);

    // Investment Plans
    Route::resource('investment-plans', InvestmentPlanController::class);

    // Referrals
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('referrals/generate/{user?}', [ReferralController::class, 'generate'])->name('referrals.generate');

    // Investment & Profit Management
    Route::get('investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::post('investments/allocate', [InvestmentController::class, 'allocate'])->name('investments.allocate');
    Route::post('investments/auto-allocate', [InvestmentController::class, 'autoAllocate'])->name('investments.auto-allocate');
    Route::post('investments/activate/{project}', [InvestmentController::class, 'activateProject'])->name('investments.activate');
    
    Route::get('profits', [ProfitDistributionController::class, 'index'])->name('profits.index');
    Route::get('profits/create', [ProfitDistributionController::class, 'create'])->name('profits.create');
    Route::post('profits', [ProfitDistributionController::class, 'store'])->name('profits.store');
    Route::get('profits/{profit}', [ProfitDistributionController::class, 'show'])->name('profits.show');
    Route::post('profits/{profit}/distribute', [ProfitDistributionController::class, 'distribute'])->name('profits.distribute');

    // Payments & Invoices
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('invoices/generate/{payment}', [InvoiceController::class, 'generate'])->name('invoices.generate');

    // Audit Logs
    Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    // Route::get('audit-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('audit-logs.show'); // If I implement show

    // Settings
    Route::get('settings/payment-gateway', [SettingsController::class, 'paymentGateway'])->name('settings.payment-gateway');
    Route::put('settings/payment-gateway', [SettingsController::class, 'updatePaymentGateway'])->name('settings.payment-gateway.update');

    // Finance & Refunds
    Route::get('finance', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::get('finance/export/csv', [FinanceController::class, 'exportCsv'])->name('finance.export.csv');
    Route::get('finance/export/pdf', [FinanceController::class, 'exportPdf'])->name('finance.export.pdf');
    Route::get('finance/transactions/export/csv', [FinanceController::class, 'exportCsv'])->name('finance.transactions.export.csv');
    Route::get('finance/transactions/export/pdf', [FinanceController::class, 'exportPdf'])->name('finance.transactions.export.pdf');
    Route::get('finance/refunds/export/csv', [FinanceController::class, 'exportRefundsCsv'])->name('finance.refunds.export.csv');
    Route::get('finance/refunds/export/pdf', [FinanceController::class, 'exportRefundsPdf'])->name('finance.refunds.export.pdf');
    Route::get('refunds', [FinanceController::class, 'refunds'])->name('refunds.index');
    Route::post('refunds/{refund}/approve', [FinanceController::class, 'approveRefund'])->name('refunds.approve');
    Route::post('refunds/{refund}/reject', [FinanceController::class, 'rejectRefund'])->name('refunds.reject');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
});
