<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Subscription & Payment Scheduled Tasks
Schedule::command('subscriptions:expire-grace-periods')->daily();
Schedule::command('subscriptions:expire')->daily(); // New Command
Schedule::command('rewards:distribute')->weekly();
Schedule::command('sitemap:generate')->weekly();
