<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Create EGI Reservation Certificates Table
 * 🎯 Purpose: Store certificate data for EGI reservations
 * 🛡️ GDPR: Contains wallet_address (pseudonymized identifier)
 *
 * @return void
 *
 * @oracode-flow
 * 1. Create table with all fields
 * 2. Set up foreign keys
 * 3. Add appropriate indexes
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egi_reservation_certificates', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('egi_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Optional for weak reservations

            // Certificate data
            $table->string('wallet_address', 58); // Algorand address is 58 chars
            $table->enum('reservation_type', ['strong', 'weak']);
            $table->decimal('offer_amount_fiat', 10, 2);
            $table->decimal('offer_amount_algo', 18, 8);
            $table->char('certificate_uuid', 36)->unique(); // Modificato da uuid a char(36)
            $table->string('signature_hash', 64); // SHA-256 is 64 chars

            // Status flags
            $table->boolean('is_superseded')->default(false);
            $table->boolean('is_current_highest')->default(true);

            // Storage paths
            $table->string('pdf_path')->nullable();
            $table->string('public_url')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations')
                  ->onDelete('cascade');

            $table->foreign('egi_id')
                  ->references('id')
                  ->on('egis')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes for performance
            $table->index('certificate_uuid');
            $table->index(['egi_id', 'is_current_highest']);
            $table->index('wallet_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_reservation_certificates');
    }
};
