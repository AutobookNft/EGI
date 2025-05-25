<?php
// database/migrations/2024_01_15_000009_create_privacy_policy_acceptances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Privacy Policy Acceptances Table
 * 🎯 Purpose: Track user acceptances of privacy policy versions
 * 🛡️ Privacy: GDPR Article 12-14 transparency documentation
 * 🧱 Core Logic: Audit trail of policy acknowledgments and changes
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
     * @privacy-safe Creates privacy policy acceptance tracking
     */
    public function up(): void
    {
        Schema::create('privacy_policy_acceptances', function (Blueprint $table) {
            $table->id();

            // User and policy relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('privacy_policy_id')->constrained()->onDelete('restrict');

            // Acceptance details
            $table->timestamp('accepted_at');
            $table->string('acceptance_method', 50); // registration, update_prompt, forced_update
            $table->enum('acceptance_type', [
                'initial',              // First acceptance during registration
                'update',               // Acceptance after policy update
                'renewal',              // Periodic re-acceptance
                'explicit_consent',     // Explicit user-initiated acceptance
                'implied_consent'       // Implied through continued use
            ]);

            // Policy snapshot at acceptance time
            $table->string('policy_version', 20);
            $table->text('policy_summary')->nullable(); // Key points user saw
            $table->json('changes_highlighted')->nullable(); // What changes were shown

            // User interaction details
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->string('device_fingerprint', 255)->nullable();
            $table->json('session_data')->nullable(); // Session context

            // Acceptance evidence
            $table->boolean('explicit_checkbox')->default(false); // Did user check a box
            $table->boolean('read_full_policy')->default(false); // Did user view full policy
            $table->integer('time_spent_reading')->nullable(); // Seconds spent on policy page
            $table->json('interaction_evidence')->nullable(); // Scroll, clicks, etc.

            // Notification and communication
            $table->boolean('was_notified')->default(false); // Was user notified of changes
            $table->timestamp('notification_sent_at')->nullable();
            $table->string('notification_method', 50)->nullable(); // email, in_app, etc.
            $table->timestamp('notification_acknowledged_at')->nullable();

            // Withdrawal tracking
            $table->timestamp('withdrawn_at')->nullable();
            $table->string('withdrawal_method', 50)->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->boolean('current_acceptance')->default(true); // Is this the current acceptance

            // Legal and compliance
            $table->string('legal_basis', 100)->default('consent');
            $table->text('compliance_notes')->nullable();
            $table->boolean('requires_new_consent')->default(false); // Does policy change require new consent

            // Verification and integrity
            $table->string('acceptance_hash', 64)->nullable(); // Hash for tamper detection
            $table->json('verification_data')->nullable(); // Additional verification info

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'current_acceptance']);
            $table->index(['user_id', 'accepted_at']);
            $table->index(['privacy_policy_id', 'accepted_at']);
            $table->index(['policy_version', 'acceptance_type']);
            $table->index('accepted_at');
            $table->index('withdrawn_at');

            // Unique constraint for current acceptance
            $table->unique(['user_id', 'privacy_policy_id', 'current_acceptance'], 'unique_current_acceptance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_policy_acceptances');
    }
};
