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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('business_type')->nullable()->after('description');
            $table->text('royalty_model')->nullable()->after('business_type');
            $table->string('visibility_status')->default('visible')->after('status'); // visible, hidden
            $table->enum('allocation_eligibility', ['manual_only', 'auto_only', 'both'])->default('both')->after('visibility_status');
            // Ensure status has 'paused' if not already (handling via string column if strict enum used, 
            // but we'll assume we can just rely on application logic or modify enum if feasible.
            // Since explicit enum was setup in previous migration, modifying it is complex in some DBs.
            // We'll stick to 'status' string checks in app logic for 'paused' or use 'visibility_status'.
            // Actually, let's keep it simple.
        });

        Schema::table('project_investments', function (Blueprint $table) {
             $table->enum('allocation_type', ['manual', 'auto'])->default('manual')->after('amount');
             $table->foreignId('admin_id')->nullable()->after('allocation_type')->constrained('users')->nullOnDelete();
        });

        Schema::table('profit_distributions', function (Blueprint $table) {
            $table->date('month')->nullable()->after('project_id'); // To track the specific month
            $table->json('supporting_documents')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'royalty_model', 'visibility_status', 'allocation_eligibility']);
        });

        Schema::table('project_investments', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['allocation_type', 'admin_id']);
        });

        Schema::table('profit_distributions', function (Blueprint $table) {
            $table->dropColumn(['month', 'supporting_documents']);
        });
    }
};
