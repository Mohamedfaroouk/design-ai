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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform')->default('others'); // 'salla', 'zid', 'others'
            $table->string('platform_product_id')->nullable(); // External platform product ID
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_url')->nullable(); // Original product image
            $table->json('metadata')->nullable(); // Additional platform-specific data
            $table->timestamps();

            // Ensure no duplicate products per platform per user
            $table->unique(['user_id', 'platform', 'platform_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
