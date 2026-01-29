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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('stripe_subscription_id')->nullable()->after('plan_id');
            $table->timestamp('grace_until')->nullable()->after('ends_at');
            $table->unsignedTinyInteger('retry_count')->default(0)->after('grace_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['stripe_subscription_id', 'grace_until', 'retry_count']);
        });
    }
};
