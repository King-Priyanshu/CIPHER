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
        Schema::create('investment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['sip', 'onetime'])->default('onetime');
            $table->decimal('min_investment', 10, 2);
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly'])->nullable(); // For SIP
            $table->integer('duration_months')->nullable();
            $table->decimal('expected_return_percentage', 5, 2)->nullable();
            $table->enum('refund_rule', ['full', 'partial', 'none'])->default('full');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_plans');
    }
};
