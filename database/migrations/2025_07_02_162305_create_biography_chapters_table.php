<?php

/**
 * @Oracode Migration: Biography Chapters for Timeline Organization
 * 🎯 Purpose: Chapter-based biography structure with temporal organization
 * 🛡️ Privacy: Inherits privacy from parent biography
 * 🧱 Core Logic: Sortable chapters with date ranges and polymorphic media
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
     * @Oracode Method: Create Biography Chapters Table
     * 🎯 Purpose: Individual biography chapters with timeline support
     * 📅 Features: Date ranges, ordering, and rich content
     * 🔗 Relations: Belongs to biography, polymorphic media support
     */
    public function up(): void
    {
        Schema::create('biography_chapters', function (Blueprint $table) {
            $table->id();

            // Parent biography relationship
            $table->foreignId('biography_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('Parent biography reference');

            // Chapter content
            $table->string('title')
                  ->comment('Chapter title/heading');

            $table->longText('content')
                  ->comment('Chapter text content');

            // Timeline support
            $table->date('date_from')
                  ->nullable()
                  ->comment('Chapter period start date');

            $table->date('date_to')
                  ->nullable()
                  ->comment('Chapter period end date');

            $table->boolean('is_ongoing')
                  ->default(false)
                  ->comment('If true, date_to is ignored (current period)');

            // Chapter organization
            $table->integer('sort_order')
                  ->default(0)
                  ->comment('Manual ordering within biography');

            $table->boolean('is_published')
                  ->default(true)
                  ->comment('Chapter visibility (even within public biography)');

            // Rich content support
            $table->json('formatting_data')
                  ->nullable()
                  ->comment('Rich text formatting, highlights, etc.');

            $table->string('chapter_type')
                  ->default('standard')
                  ->comment('Chapter type for different rendering (standard, milestone, achievement)');

            // SEO for individual chapters
            $table->string('slug')
                  ->nullable()
                  ->comment('Chapter-specific URL fragment');

            $table->timestamps();

            // Indexes for performance and queries
            $table->index(['biography_id', 'sort_order']);
            $table->index(['biography_id', 'date_from']);
            $table->index(['biography_id', 'is_published']);
            $table->index(['date_from', 'date_to']); // For timeline queries

            // Unique constraint for slug within biography
            $table->unique(['biography_id', 'slug']);
        });
    }

    /**
     * @Oracode Method: Drop Biography Chapters Table
     * 🎯 Purpose: Clean rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('biography_chapters');
    }
};
