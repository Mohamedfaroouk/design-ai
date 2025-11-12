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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('prompt')->nullable(); // AI prompt used
            $table->string('style')->nullable(); // AI style applied
            $table->string('image_url'); // Generated image URL
            $table->string('status')->default('pending'); // 'pending', 'completed', 'failed'
            $table->json('metadata')->nullable(); // Additional AI generation data
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
