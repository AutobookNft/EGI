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
        Schema::create('gdpr_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // GDPR action details
            $table->string('action', 100); // consent_updated, data_exported, account_deleted
            $table->string('legal_basis', 50); // consent, legitimate_interest, user_request
            $table->json('details'); // Action-specific details

            // Request context
            $table->json('request_metadata'); // Full request context
            $table->ipAddress('ip_address'); // Masked IP
            $table->text('user_agent');

            // Legal compliance
            $table->timestamp('timestamp'); // Explicit timestamp for legal records
            $table->timestamp('retention_until')->nullable(); // Legal retention period
            $table->text('compliance_note'); // Reference to GDPR article/requirement

            // Immutability protection
            $table->string('record_hash', 64); // Hash of record for tamper detection
            $table->boolean('is_verified')->default(true);

            // Timestamps (separate from business timestamp)
            $table->timestamps();

            // Indexes for compliance reporting
            $table->index(['user_id', 'action']);
            $table->index(['user_id', 'timestamp']);
            $table->index(['action', 'timestamp']);
            $table->index(['legal_basis', 'timestamp']);
            $table->index('timestamp');
            $table->index('retention_until');

            // Ensure records cannot be updated (immutable)
            // This will be enforced at the model level
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_audit_logs');
    }
};
