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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade');

            // Reservation details
            $table->enum('type', ['weak', 'strong'])->default('weak');
            $table->enum('status', ['active', 'expired', 'completed', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable(); // Null for strong reservations

            // For strong reservations, store additional contact data
            $table->json('contact_data')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'egi_id']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
