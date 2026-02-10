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
            $table->decimal('roi_percentage', 5, 2)->nullable()->after('fund_goal');
            $table->integer('duration_months')->nullable()->after('roi_percentage');
            $table->string('location')->nullable()->after('duration_months');
            $table->string('image_url')->nullable()->after('location');
            $table->boolean('is_featured')->default(false)->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['roi_percentage', 'duration_months', 'location', 'image_url', 'is_featured']);
        });
    }
};
