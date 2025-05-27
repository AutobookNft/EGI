<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Populate User Domain Tables (Generated)
 * 🎯 Purpose: Migrate existing user data to optimized domain-specific tables
 * 🛡️ Privacy: Preserves all data while improving GDPR compliance structure
 */
return new class extends Migration
{
    public function up(): void
    {
        // Create backup
        $backupTableName = 'users_backup_' . date('Y_m_d_H_i_s');
        DB::statement("CREATE TABLE {$backupTableName} AS SELECT * FROM users");

        // Populate user_profiles
        DB::statement("
            INSERT INTO user_profiles (user_id, title, job_role, site_url, facebook, social_x, tiktok, instagram, snapchat, twitch, linkedin, discord, telegram, other, annotation, created_at, updated_at)
            SELECT id, title, job_role, site_url, facebook, social_x, tiktok, instagram, snapchat, twitch, linkedin, discord, telegram, other, annotation, created_at, updated_at
            FROM users
            WHERE title IS NOT NULL OR job_role IS NOT NULL OR site_url IS NOT NULL OR facebook IS NOT NULL OR social_x IS NOT NULL OR tiktok IS NOT NULL OR instagram IS NOT NULL OR snapchat IS NOT NULL OR twitch IS NOT NULL OR linkedin IS NOT NULL OR discord IS NOT NULL OR telegram IS NOT NULL OR other IS NOT NULL OR annotation IS NOT NULL
        ");

        // Populate user_personal_data
        DB::statement("
            INSERT INTO user_personal_data (user_id, street, city, region, state, zip, home_phone, cell_phone, work_phone, birth_date, fiscal_code, tax_id_number, allow_personal_data_processing, consent_updated_at, created_at, updated_at)
            SELECT id, street, city, region, state, zip, home_phone, cell_phone, work_phone, birth_date, fiscal_code, tax_id_number, COALESCE(consent, false), CASE WHEN consent IS NOT NULL THEN updated_at ELSE NULL END, created_at, updated_at
            FROM users
            WHERE street IS NOT NULL OR city IS NOT NULL OR region IS NOT NULL OR state IS NOT NULL OR zip IS NOT NULL OR home_phone IS NOT NULL OR cell_phone IS NOT NULL OR work_phone IS NOT NULL OR birth_date IS NOT NULL OR fiscal_code IS NOT NULL OR tax_id_number IS NOT NULL
        ");

        // Populate user_organization_data
        DB::statement("
            INSERT INTO user_organization_data (user_id, org_name, org_email, org_street, org_city, org_region, org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3, rea, org_fiscal_code, org_vat_number, is_seller_verified, can_issue_invoices, business_type, created_at, updated_at)
            SELECT id, org_name, org_email, org_street, org_city, org_region, org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3, rea, org_fiscal_code, org_vat_number,
            CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_name IS NOT NULL AND (org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL) THEN true ELSE false END,
            CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_vat_number IS NOT NULL THEN true ELSE false END,
            CASE WHEN usertype = 'creator' THEN 'individual' WHEN usertype = 'azienda' THEN 'corporation' WHEN usertype = 'epp_entity' THEN 'non_profit' ELSE 'other' END,
            created_at, updated_at
            FROM users
            WHERE org_name IS NOT NULL OR org_email IS NOT NULL OR org_street IS NOT NULL OR org_city IS NOT NULL OR org_region IS NOT NULL OR org_state IS NOT NULL OR org_zip IS NOT NULL OR org_site_url IS NOT NULL OR org_phone_1 IS NOT NULL OR org_phone_2 IS NOT NULL OR org_phone_3 IS NOT NULL OR rea IS NOT NULL OR org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL
        ");

        // Populate user_documents
        DB::statement("
            INSERT INTO user_documents (user_id, doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from, doc_photo_path_f, doc_photo_path_r, verification_status, is_encrypted, created_at, updated_at)
            SELECT id, doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from, doc_photo_path_f, doc_photo_path_r,
            CASE WHEN doc_num IS NOT NULL AND doc_expired_date > NOW() THEN 'verified' WHEN doc_num IS NOT NULL AND doc_expired_date <= NOW() THEN 'expired' WHEN doc_num IS NOT NULL THEN 'pending' ELSE 'pending' END,
            true, created_at, updated_at
            FROM users
            WHERE doc_typo IS NOT NULL OR doc_num IS NOT NULL OR doc_issue_date IS NOT NULL OR doc_expired_date IS NOT NULL OR doc_issue_from IS NOT NULL OR doc_photo_path_f IS NOT NULL OR doc_photo_path_r IS NOT NULL
        ");

        // Populate user_invoice_preferences
        DB::statement("
            INSERT INTO user_invoice_preferences (user_id, can_issue_invoices, created_at, updated_at)
            SELECT id, CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') THEN true ELSE false END, created_at, updated_at
            FROM users
        ");
    }

    public function down(): void
    {
        DB::table('user_invoice_preferences')->truncate();
        DB::table('user_documents')->truncate();
        DB::table('user_organization_data')->truncate();
        DB::table('user_personal_data')->truncate();
        DB::table('user_profiles')->truncate();
    }
};