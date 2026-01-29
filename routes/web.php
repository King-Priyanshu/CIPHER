<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Subscriber\DashboardController as SubscriberDashboard;
use App\Http\Controllers\Subscriber\ProjectController as SubscriberProjects;
use App\Http\Controllers\Subscriber\RewardController as SubscriberRewards;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/page/{slug}', [PublicPageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

// Subscriber Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SubscriberDashboard::class, 'index'])->name('subscriber.dashboard');
    Route::get('/projects', [SubscriberProjects::class, 'index'])->name('subscriber.projects.index');
    Route::get('/rewards', [SubscriberRewards::class, 'index'])->name('subscriber.rewards.index');
    
    // Checkout Routes
    Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{plan}', [CheckoutController::class, 'process'])->name('checkout.process');
});
