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
            $table->string('razorpay_order_id')->nullable()->after('plan_id');
            $table->string('razorpay_subscription_id')->nullable()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('razorpay_order_id')->nullable()->after('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('razorpay_order_id');
            // We cannot easily revert nullable->change() without knowing original state, roughly:
            // $table->string('razorpay_subscription_id')->nullable(false)->change(); 
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('razorpay_order_id');
        });
    }
};
