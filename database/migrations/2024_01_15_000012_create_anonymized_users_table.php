<?php
// database/migrations/2024_01_15_000012_create_anonymized_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Anonymized Users Table
 * 🎯 Purpose: Track anonymized user data for GDPR compliance
 * 🛡️ Privacy: GDPR Article 17 right to erasure implementation
 * 🧱 Core Logic: Maintain statistical data while protecting privacy
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2024-01-15
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @privacy-safe Creates anonymized user tracking table
     */
    public function up(): void
    {
        Schema::create('anonymized_users', function (Blueprint $table) {
            $table->id();

            // Original user identification
            $table->string('original_user_id', 100)->unique(); // Original user ID (encrypted)
            $table->string('anonymization_id', 100)->unique(); // New anonymous identifier
            $table->string('pseudonym', 50)->unique()->nullable(); // Human-readable pseudonym

            // Anonymization process tracking
            $table->timestamp('anonymized_at');
            $table->enum('anonymization_reason', [
                'user_request',         // User requested deletion
                'account_closure',      // Account closed
                'consent_withdrawal',   // Consent withdrawn
                'retention_expired',    // Data retention period expired
                'legal_requirement',    // Legal obligation
                'admin_action',         // Administrative action
                'automatic_cleanup'     // Automated data cleanup
            ]);

            $table->enum('anonymization_method', [
                'full_anonymization',   // Complete data removal
                'pseudonymization',     // Replace with pseudonyms
                'statistical_anonymization', // Keep aggregated stats only
                'selective_anonymization'    // Remove specific fields only
            ]);

            // Processing details
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('anonymization_steps')->nullable(); // Steps performed
            $table->json('fields_anonymized')->nullable(); // Which fields were anonymized
            $table->json('data_preserved')->nullable(); // What data was preserved

            // Original user metadata (anonymized)
            $table->date('original_registration_date')->nullable();
            $table->date('original_last_login')->nullable();
            $table->string('original_user_type', 50)->nullable(); // creator, collector, etc.
            $table->string('original_subscription_level', 50)->nullable();
            $table->integer('original_activity_score')->nullable();

            // Aggregated statistics (safe to keep)
            $table->integer('total_collections_created')->default(0);
            $table->integer('total_egis_created')->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_transaction_value', 15, 2)->default(0.00);
            $table->integer('total_logins')->default(0);
            $table->integer('days_active')->default(0);

            // Geographic data (anonymized to region level)
            $table->string('region', 100)->nullable(); // EU, North America, Asia, etc.
            $table->string('country_code', 3)->nullable(); // ISO country code only
            $table->string('timezone', 50)->nullable();

            // Technical metadata (anonymized)
            $table->json('device_categories')->nullable(); // mobile, desktop, tablet (aggregated)
            $table->json('browser_families')->nullable(); // Chrome, Firefox, etc. (aggregated)
            $table->string('preferred_language', 5)->nullable();

            // Legal and compliance
            $table->json('consent_history_summary')->nullable(); // Summary of consent given/withdrawn
            $table->json('gdpr_requests_summary')->nullable(); // Summary of GDPR requests
            $table->boolean('had_security_incidents')->default(false);
            $table->timestamp('last_privacy_policy_accepted')->nullable();

            // Verification and integrity
            $table->string('verification_hash', 64); // Hash to verify anonymization completeness
            $table->boolean('anonymization_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');

            // Data retention for anonymized records
            $table->timestamp('expires_at')->nullable(); // When to delete this anonymized record
            $table->enum('retention_reason', [
                'statistical_analysis',
                'regulatory_requirement',
                'business_intelligence',
                'fraud_prevention',
                'research_purposes'
            ])->nullable();

            // Related records management
            $table->json('related_records_anonymized')->nullable(); // Other records that were anonymized
            $table->json('external_references')->nullable(); // External systems that need updates
            $table->boolean('blockchain_references_updated')->default(false);

            // Quality and audit
            $table->enum('anonymization_quality', [
                'basic',        // Basic anonymization
                'enhanced',     // Enhanced anonymization with k-anonymity
                'differential', // Differential privacy applied
                'certified'     // Certified anonymization process
            ])->default('basic');

            $table->text('quality_notes')->nullable();
            $table->json('audit_trail')->nullable(); // Detailed audit trail

            // Recovery and rollback (if needed for legal purposes)
            $table->boolean('is_recoverable')->default(false); // Can be de-anonymized if legally required
            $table->string('recovery_key_hash', 64)->nullable(); // Hash of recovery key
            $table->timestamp('recovery_expires_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for reporting and analysis
            $table->index(['anonymized_at','anonymization_reason'], 'idx_anon_at_reason');
            $table->index(['original_user_type','anonymized_at'],   'idx_origtype_anonat');
            $table->index(['region','anonymized_at'],               'idx_region_anonat');
            $table->index(['expires_at','retention_reason'],        'idx_expires_reason');
            $table->index(['anonymization_verified','verified_at'], 'idx_verified');
            $table->index('original_registration_date',             'idx_orig_reg_date');
            $table->index(['total_collections_created','total_egis_created'], 'idx_tot_coll_egis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('anonymized_users');
    }
};
