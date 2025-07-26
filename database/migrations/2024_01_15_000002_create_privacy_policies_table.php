<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Privacy Policies Table
 * 🎯 Purpose: Version-controlled privacy policy management
 * 🛡️ Privacy: Policy versioning for transparency and compliance
 * 🧱 Core Logic: Privacy policy lifecycle with change tracking
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
return new class extends Migration {
    /**
     * Run the migrations
     *
     * @return void
     * @privacy-safe Creates privacy policy versioning table
     */
    public function up(): void {
        Schema::create('privacy_policies', function (Blueprint $table) {
            $table->id();

            // Version control
            $table->string('version', 20); // e.g., "1.0", "1.1", "2.0"
            $table->string('title', 255);
            $table->longText('content'); // Full policy content
            $table->json('summary'); // Key points summary

            // Change tracking
            $table->json('changes_from_previous')->nullable(); // What changed
            $table->text('change_summary')->nullable(); // Human-readable summary
            $table->string('previous_version', 20)->nullable();

            // Lifecycle
            $table->timestamp('effective_date');
            $table->timestamp('review_date')->nullable(); // Next review date
            $table->boolean('is_active')->default(false);
            $table->boolean('requires_consent_refresh')->default(false);

            // Approval workflow
            $table->enum('status', ['draft', 'review', 'approved', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();

            // Legal references
            $table->json('legal_basis')->nullable(); // GDPR articles, local laws
            $table->json('third_party_services')->nullable(); // External services mentioned

            // Metadata
            $table->string('language', 5)->default('en');
            $table->json('metadata')->nullable(); // Additional structured data

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'effective_date']);
            $table->index(['status', 'effective_date']);
            $table->index('effective_date');
            $table->index('review_date');
            $table->index(['language', 'is_active']);
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void {
        Schema::dropIfExists('privacy_policies');
    }
};