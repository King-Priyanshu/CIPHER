<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('payment_reminders_enabled')->default(true)->after('referral_code');
            $table->string('payment_reminder_method')->default('email')->after('payment_reminders_enabled');
            $table->integer('payment_reminder_days')->default(3)->after('payment_reminder_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_reminders_enabled', 'payment_reminder_method', 'payment_reminder_days']);
        });
    }
};
