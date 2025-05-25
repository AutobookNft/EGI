<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add GDPR Columns to Users Table
 * 🎯 Purpose: Extend users table with GDPR-related fields
 * 🛡️ Privacy: User-level GDPR settings and tracking
 * 🧱 Core Logic: Centralized user GDPR status and preferences
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
     * @privacy-safe Adds GDPR fields to existing users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Consent management
            $table->json('consent_summary')->nullable()->after('email_verified_at'); // Quick consent lookup
            $table->timestamp('consents_updated_at')->nullable()->after('consent_summary');

            // Processing limitations
            $table->json('processing_limitations')->nullable()->after('consents_updated_at');
            $table->timestamp('limitations_updated_at')->nullable()->after('processing_limitations');

            // Data subject requests tracking
            $table->boolean('has_pending_gdpr_requests')->default(false)->after('limitations_updated_at');
            $table->timestamp('last_gdpr_request_at')->nullable()->after('has_pending_gdpr_requests');

            // Account status
            $table->boolean('gdpr_compliant')->default(true)->after('last_gdpr_request_at');
            $table->timestamp('gdpr_status_updated_at')->nullable()->after('gdpr_compliant');

            // Data retention
            $table->timestamp('data_retention_until')->nullable()->after('gdpr_status_updated_at');
            $table->enum('retention_reason', [
                'active_user',        // Active platform usage
                'legal_obligation',   // Legal requirement to retain
                'pending_request',    // GDPR request in progress
                'contract_obligation' // Contractual obligation
            ])->default('active_user')->after('data_retention_until');

            // Privacy preferences
            $table->json('privacy_settings')->nullable()->after('retention_reason');
            $table->string('preferred_communication_method', 20)->default('email')->after('privacy_settings');

            // Audit and compliance
           $table->timestamp('last_activity_logged_at')->nullable()->after('preferred_communication_method');
           $table->unsignedInteger('total_gdpr_requests')->default(0)->after('last_activity_logged_at');

           // Indexes for GDPR operations
           $table->index('has_pending_gdpr_requests');
           $table->index('gdpr_compliant');
           $table->index('last_gdpr_request_at');
           $table->index('data_retention_until');
           $table->index(['gdpr_compliant', 'has_pending_gdpr_requests']);
       });
   }

   /**
    * Reverse the migrations
    *
    * @return void
    */
   public function down(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->dropIndex(['users_has_pending_gdpr_requests_index']);
           $table->dropIndex(['users_gdpr_compliant_index']);
           $table->dropIndex(['users_last_gdpr_request_at_index']);
           $table->dropIndex(['users_data_retention_until_index']);
           $table->dropIndex(['users_gdpr_compliant_has_pending_gdpr_requests_index']);

           $table->dropColumn([
               'consent_summary',
               'consents_updated_at',
               'processing_limitations',
               'limitations_updated_at',
               'has_pending_gdpr_requests',
               'last_gdpr_request_at',
               'gdpr_compliant',
               'gdpr_status_updated_at',
               'data_retention_until',
               'retention_reason',
               'privacy_settings',
               'preferred_communication_method',
               'last_activity_logged_at',
               'total_gdpr_requests'
           ]);
       });
   }
};
