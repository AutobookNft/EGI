<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Extend Reservations Table for Enhanced Pricing and Status
 * 🎯 Purpose: Add fields for monetary offers and reservation status tracking
 * 🛡️ GDPR: No PII added, only transactional amounts
 *
 * @return void
 *
 * @oracode-flow
 * 1. Add offer amounts in EUR and ALGO
 * 2. Add expiration timestamp
 * 3. Add fields for tracking active state and superseded reservations
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Monetary offer amounts

            // Status tracking
            $table->boolean('is_current')->default(true)->after('expires_at');
            $table->unsignedBigInteger('superseded_by_id')->nullable()->after('is_current');

            // Self-referencing foreign key for superseded_by_id
            $table->foreign('superseded_by_id')
                  ->references('id')
                  ->on('reservations')
                  ->onDelete('set null');

            // Add index for performance
            $table->index(['egi_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['superseded_by_id']);

            // Drop index
            $table->dropIndex(['egi_id', 'is_current']);

            // Drop columns
            $table->dropColumn([
                'is_current',
                'superseded_by_id'
            ]);
        });
    }
};
