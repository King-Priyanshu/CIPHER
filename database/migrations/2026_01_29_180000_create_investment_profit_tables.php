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
        // Project Investments - Auto-allocated user investments
        Schema::create('project_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('user_subscriptions')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('allocated'); // allocated, active, withdrawn
            $table->timestamp('allocated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'project_id']);
            $table->index('status');
        });

        // Profit Distributions - Admin-declared project profits
        Schema::create('profit_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('total_profit', 12, 2);
            $table->decimal('distributed_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, distributing, completed
            $table->timestamp('declared_at')->nullable();
            $table->timestamp('distributed_at')->nullable();
            $table->foreignId('declared_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        // User Profit Logs - Individual user profit records
        Schema::create('user_profit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profit_distribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_investment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending'); // pending, credited, withdrawn
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Razorpay Webhooks - Audit log for transparency
        Schema::create('razorpay_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->json('payload');
            $table->string('status')->default('received'); // received, processed, failed
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razorpay_webhooks');
        Schema::dropIfExists('user_profit_logs');
        Schema::dropIfExists('profit_distributions');
        Schema::dropIfExists('project_investments');
    }
};
