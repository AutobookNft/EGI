<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: User Consents Table
 * 🎯 Purpose: Store user consent history with versioning
 * 🛡️ Privacy: GDPR-compliant consent tracking with audit trail
 * 🧱 Core Logic: Supports consent versioning and withdrawal tracking
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
return new class extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     * @privacy-safe Creates consent tracking table with proper retention
     */
    public function up(): void
    {
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('consent_version_id')->constrained()->onDelete('restrict');

            // Consent details
            $table->string('consent_type', 50); // functional, analytics, marketing, profiling
            $table->boolean('granted');
            $table->string('legal_basis', 50); // consent, legitimate_interest, contract
            $table->string('withdrawal_method', 50)->nullable(); // manual, automatic, bulk

            // Audit trail
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // source, session_id, etc.

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'consent_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['consent_type', 'granted']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
