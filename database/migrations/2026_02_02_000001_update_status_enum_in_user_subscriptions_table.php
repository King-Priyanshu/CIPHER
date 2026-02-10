<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('active', 'cancelled', 'expired', 'trialing', 'past_due', 'pending', 'paused', 'suspended') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // We'll map the new statuses to a fallback before reverting schema
            DB::statement("UPDATE user_subscriptions SET status = 'active' WHERE status IN ('pending', 'paused', 'suspended')");
            DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('active', 'cancelled', 'expired', 'trialing', 'past_due')");
        }
    }
};
