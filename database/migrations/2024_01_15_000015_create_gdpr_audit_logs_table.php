<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: GDPR Audit Logs Table
 * 🎯 Purpose: Specialized audit trail for GDPR compliance
 * 🛡️ Privacy: Article 30 compliant record of processing activities
 * 🧱 Core Logic: Immutable GDPR action logging for legal compliance
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
     * @privacy-safe Creates GDPR-specific audit table
     */
    public function up(): void
    {
        // Drop existing table if exists
        Schema::dropIfExists('gdpr_audit_logs');

        // Create table aligned with GdprAuditLog model fillable
        Schema::create('gdpr_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // GDPR action details (aligned with model)
            $table->string('action_type', 100); // Model uses action_type
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('legal_basis', 100); // consent, legitimate_interest, user_request

            // Data subject details
            $table->unsignedBigInteger('data_subject_id')->nullable(); // Can be different from user_id
            $table->string('data_controller', 255)->nullable();
            $table->string('data_processor', 255)->nullable();
            $table->string('purpose_of_processing', 500)->nullable();

            // Data categories (JSON arrays as per model casts)
            $table->json('data_categories')->nullable();
            $table->json('recipient_categories')->nullable();
            $table->json('international_transfers')->nullable();
            $table->string('retention_period', 100)->nullable(); // Model uses string, not timestamp
            $table->json('security_measures')->nullable();

            // Context and request data
            $table->json('context_data')->nullable(); // Model uses context_data, not details
            $table->ipAddress('ip_address');
            $table->text('user_agent');

            // Immutability protection
            $table->string('checksum', 64); // Model uses checksum, not record_hash

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'action_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['legal_basis', 'created_at']);
            $table->index(['data_subject_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_audit_logs');
    }
};
