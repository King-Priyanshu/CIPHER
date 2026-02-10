<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Stripe customer ID to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->after('razorpay_customer_id');
            }
        });

        // Add Stripe payment intent ID to user subscriptions
        Schema::table('user_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_subscriptions', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->after('stripe_subscription_id');
            }
        });

        // Add Razorpay + Stripe payment fields to project_investments
        Schema::table('project_investments', function (Blueprint $table) {
            if (!Schema::hasColumn('project_investments', 'razorpay_order_id')) {
                $table->string('razorpay_order_id')->nullable();
            }
            if (!Schema::hasColumn('project_investments', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id')->nullable();
            }
            if (!Schema::hasColumn('project_investments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable();
            }
        });

        // Add Stripe payment fields to payments
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            }
            if (!Schema::hasColumn('payments', 'razorpay_subscription_id')) {
                $table->string('razorpay_subscription_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable();
            }
        });

        // Add Stripe product and price IDs to subscription plans
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_plans', 'stripe_product_id')) {
                $table->string('stripe_product_id')->nullable()->after('razorpay_plan_id');
            }
            if (!Schema::hasColumn('subscription_plans', 'stripe_price_id')) {
                $table->string('stripe_price_id')->nullable()->after('stripe_product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('stripe_payment_intent_id');
        });

        Schema::table('project_investments', function (Blueprint $table) {
            $table->dropColumn(['razorpay_order_id', 'razorpay_payment_id', 'stripe_payment_intent_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['razorpay_payment_id', 'razorpay_subscription_id', 'stripe_payment_intent_id', 'stripe_subscription_id']);
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['stripe_product_id', 'stripe_price_id']);
        });
    }
};
