<?php
// database/migrations/2024_01_15_000001_create_consent_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Consent Types Table
 * 🎯 Purpose: Define available consent types and their configurations
 * 🛡️ Privacy: Master data for consent management system
 * 🧱 Core Logic: Centralized consent type definitions with GDPR compliance
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
     * @privacy-safe Creates consent type definitions table
     */
    public function up(): void
    {
        Schema::create('consent_types', function (Blueprint $table) {
            $table->id();

            // Type identification
            $table->string('slug', 100)->unique(); // marketing_emails, analytics_tracking, etc.

            // GDPR compliance
            $table->enum('legal_basis', [
                'consent',
                'legitimate_interest',
                'contract',
                'legal_obligation',
                'vital_interests',
                'public_task'
            ])->default('consent');

            $table->json('data_categories')->nullable(); // Array of data categories processed
            $table->json('processing_purposes')->nullable(); // Specific processing purposes
            $table->json('recipients')->nullable(); // Third parties who receive data
            $table->boolean('international_transfers')->nullable()->default(false);
            $table->json('transfer_countries')->nullable(); // Countries for transfers

            // Consent configuration
            $table->boolean('is_required')->default(false)->nullable(); // Required for service
            $table->boolean('is_granular')->default(true)->nullable(); // Can be given/withdrawn independently
            $table->boolean('can_withdraw')->default(true)->nullable(); // User can withdraw
            $table->integer('withdrawal_effect_days')->default(30)->nullable(); // Days to process withdrawal

            // Data retention
            $table->string('retention_period', 100)->nullable(); // "2 years", "until withdrawal", etc.
            $table->integer('retention_days')->nullable(); // Numeric days for automation
            $table->string('deletion_method', 50)->default('hard_delete'); // hard_delete, anonymize, archive

            // Business configuration
            $table->integer('priority_order')->default(100); // Display order
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_double_opt_in')->default(false);
            $table->boolean('requires_age_verification')->default(false);
            $table->integer('minimum_age')->nullable();

            // UI configuration
            $table->string('icon', 100)->nullable(); // Icon identifier
            $table->string('color', 7)->nullable(); // Hex color for UI
            $table->json('form_fields')->nullable(); // Additional form fields needed

            // Compliance tracking
            $table->timestamp('gdpr_assessment_date')->nullable();
            $table->text('gdpr_assessment_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['slug', 'is_active']);
            $table->index(['legal_basis', 'is_active']);
            $table->index(['is_required', 'is_active']);
            $table->index(['priority_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_types');
    }
};
