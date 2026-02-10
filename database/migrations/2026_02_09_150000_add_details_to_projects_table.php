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
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium')->after('description');
            $table->text('outcome_description')->nullable()->after('risk_level');
            $table->json('images')->nullable()->after('outcome_description');
            $table->json('documents')->nullable()->after('images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['risk_level', 'outcome_description', 'images', 'documents']);
        });
    }
};
