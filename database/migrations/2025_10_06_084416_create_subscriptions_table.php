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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform'); // salla, zid, wordpress
            $table->string('merchant_id')->nullable(); // Store merchant_id for platform
            $table->string('subscription_id')->nullable(); // Platform subscription ID
            $table->string('status')->default('active'); // active, cancelled, expired, trial
            $table->json('package_data'); // Store full package data snapshot
            $table->integer('photos_limit')->default(0); // Current photos limit
            $table->integer('photos_used')->default(0); // Photos used by user
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable(); // Platform-specific subscription data
            $table->timestamps();

            $table->unique(['user_id', 'platform']); // One subscription per user per platform
            $table->index(['status', 'end_date']);
            $table->index('merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
