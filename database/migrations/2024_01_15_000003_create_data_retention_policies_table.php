<?php
// database/migrations/2024_01_15_000003_create_data_retention_policies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Data Retention Policies Table
 * 🎯 Purpose: Define data retention policies per data category
 * 🛡️ Privacy: GDPR Article 5(e) storage limitation compliance
 * 🧱 Core Logic: Automated data lifecycle management
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
     * @privacy-safe Creates data retention policy definitions
     */
    public function up(): void
    {
        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->id();

            // Policy identification
            $table->string('name', 255);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();

            // Data scope
            $table->string('data_category', 100); // personal_data, usage_data, etc.
            $table->string('data_type', 100)->nullable(); // specific type within category
            $table->json('applicable_tables')->nullable(); // Database tables affected
            $table->json('applicable_fields')->nullable(); // Specific fields

            // Retention configuration
            $table->enum('retention_trigger', [
                'time_based',           // After X time from creation
                'inactivity_based',     // After X time of inactivity
                'consent_withdrawal',   // When consent is withdrawn
                'account_closure',      // When account is closed
                'legal_basis_ends',     // When legal basis expires
                'custom_event'          // Custom business event
            ]);

            $table->integer('retention_days')->nullable(); // Days to retain
            $table->string('retention_period', 100)->nullable(); // Human readable
            $table->integer('grace_period_days')->default(0); // Extra days before deletion

            // Deletion configuration
            $table->enum('deletion_method', [
                'hard_delete',          // Permanent deletion
                'soft_delete',          // Mark as deleted
                'anonymize',           // Remove identifying data
                'pseudonymize',        // Replace with pseudonyms
                'archive'              // Move to archive storage
            ])->default('anonymize');

            $table->json('anonymization_rules')->nullable(); // How to anonymize
            $table->json('deletion_exceptions')->nullable(); // What to preserve

            // Legal basis
            $table->string('legal_basis', 100)->nullable(); // GDPR legal basis
            $table->text('legal_justification')->nullable();
            $table->json('regulatory_requirements')->nullable(); // Legal obligations

            // Business rules
            $table->boolean('user_can_request_deletion')->default(true);
            $table->boolean('requires_admin_approval')->default(false);
            $table->boolean('notify_user_before_deletion')->default(true);
            $table->integer('notification_days_before')->default(30);

            // Execution configuration
            $table->boolean('is_automated')->default(true);
            $table->string('execution_schedule', 50)->default('daily'); // daily, weekly, monthly
            $table->time('execution_time')->default('02:00:00');
            $table->integer('batch_size')->default(1000); // Records per batch

            // Status and control
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_executed_at')->nullable();
            $table->integer('last_execution_count')->default(0);
            $table->json('execution_log')->nullable(); // Last execution details

            // Compliance tracking
            $table->timestamp('policy_effective_date')->nullable();
            $table->timestamp('policy_review_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Risk assessment
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('risk_assessment')->nullable();
            $table->json('mitigation_measures')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['data_category', 'is_active'], 'drp_data_category_active');
            $table->index(['retention_trigger', 'is_active'], 'drp_retention_trigger_active');
            $table->index(['is_automated', 'is_active'], 'drp_automated_active');
            $table->index(['last_executed_at', 'execution_schedule'], 'drp_last_exec_schedule'); // ← FIX
            $table->index(['policy_effective_date', 'is_active'], 'drp_policy_effective_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('data_retention_policies');
    }
};
