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
        Schema::table('project_investments', function (Blueprint $table) {
            $table->foreignId('investment_plan_id')->nullable()->constrained('investment_plans')->nullOnDelete();
            $table->date('roi_start_date')->nullable();
            $table->date('roi_end_date')->nullable();
            $table->decimal('total_roi_earned', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_investments', function (Blueprint $table) {
            $table->dropForeign(['investment_plan_id']);
            $table->dropColumn(['investment_plan_id', 'roi_start_date', 'roi_end_date', 'total_roi_earned']);
        });
    }
};
