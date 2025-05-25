<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Consent Versions Table
 * 🎯 Purpose: Track consent policy versions and changes
 * 🛡️ Privacy: Maintains consent policy evolution for compliance
 * 🧱 Core Logic: Supports consent version management and migration
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
     * @privacy-safe Creates consent version tracking table
     */
    public function up(): void
    {
        Schema::create('consent_versions', function (Blueprint $table) {
            $table->id();

            // Version information
            $table->string('version', 20)->unique(); // e.g., "1.0", "1.1", "2.0"
            $table->json('consent_types'); // Definition of consent types for this version
            $table->json('changes')->nullable(); // Summary of changes from previous version

            // Lifecycle
            $table->timestamp('effective_date');
            $table->timestamp('deprecated_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('effective_date');
            $table->index(['is_active', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_versions');
    }
};
