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
        Schema::create('perks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_card_id')->constrained()->onDelete('cascade');
            $table->enum('perk_type', ['travel', 'food', 'shopping', 'medical', 'education']);
            $table->decimal('discount_percentage', 5, 2); // e.g., 10.00
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
        Schema::dropIfExists('perks');
    }
};
