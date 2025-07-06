<?php

/**
 * @Oracode Migration: User Biography System with Flexible Structure
 * 🎯 Purpose: Support both single-text and chapter-based biographies
 * 🛡️ Privacy: GDPR-compliant with user ownership and visibility control
 * 🧱 Core Logic: Polymorphic image support via Spatie Media Library
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-02
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @Oracode Method: Create Biographies Table
     * 🎯 Purpose: Main biography container with flexible type system
     * 📊 Structure: Supports both single content and chapter-based organization
     * 🔐 Privacy: Built-in visibility control and user ownership
     */
    public function up(): void
    {
        Schema::create('biographies', function (Blueprint $table) {
            $table->id();

            // User ownership - Required
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('Biography owner');

            // Biography configuration
            $table->enum('type', ['single', 'chapters'])
                  ->default('single')
                  ->comment('Biography structure type');

            $table->string('title')
                  ->comment('Biography main title');

            // Content for single-type biographies
            $table->longText('content')
                  ->nullable()
                  ->comment('Full biography content (only for type=single)');

            // Privacy and visibility
            $table->boolean('is_public')
                  ->default(false)
                  ->comment('Public visibility flag');

            $table->boolean('is_completed')
                  ->default(false)
                  ->comment('Completion status for UX purposes');

            // SEO and sharing
            $table->string('slug')
                  ->unique()
                  ->nullable()
                  ->comment('URL-friendly identifier for public biographies');

            $table->text('excerpt')
                  ->nullable()
                  ->comment('Short description for sharing/preview');

            // Metadata
            $table->json('settings')
                  ->nullable()
                  ->comment('Biography display preferences and options');

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'type']);
            $table->index(['is_public', 'created_at']);
            $table->index('slug');
        });
    }

    /**
     * @Oracode Method: Drop Biographies Table
     * 🎯 Purpose: Clean rollback with cascade consideration
     */
    public function down(): void
    {
        Schema::dropIfExists('biographies');
    }
};
