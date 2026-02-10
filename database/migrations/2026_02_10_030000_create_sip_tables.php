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
        Schema::create('sips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('frequency', ['weekly', 'monthly'])->default('monthly');
            $table->date('start_date');
            $table->integer('duration');
            $table->boolean('auto_pay')->default(false);
            $table->enum('status', ['active', 'cancelled', 'completed', 'paused'])->default('active');
            $table->timestamps();
        });

        Schema::create('sip_payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sip_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sip_payment_schedules');
        Schema::dropIfExists('sips');
    }
};
