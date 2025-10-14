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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform');
            $table->string('event_type'); // started, renewed, upgraded, downgraded, cancelled, expired, trial_started, trial_expired
            $table->string('status'); // Status after event
            $table->json('package_data'); // Package data at time of event
            $table->json('changes')->nullable(); // What changed (old vs new)
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('webhook_payload')->nullable(); // Full webhook data for reference
            $table->timestamps();

            $table->index(['subscription_id', 'created_at']);
            $table->index(['user_id', 'platform']);
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
