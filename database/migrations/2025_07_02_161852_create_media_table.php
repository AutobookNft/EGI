<?php

/**
 * @Oracode Migration: Spatie Media Library - MariaDB Compatible
 * 🎯 Purpose: Fixed Spatie Media migration for MariaDB UUID compatibility
 * 🛡️ Privacy: Polymorphic media relations with proper UUID handling
 * 🧱 Core Logic: UUID as CHAR(36) for MariaDB compatibility
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - MariaDB Fixed)
 * @date 2025-07-02
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @Oracode Method: Create Media Table - MariaDB Compatible
     * 🎯 Purpose: Spatie Media Library with MariaDB UUID support
     * 🔧 Fix: UUID field as CHAR(36) instead of native UUID type
     * 📊 Structure: All Spatie features preserved with DB compatibility
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship (model_type, model_id)
            $table->morphs('model');

            // UUID field - MariaDB compatible as CHAR(36)
            $table->char('uuid', 36)->nullable()->unique();

            // Media organization
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();

            // File metadata
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');

            // Ordering support
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->nullableTimestamps();

            // Additional indexes for performance
            $table->index(['model_type', 'model_id', 'collection_name']);
            $table->index(['collection_name', 'created_at']);
        });
    }

    /**
     * @Oracode Method: Drop Media Table
     * 🎯 Purpose: Clean rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
