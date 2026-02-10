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
            $table->enum('participation_mode', ['auto', 'manual'])->default('auto')->after('role_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0)->after('plan_id');
            $table->decimal('allocated_amount', 10, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('participation_mode');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'allocated_amount']);
        });
    }
};
