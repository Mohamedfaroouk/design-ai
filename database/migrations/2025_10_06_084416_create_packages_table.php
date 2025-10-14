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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Used by Salla to identify package
            $table->string('display_name'); // Display name in UI
            $table->text('description')->nullable();
            $table->string('platform'); // salla, zid, wordpress, or 'all' for all platforms
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('SAR');
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, lifetime
            $table->integer('photos_limit')->default(0); // Number of photos allowed
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('features')->nullable(); // Additional features as JSON
            $table->json('metadata')->nullable(); // Platform-specific data
            $table->timestamps();

            $table->index(['platform', 'is_active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
