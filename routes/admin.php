<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\FundPoolController;
use App\Http\Controllers\Admin\RewardPoolController;
use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\UserController;

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
    Route::resource('projects', ProjectController::class);
    Route::resource('plans', SubscriptionPlanController::class);
    Route::resource('pools', FundPoolController::class);
    Route::resource('reward-pools', RewardPoolController::class);
    Route::resource('pages', ContentPageController::class);
});
