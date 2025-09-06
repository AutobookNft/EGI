<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI - Complete User Activities Table)
 * @date 2025-07-31
 * @purpose Comprehensive user activity audit trail with COMPLETE enum categories
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
            $table->string('action', 120); // login, logout, data_export, consent_update

            // COMPLETE ENUM - matches App\Enums\Gdpr\GdprActivityCategory exactly
            $table->enum('category', [
                'authentication',        // Login/logout activities
                'authentication_login',  // Authentication-related activities
                'authentication_logout', // Logout activities
                'registration',          // User Registration Activities
                'gdpr_actions',         // GDPR-related actions
                'data_access',          // Data viewing/downloading
                'data_deletion',        // Data deletion and erasure
                'content_creation',     // Content creation activities
                'content_modification', // Content modification and updates
                'platform_usage',       // General platform interaction
                'system_interaction',   // System interactions and UI operations
                'security_events',      // Security-related activities
                'blockchain_activity',  // Blockchain/NFT activities
                'media_management',     // File and media operations
                'privacy_management',   // Privacy and consent operations
                'personal_data_update', // Personal data updates
                'wallet_management',    // Wallet and financial operations
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