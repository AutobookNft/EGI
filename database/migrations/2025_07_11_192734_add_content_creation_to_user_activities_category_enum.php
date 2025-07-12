<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Add content_creation to user_activities category enum
 * 🎯 Purpose: Add support for content creation activities in audit log
 * 🧱 Core Logic: Extend ENUM to include biography and content creation activities
 *
 * @author Assistant for Fabio Cherici
 * @version 1.0.0
 * @date 2025-07-11
 */
return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        // Add 'content_creation' to the category ENUM
        DB::statement("ALTER TABLE user_activities MODIFY COLUMN category ENUM(
            'authentication',
            'gdpr_actions',
            'data_access',
            'platform_usage',
            'security_events',
            'blockchain_activity',
            'content_creation'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Remove 'content_creation' from the category ENUM
        DB::statement("ALTER TABLE user_activities MODIFY COLUMN category ENUM(
            'authentication',
            'gdpr_actions',
            'data_access',
            'platform_usage',
            'security_events',
            'blockchain_activity'
        ) NOT NULL");
    }
};
