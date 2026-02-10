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
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds', 'project_investment_id')) {
                $table->foreignId('project_investment_id')->nullable()->constrained('project_investments')->nullOnDelete();
            }
        });

        Schema::table('refunds', function (Blueprint $table) {
             if (!Schema::hasColumn('refunds', 'subscription_id')) {
                 $table->foreignId('subscription_id')->nullable()->after('project_investment_id')->constrained('user_subscriptions')->nullOnDelete();
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            if (Schema::hasColumn('refunds', 'subscription_id')) {
                // Check if foreign key exists if possible, but dropForeign on column usually works if column exists
                try {
                    $table->dropForeign(['subscription_id']);
                } catch (\Exception $e) {
                    // ignore if foreign key doesn't exist
                }
                $table->dropColumn('subscription_id');
            }
        });
    }
};
