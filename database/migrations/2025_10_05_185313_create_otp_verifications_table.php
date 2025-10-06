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
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index(); // Can be email or phone
            $table->string('channel')->default('email'); // email or phone
            $table->string('otp', 6);
            $table->string('type')->default('password_reset'); // password_reset, email_verification, etc.
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
