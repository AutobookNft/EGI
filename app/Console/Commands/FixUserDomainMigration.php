<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Command: Fix User Domain Population (Schema Adaptive)
 * 🎯 Purpose: Fix the failed migration by adapting to actual table schema
 * 🛡️ Privacy: Safely migrates data without assuming column existence
 * 🧱 Core Logic: Inspects actual schema and adapts queries accordingly
 */
class FixUserDomainMigration extends Command
{
    protected $signature = 'florence:fix-user-domain-migration {--inspect : Only inspect table schemas}';
    protected $description = 'Fix the failed user domain migration by adapting to actual table schema';

    public function handle(): int
    {
        $this->info('🔧 Fixing User Domain Migration (Schema Adaptive)...');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        try {
            // First inspect schemas
            $schemas = $this->inspectTableSchemas();

            if ($this->option('inspect')) {
                $this->displaySchemas($schemas);
                return Command::SUCCESS;
            }

            // Check if tables exist
            if (!$this->tablesExist()) {
                $this->error('❌ Domain tables do not exist. Please run the create migration first.');
                return Command::FAILURE;
            }

            // Clear existing data to avoid duplicates
            $this->clearExistingData();

            // Populate tables with schema-aware queries
            $this->populateUserProfiles($schemas['user_profiles']);
            $this->populateUserPersonalData($schemas['user_personal_data']);
            $this->populateUserOrganizationData($schemas['user_organization_data']);
            $this->populateUserDocuments($schemas['user_documents']);
            $this->populateUserInvoicePreferences($schemas['user_invoice_preferences']);

            $this->displaySummary();

            $this->info('✅ User domain migration fixed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Migration fix failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    protected function inspectTableSchemas(): array
    {
        $tables = ['user_profiles', 'user_personal_data', 'user_organization_data', 'user_documents', 'user_invoice_preferences'];
        $schemas = [];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $columns = DB::select("DESCRIBE {$table}");
                $schemas[$table] = array_column($columns, 'Field');
                $this->line("📋 {$table}: " . count($schemas[$table]) . " columns");
            } else {
                $schemas[$table] = [];
                $this->warn("⚠️  Table {$table} does not exist");
            }
        }

        return $schemas;
    }

    protected function displaySchemas(array $schemas): void
    {
        foreach ($schemas as $table => $columns) {
            $this->info("\n📋 {$table}:");
            foreach ($columns as $column) {
                $this->line("   • {$column}");
            }
        }
    }

    protected function tablesExist(): bool
    {
        $tables = ['user_profiles', 'user_personal_data', 'user_organization_data', 'user_documents', 'user_invoice_preferences'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    protected function clearExistingData(): void
    {
        $this->info('🧹 Clearing existing data to avoid duplicates...');

        DB::table('user_invoice_preferences')->truncate();
        DB::table('user_documents')->truncate();
        DB::table('user_organization_data')->truncate();
        DB::table('user_personal_data')->truncate();
        DB::table('user_profiles')->truncate();

        $this->line('   ✅ Cleared existing data');
    }

    protected function populateUserProfiles(array $columns): void
    {
        $this->info('👤 Populating user profiles...');

        // Build column list based on what exists
        $selectColumns = [];
        $insertColumns = [];

        $columnMap = [
            'user_id' => 'id',
            'title' => 'title',
            'job_role' => 'job_role',
            'site_url' => 'site_url',
            'facebook' => 'facebook',
            'social_x' => 'social_x',
            'tiktok' => 'tiktok',
            'instagram' => 'instagram',
            'snapchat' => 'snapchat',
            'twitch' => 'twitch',
            'linkedin' => 'linkedin',
            'discord' => 'discord',
            'telegram' => 'telegram',
            'other' => 'other',
            'annotation' => 'annotation',
            'profile_photo_path' => 'profile_photo_path',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];

        foreach ($columnMap as $targetCol => $sourceCol) {
            if (in_array($targetCol, $columns)) {
                $insertColumns[] = $targetCol;
                $selectColumns[] = ($targetCol === 'user_id') ? "{$sourceCol} as {$targetCol}" : $sourceCol;
            }
        }

        if (empty($insertColumns)) {
            $this->warn('   ⚠️  No matching columns found for user_profiles');
            return;
        }

        $insertCols = implode(', ', $insertColumns);
        $selectCols = implode(', ', $selectColumns);

        DB::statement("
            INSERT INTO user_profiles ({$insertCols})
            SELECT {$selectCols}
            FROM users
            WHERE
                title IS NOT NULL OR job_role IS NOT NULL OR site_url IS NOT NULL OR
                facebook IS NOT NULL OR social_x IS NOT NULL OR tiktok IS NOT NULL OR
                instagram IS NOT NULL OR snapchat IS NOT NULL OR twitch IS NOT NULL OR
                linkedin IS NOT NULL OR discord IS NOT NULL OR telegram IS NOT NULL OR
                other IS NOT NULL OR annotation IS NOT NULL
        ");

        $count = DB::table('user_profiles')->count();
        $this->line("   ✅ Migrated {$count} user profiles");
    }

    protected function populateUserPersonalData(array $columns): void
    {
        $this->info('🔒 Populating personal data (GDPR sensitive)...');

        // Build column list based on what exists
        $columnMap = [
            'user_id' => 'id',
            'street' => 'street',
            'city' => 'city',
            'region' => 'region',
            'state' => 'state',
            'zip' => 'zip',
            'home_phone' => 'home_phone',
            'cell_phone' => 'cell_phone',
            'work_phone' => 'work_phone',
            'birth_date' => 'birth_date',
            'fiscal_code' => 'fiscal_code',
            'tax_id_number' => 'tax_id_number',
            'allow_personal_data_processing' => 'true',
            'consent_updated_at' => 'created_at',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];

        $insertColumns = [];
        $selectColumns = [];

        foreach ($columnMap as $targetCol => $sourceCol) {
            if (in_array($targetCol, $columns)) {
                $insertColumns[] = $targetCol;
                if ($targetCol === 'user_id') {
                    $selectColumns[] = "{$sourceCol} as {$targetCol}";
                } elseif (in_array($sourceCol, ['true', 'created_at'])) {
                    $selectColumns[] = $sourceCol . " as {$targetCol}";
                } else {
                    $selectColumns[] = $sourceCol;
                }
            }
        }

        if (empty($insertColumns)) {
            $this->warn('   ⚠️  No matching columns found for user_personal_data');
            return;
        }

        $insertCols = implode(', ', $insertColumns);
        $selectCols = implode(', ', $selectColumns);

        DB::statement("
            INSERT INTO user_personal_data ({$insertCols})
            SELECT {$selectCols}
            FROM users
            WHERE
                street IS NOT NULL OR city IS NOT NULL OR region IS NOT NULL OR
                state IS NOT NULL OR zip IS NOT NULL OR home_phone IS NOT NULL OR
                cell_phone IS NOT NULL OR work_phone IS NOT NULL OR
                birth_date IS NOT NULL OR fiscal_code IS NOT NULL OR
                tax_id_number IS NOT NULL
        ");

        $count = DB::table('user_personal_data')->count();
        $this->line("   ✅ Migrated {$count} personal data records");
    }

    protected function populateUserOrganizationData(array $columns): void
    {
        $this->info('🏢 Populating organization data...');

        // Build column list based on what actually exists
        $columnMap = [
            'user_id' => 'id',
            'org_name' => 'org_name',
            'org_email' => 'org_email',
            'org_street' => 'org_street',
            'org_city' => 'org_city',
            'org_region' => 'org_region',
            'org_state' => 'org_state',
            'org_zip' => 'org_zip',
            'org_site_url' => 'org_site_url',
            'org_phone_1' => 'org_phone_1',
            'org_phone_2' => 'org_phone_2',
            'org_phone_3' => 'org_phone_3',
            'rea' => 'rea',
            'org_fiscal_code' => 'org_fiscal_code',
            'org_vat_number' => 'org_vat_number',
            'is_seller_verified' => "CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_name IS NOT NULL AND (org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL) THEN true ELSE false END",
            'can_issue_invoices' => "CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_vat_number IS NOT NULL THEN true ELSE false END",
            'business_type' => "CASE WHEN usertype = 'creator' THEN 'individual' WHEN usertype = 'azienda' THEN 'corporation' WHEN usertype = 'epp_entity' THEN 'non_profit' ELSE 'other' END",
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];

        $insertColumns = [];
        $selectColumns = [];

        foreach ($columnMap as $targetCol => $sourceCol) {
            if (in_array($targetCol, $columns)) {
                $insertColumns[] = $targetCol;
                if ($targetCol === 'user_id') {
                    $selectColumns[] = "{$sourceCol} as {$targetCol}";
                } elseif (str_contains($sourceCol, 'CASE')) {
                    $selectColumns[] = "({$sourceCol}) as {$targetCol}";
                } else {
                    $selectColumns[] = $sourceCol;
                }
            }
        }

        if (empty($insertColumns)) {
            $this->warn('   ⚠️  No matching columns found for user_organization_data');
            return;
        }

        $insertCols = implode(', ', $insertColumns);
        $selectCols = implode(', ', $selectColumns);

        DB::statement("
            INSERT INTO user_organization_data ({$insertCols})
            SELECT {$selectCols}
            FROM users
            WHERE
                org_name IS NOT NULL OR org_email IS NOT NULL OR org_street IS NOT NULL OR
                org_city IS NOT NULL OR org_region IS NOT NULL OR org_state IS NOT NULL OR
                org_zip IS NOT NULL OR org_site_url IS NOT NULL OR
                org_phone_1 IS NOT NULL OR org_phone_2 IS NOT NULL OR org_phone_3 IS NOT NULL OR
                rea IS NOT NULL OR org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL
        ");

        $count = DB::table('user_organization_data')->count();
        $this->line("   ✅ Migrated {$count} organization data records");
    }

    protected function populateUserDocuments(array $columns): void
    {
        $this->info('📄 Populating documents...');

        $columnMap = [
            'user_id' => 'id',
            'doc_typo' => 'doc_typo',
            'doc_num' => 'doc_num',
            'doc_issue_date' => 'doc_issue_date',
            'doc_expired_date' => 'doc_expired_date',
            'doc_issue_from' => 'doc_issue_from',
            'doc_photo_path_f' => 'doc_photo_path_f',
            'doc_photo_path_r' => 'doc_photo_path_r',
            'verification_status' => "CASE WHEN doc_num IS NOT NULL AND doc_expired_date > NOW() THEN 'verified' WHEN doc_num IS NOT NULL AND doc_expired_date <= NOW() THEN 'expired' WHEN doc_num IS NOT NULL THEN 'pending' ELSE 'pending' END",
            'is_encrypted' => 'true',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];

        $insertColumns = [];
        $selectColumns = [];

        foreach ($columnMap as $targetCol => $sourceCol) {
            if (in_array($targetCol, $columns)) {
                $insertColumns[] = $targetCol;
                if ($targetCol === 'user_id') {
                    $selectColumns[] = "{$sourceCol} as {$targetCol}";
                } elseif (str_contains($sourceCol, 'CASE') || $sourceCol === 'true') {
                    $selectColumns[] = "({$sourceCol}) as {$targetCol}";
                } else {
                    $selectColumns[] = $sourceCol;
                }
            }
        }

        if (empty($insertColumns)) {
            $this->warn('   ⚠️  No matching columns found for user_documents');
            return;
        }

        $insertCols = implode(', ', $insertColumns);
        $selectCols = implode(', ', $selectColumns);

        DB::statement("
            INSERT INTO user_documents ({$insertCols})
            SELECT {$selectCols}
            FROM users
            WHERE
                doc_typo IS NOT NULL OR doc_num IS NOT NULL OR
                doc_issue_date IS NOT NULL OR doc_expired_date IS NOT NULL OR
                doc_issue_from IS NOT NULL OR doc_photo_path_f IS NOT NULL OR
                doc_photo_path_r IS NOT NULL
        ");

        $count = DB::table('user_documents')->count();
        $this->line("   ✅ Migrated {$count} document records");
    }

    protected function populateUserInvoicePreferences(array $columns): void
    {
        $this->info('🧾 Populating invoice preferences...');

        $columnMap = [
            'user_id' => 'id',
            'can_issue_invoices' => "CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') THEN true ELSE false END",
            'auto_request_invoice' => 'false',
            'preferred_invoice_format' => "'pdf'",
            'require_invoice_for_purchases' => 'false',
            'electronic_invoicing_enabled' => "CASE WHEN usertype = 'azienda' THEN true ELSE false END",
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];

        $insertColumns = [];
        $selectColumns = [];

        foreach ($columnMap as $targetCol => $sourceCol) {
            if (in_array($targetCol, $columns)) {
                $insertColumns[] = $targetCol;
                if ($targetCol === 'user_id') {
                    $selectColumns[] = "{$sourceCol} as {$targetCol}";
                } elseif (str_contains($sourceCol, 'CASE') || str_contains($sourceCol, "'")) {
                    $selectColumns[] = "({$sourceCol}) as {$targetCol}";
                } else {
                    $selectColumns[] = $sourceCol;
                }
            }
        }

        if (empty($insertColumns)) {
            $this->warn('   ⚠️  No matching columns found for user_invoice_preferences');
            return;
        }

        $insertCols = implode(', ', $insertColumns);
        $selectCols = implode(', ', $selectColumns);

        DB::statement("
            INSERT INTO user_invoice_preferences ({$insertCols})
            SELECT {$selectCols}
            FROM users
        ");

        $count = DB::table('user_invoice_preferences')->count();
        $this->line("   ✅ Created {$count} invoice preference records");
    }

    protected function displaySummary(): void
    {
        $totalUsers = DB::table('users')->count();
        $profiles = DB::table('user_profiles')->count();
        $personal = DB::table('user_personal_data')->count();
        $org = DB::table('user_organization_data')->count();
        $docs = DB::table('user_documents')->count();
        $invoices = DB::table('user_invoice_preferences')->count();

        $this->info("\n📊 MIGRATION FIX SUMMARY");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("Total Users: {$totalUsers}");
        $this->line("User Profiles: {$profiles}");
        $this->line("Personal Data: {$personal}");
        $this->line("Organization Data: {$org}");
        $this->line("Documents: {$docs}");
        $this->line("Invoice Preferences: {$invoices}");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
