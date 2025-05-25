<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: GDPR Requests Table
 * 🎯 Purpose: Track all GDPR data subject requests
 * 🛡️ Privacy: Comprehensive GDPR request management
 * 🧱 Core Logic: Supports all GDPR rights with proper status tracking
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
     * @privacy-safe Creates GDPR request tracking table
     */
    public function up(): void
    {
        Schema::create('gdpr_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Request details
            $table->enum('type', [
                'access',           // Article 15 - Right of access
                'rectification',    // Article 16 - Right to rectification
                'erasure',         // Article 17 - Right to erasure
                'portability',     // Article 20 - Right to data portability
                'restriction',     // Article 18 - Right to restriction
                'objection',       // Article 21 - Right to object
                'data_update',     // Internal data updates
                'deletion',        // Account deletion
                'deletion_executed' // Completed deletion
            ]);

            $table->enum('status', [
                'pending',         // Request received, awaiting processing
                'in_progress',     // Currently being processed
                'completed',       // Successfully completed
                'rejected',        // Rejected with reason
                'cancelled',       // Cancelled by user
                'expired'          // Expired without completion
            ])->default('pending');

            // Request data and response
            $table->json('request_data')->nullable(); // Original request details
            $table->json('response_data')->nullable(); // Response/result data
            $table->text('notes')->nullable(); // Admin notes
            $table->text('rejection_reason')->nullable(); // Reason if rejected

            // Processing timeline
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // For time-limited requests

            // Processing details
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('processor_role', 50)->nullable(); // admin, dpo, automated

            // Timestamps
            $table->timestamps();

            // Indexes for performance and reporting
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['requested_at', 'type']);
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_requests');
    }
};
