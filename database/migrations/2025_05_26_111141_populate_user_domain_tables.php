<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Populate User Domain Tables (Fixed - No consent field)
 * 🎯 Purpose: Migrate existing user data to optimized domain-specific tables
 * 🛡️ Privacy: Preserves all data while improving GDPR compliance structure
 * 🧱 Core Logic: Safe data migration with clear personal/business separation
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ================================================================
        // SAFETY: Create backup of current user data
        // ================================================================
        $backupTableName = 'users_backup_' . date('Y_m_d_H_i_s');
        DB::statement("CREATE TABLE {$backupTableName} AS SELECT * FROM users");

        echo "✅ Backup created: {$backupTableName}\n";

        // ================================================================
        // 1. POPULATE user_profiles
        // ================================================================
        DB::statement("
            INSERT INTO user_profiles (
                user_id, title, job_role, site_url, facebook, social_x, tiktok,
                instagram, snapchat, twitch, linkedin, discord, telegram, other,
                annotation, created_at, updated_at
            )
            SELECT
                id as user_id,
                title,
                job_role,
                site_url,
                facebook,
                social_x,
                tiktok,
                instagram,
                snapchat,
                twitch,
                linkedin,
                discord,
                telegram,
                other,
                annotation,
                created_at,
                updated_at
            FROM users
            WHERE
                title IS NOT NULL OR
                job_role IS NOT NULL OR
                site_url IS NOT NULL OR
                facebook IS NOT NULL OR
                social_x IS NOT NULL OR
                tiktok IS NOT NULL OR
                instagram IS NOT NULL OR
                snapchat IS NOT NULL OR
                twitch IS NOT NULL OR
                linkedin IS NOT NULL OR
                discord IS NOT NULL OR
                telegram IS NOT NULL OR
                other IS NOT NULL OR
                annotation IS NOT NULL
        ");

        $profilesCount = DB::table('user_profiles')->count();
        echo "✅ Migrated {$profilesCount} user profiles\n";

        // ================================================================
        // 2. POPULATE user_personal_data (FIXED - NO consent field)
        // ================================================================
        DB::statement("
            INSERT INTO user_personal_data (
                user_id, street, city, region, state, zip,
                home_phone, cell_phone, work_phone, birth_date,
                fiscal_code, tax_id_number,
                allow_personal_data_processing, consent_updated_at,
                created_at, updated_at
            )
            SELECT
                id as user_id,
                street, city, region, state, zip,
                home_phone, cell_phone, work_phone, birth_date,
                fiscal_code, tax_id_number,
                -- Default consent to true for existing users (they're already using the platform)
                true as allow_personal_data_processing,
                created_at as consent_updated_at,
                created_at,
                updated_at
            FROM users
            WHERE
                street IS NOT NULL OR city IS NOT NULL OR region IS NOT NULL OR
                state IS NOT NULL OR zip IS NOT NULL OR home_phone IS NOT NULL OR
                cell_phone IS NOT NULL OR work_phone IS NOT NULL OR
                birth_date IS NOT NULL OR fiscal_code IS NOT NULL OR
                tax_id_number IS NOT NULL
        ");

        $personalDataCount = DB::table('user_personal_data')->count();
        echo "✅ Migrated {$personalDataCount} personal data records\n";

        // ================================================================
        // 3. POPULATE user_organization_data
        // ================================================================
        DB::statement("
            INSERT INTO user_organization_data (
                user_id, org_name, org_email, org_street, org_city, org_region,
                org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3,
                rea, org_fiscal_code, org_vat_number,
                is_seller_verified, can_issue_invoices, vat_registered,
                business_type, created_at, updated_at
            )
            SELECT
                id as user_id,
                org_name, org_email, org_street, org_city, org_region,
                org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3,
                rea, org_fiscal_code, org_vat_number,
                -- Set seller verification based on existing data completeness
                CASE
                    WHEN usertype IN ('creator', 'azienda', 'epp_entity')
                        AND org_name IS NOT NULL
                        AND (org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL)
                    THEN true
                    ELSE false
                END as is_seller_verified,
                CASE
                    WHEN usertype IN ('creator', 'azienda', 'epp_entity')
                        AND org_vat_number IS NOT NULL
                    THEN true
                    ELSE false
                END as can_issue_invoices,
                CASE WHEN org_vat_number IS NOT NULL THEN true ELSE false END as vat_registered,
                -- Map usertype to business_type
                CASE
                    WHEN usertype = 'creator' THEN 'individual'
                    WHEN usertype = 'azienda' THEN 'corporation'
                    WHEN usertype = 'epp_entity' THEN 'non_profit'
                    ELSE 'other'
                END as business_type,
                created_at,
                updated_at
            FROM users
            WHERE
                org_name IS NOT NULL OR org_email IS NOT NULL OR org_street IS NOT NULL OR
                org_city IS NOT NULL OR org_region IS NOT NULL OR org_state IS NOT NULL OR
                org_zip IS NOT NULL OR org_site_url IS NOT NULL OR
                org_phone_1 IS NOT NULL OR org_phone_2 IS NOT NULL OR org_phone_3 IS NOT NULL OR
                rea IS NOT NULL OR org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL
        ");

        $orgDataCount = DB::table('user_organization_data')->count();
        echo "✅ Migrated {$orgDataCount} organization data records\n";

        // ================================================================
        // 4. POPULATE user_documents
        // ================================================================
        DB::statement("
            INSERT INTO user_documents (
                user_id, doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from,
                doc_photo_path_f, doc_photo_path_r,
                verification_status, document_purpose, is_encrypted,
                retention_until, created_at, updated_at
            )
            SELECT
                id as user_id,
                doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from,
                doc_photo_path_f, doc_photo_path_r,
                -- Set verification status based on data completeness
                CASE
                    WHEN doc_num IS NOT NULL AND doc_expired_date > NOW() THEN 'verified'
                    WHEN doc_num IS NOT NULL AND doc_expired_date <= NOW() THEN 'expired'
                    WHEN doc_num IS NOT NULL THEN 'pending'
                    ELSE 'pending'
                END as verification_status,
                'identity_verification' as document_purpose,
                true as is_encrypted,
                -- Set retention period to 7 years from issue date (GDPR compliance)
                CASE
                    WHEN doc_issue_date IS NOT NULL
                    THEN DATE_ADD(doc_issue_date, INTERVAL 7 YEAR)
                    ELSE DATE_ADD(NOW(), INTERVAL 7 YEAR)
                END as retention_until,
                created_at,
                updated_at
            FROM users
            WHERE
                doc_typo IS NOT NULL OR doc_num IS NOT NULL OR
                doc_issue_date IS NOT NULL OR doc_expired_date IS NOT NULL OR
                doc_issue_from IS NOT NULL OR doc_photo_path_f IS NOT NULL OR
                doc_photo_path_r IS NOT NULL
        ");

        $documentsCount = DB::table('user_documents')->count();
        echo "✅ Migrated {$documentsCount} document records\n";

        // ================================================================
        // 5. POPULATE user_invoice_preferences
        // ================================================================
        // Create records for all users to prepare for invoice system
        DB::statement("
            INSERT INTO user_invoice_preferences (
                user_id, can_issue_invoices, auto_request_invoice,
                preferred_invoice_format, require_invoice_for_purchases,
                electronic_invoicing_enabled, created_at, updated_at
            )
            SELECT
                id as user_id,
                CASE
                    WHEN usertype IN ('creator', 'azienda', 'epp_entity')
                        AND EXISTS (
                            SELECT 1 FROM user_organization_data uod
                            WHERE uod.user_id = users.id AND uod.org_vat_number IS NOT NULL
                        )
                    THEN true
                    ELSE false
                END as can_issue_invoices,
                false as auto_request_invoice,
                'pdf' as preferred_invoice_format,
                false as require_invoice_for_purchases,
                CASE
                    WHEN usertype = 'azienda' THEN true
                    ELSE false
                END as electronic_invoicing_enabled,
                created_at,
                updated_at
            FROM users
        ");

        $invoicePrefsCount = DB::table('user_invoice_preferences')->count();
        echo "✅ Created {$invoicePrefsCount} invoice preference records\n";

        // ================================================================
        // 6. UPDATE SELLER VERIFICATION TIMESTAMPS
        // ================================================================
        DB::statement("
            UPDATE user_organization_data
            SET seller_verified_at = updated_at
            WHERE is_seller_verified = true
        ");

        // ================================================================
        // 7. VERIFICATION SUMMARY
        // ================================================================
        $totalUsers = DB::table('users')->count();
        echo "\n📊 MIGRATION SUMMARY:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Total Users: {$totalUsers}\n";
        echo "User Profiles: {$profilesCount}\n";
        echo "Personal Data: {$personalDataCount}\n";
        echo "Organization Data: {$orgDataCount}\n";
        echo "Documents: {$documentsCount}\n";
        echo "Invoice Preferences: {$invoicePrefsCount}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Backup Table: {$backupTableName}\n";
        echo "✅ Migration completed successfully!\n\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the populated data (but keep table structure)
        DB::table('user_invoice_preferences')->truncate();
        DB::table('user_documents')->truncate();
        DB::table('user_organization_data')->truncate();
        DB::table('user_personal_data')->truncate();
        DB::table('user_profiles')->truncate();

        echo "✅ Cleared all migrated data from domain tables\n";
        echo "💡 Note: Backup tables are preserved for manual restoration if needed\n";
        echo "💡 To restore: INSERT INTO users SELECT * FROM users_backup_YYYY_mm_dd_HH_ii_ss;\n";
    }
};
