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
        // Add Razorpay fields to subscription_plans
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('razorpay_plan_id')->nullable()->after('slug');
        });

        // Add Razorpay fields to user_subscriptions
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('razorpay_subscription_id')->nullable()->after('id');
            $table->string('razorpay_customer_id')->nullable()->after('razorpay_subscription_id');
            $table->timestamp('current_period_start')->nullable()->after('ends_at');
            $table->timestamp('current_period_end')->nullable()->after('current_period_start');
            $table->string('cancel_reason')->nullable()->after('grace_until');
            $table->boolean('cancel_at_period_end')->default(false)->after('cancel_reason');
        });

        // Add Razorpay customer ID to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('razorpay_customer_id')->nullable()->after('email');
            $table->string('phone')->nullable()->after('razorpay_customer_id');
        });

        // Add Razorpay fields to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->string('razorpay_payment_id')->nullable()->after('gateway_transaction_id');
            $table->string('razorpay_subscription_id')->nullable()->after('razorpay_payment_id');
            $table->string('razorpay_invoice_id')->nullable()->after('razorpay_subscription_id');
        });

        // Add Razorpay invoice ID to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('razorpay_invoice_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('razorpay_plan_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_subscription_id',
                'razorpay_customer_id',
                'current_period_start',
                'current_period_end',
                'cancel_reason',
                'cancel_at_period_end',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['razorpay_customer_id', 'phone']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_payment_id',
                'razorpay_subscription_id',
                'razorpay_invoice_id',
            ]);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('razorpay_invoice_id');
        });
    }
};
