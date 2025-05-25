<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Data Exports Table
 * 🎯 Purpose: Track GDPR data portability exports
 * 🛡️ Privacy: Secure export tracking with token-based access
 * 🧱 Core Logic: Export lifecycle management with expiration
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
     * @privacy-safe Creates data export tracking table
     */
    public function up(): void
    {
        Schema::create('data_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Export identification
            $table->string('token', 64)->unique(); // Secure download token
            $table->enum('format', ['json', 'csv', 'pdf']);
            $table->json('categories'); // Array of data categories to export

            // Export status and progress
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'expired'])->default('pending');
            $table->tinyInteger('progress')->default(0); // 0-100 percentage

            // File information
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // Size in bytes
            $table->string('file_hash', 64)->nullable(); // SHA-256 hash for integrity

            // Access tracking
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();

            // Lifecycle management
            $table->timestamp('expires_at')->nullable(); // Export expiration
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();

            // Export metadata
            $table->json('metadata')->nullable(); // Request details, user agent, etc.

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('expires_at');
            $table->index('token'); // Already unique, but explicit index
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('data_exports');
    }
};
