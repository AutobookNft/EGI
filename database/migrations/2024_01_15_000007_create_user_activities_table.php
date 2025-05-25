<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: User Activities Table
 * 🎯 Purpose: Comprehensive user activity audit trail
 * 🛡️ Privacy: GDPR-compliant activity logging with retention
 * 🧱 Core Logic: Categorized activity tracking with privacy levels
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
     * @privacy-safe Creates activity logging table with privacy controls
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Activity details
            $table->string('action', 100); // login, logout, data_export, consent_update
            $table->enum('category', [
                'authentication',    // Login/logout activities
                'gdpr_actions',     // GDPR-related actions
                'data_access',      // Data viewing/downloading
                'platform_usage',   // General platform interaction
                'security_events',  // Security-related activities
                'blockchain_activity' // Blockchain/NFT activities
            ]);

            // Context and metadata
            $table->json('context')->nullable(); // Action-specific context data
            $table->json('metadata')->nullable(); // Request metadata (url, method, etc.)

            // Privacy and audit
            $table->enum('privacy_level', ['standard', 'high', 'critical', 'immutable'])->default('standard');
            $table->ipAddress('ip_address')->nullable(); // Masked IP
            $table->text('user_agent')->nullable();
            $table->string('session_id', 100)->nullable();

            // Data retention
            $table->timestamp('expires_at')->nullable(); // Auto-cleanup date

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['privacy_level', 'created_at']);
            $table->index('expires_at');
            $table->index('created_at');

            // Composite indexes for common queries
            $table->index(['user_id', 'category', 'created_at']);
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
