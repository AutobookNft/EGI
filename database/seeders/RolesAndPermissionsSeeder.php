<?php

// ════════════════════════════════════════════════════════════════════════════════════
// AGGIORNARE il file database/seeders/RolesAndPermissionsSeeder.php
// AGGIUNGERE i nuovi permessi e ruoli SENZA toccare quelli esistenti
// ════════════════════════════════════════════════════════════════════════════════════

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permessi e ruoli predefiniti - ESTESO con nuovi user types
     */
    private $permissions = [

        // ═══ PERMESSI ESISTENTI (NON TOCCARE) ═══
        'manage_roles',
        'manage_permissions',
        'access_dashboard',
        'create_team',
        'update_team',
        'delete_team',
        'add_team_member',
        'remove_team_member',
        'modify_team_roles',
        'create_collection',
        'update_collection',
        'update_collection_image_header',
        'delete_collection',
        'read_collection_header',
        'open_collection',
        'view_collection',
        'view_collection_header',
        'edit_own_collection',
        'create_EGI',
        'update_EGI',
        'delete_EGI',
        'manage_EGI',
        'create_wallet',
        'update_wallet',
        'approve_wallet',
        'reject_wallet',
        'delete_wallet',
        'view_wallet',
        'view_dashboard',
        'view_team',
        'view_EGI',
        'view_user',
        'view_profile',
        'view_bio',
        'view_settings',
        'view_notifications',
        'view_logs',
        'manage_profile',
        'manage_account',
        'delete_account',
        'view_documentation',
        'view_statistics',
        'manage_consents',
        'manage_privacy',
        'export_personal_data',
        'view_activity_log',
        'view_breach_reports',
        'view_privacy_policy',
        'edit_personal_data',
        'limit_data_processing',
        'like_EGI',
        'reserve_EGI',
        'manage_own_biographies',
        'manage_bio_profile',


        // ═══ NUOVI PERMESSI (AGGIUNGERE) ═══

        // Patron specifici
        'support_creators',
        'view_creator_projects',
        'make_donations',
        'patronage_management',

        // Collector specifici
        'buy_egi',
        'manage_personal_collection',
        'trade_egi',
        'collection_wishlist',

        // Enterprise specifici
        'manage_corporate_data',
        'issue_invoices',
        'bulk_operations',
        'corporate_analytics',
        'manage_business_profile',

        // Trader Pro specifici
        'advanced_trading',
        'view_trading_analytics',
        'bulk_trade_operations',
        'access_pro_tools',
        'trading_algorithms',

        // EPP Entity specifici
        'create_epp_projects',
        'manage_epp_projects',
        'allocate_epp_points',
        'certify_sustainability',
        'environmental_reporting',

        // Marketplace generici (per tutti i user types che possono comprare/vendere)
        'view_marketplace',
        'browse_marketplace',
        'make_offers',
        'accept_offers',
        'rate_transactions',

        // ✅ NUOVI: Permessi domini dati utente
        'edit_own_profile_data',
        'edit_own_personal_data',
        'edit_own_organization_data',
        'manage_own_documents',
        'manage_own_invoice_preferences',

        // ✅ NUOVI: Permessi documenti specifici
        'upload_identity_documents',
        'verify_document_status',
        'download_own_documents',

        // ✅ NUOVI: Permessi fatturazione
        'configure_invoice_preferences',
        'view_own_invoices',
        'download_own_invoices',

        // Accesso weak/strong differenziato
        'access_weak_dashboard',           // Dashboard limitata per weak
        'access_full_dashboard',           // Dashboard completa per strong
        'view_own_wallet_address',         // Vedere wallet address
        'upgrade_account_to_strong',       // Processo upgrade weak→strong
        'view_own_profile',                // Vedere il proprio profilo
        'edit_own_EGI',
        'delete_own_EGI',

        // Permessi avanzati strong-only
        'create_multiple_collections',     // Solo strong può creare più collection
        'priority_reservations',          // Strong ha priorità su prenotazioni
        'full_auction_access',            // Strong ha accesso completo alle aste
        'manage_advanced_settings',       // Impostazioni avanzate solo strong

        // ═══ NUOVI PERMESSI LEGAL SYSTEM (AGGIUNGERE) ═══

        // Legal Dashboard Access
        'legal.dashboard.access',

        // Legal Terms Management
        'legal.terms.view',
        'legal.terms.edit',
        'legal.terms.create_version',
        'legal.terms.approve_version',
        'legal.terms.publish_version',

        // Legal Content Management
        'legal.content.validate',
        'legal.content.backup',
        'legal.content.restore',

        // Legal Audit & History
        'legal.history.view',
        'legal.history.export',
        'legal.audit.access',

        // Legal Translations (future)
        'legal.translations.view',
        'legal.translations.manage',

        // Legal Jurisdiction Management
        'legal.jurisdiction.manage',
        'legal.jurisdiction.review',

        // Legal Compliance Monitoring
        'legal.compliance.monitor',
        'legal.compliance.report',
    ];

    private $roles = [

        // ═══ RUOLI ESISTENTI (NON TOCCARE) ═══
        'superadmin' => ['all'],

        'creator' => [
            // ═══ PERMESSI ESISTENTI DEL CREATOR (NON TOCCARE) ═══
            'create_team', 'update_team', 'delete_team',
            'add_team_member', 'remove_team_member', 'modify_team_roles',
            'create_collection', 'update_collection', 'delete_collection','update_collection_image_header', 'open_collection',
            'create_EGI', 'update_EGI', 'delete_EGI', 'manage_EGI',
            'create_wallet', 'update_wallet', 'approve_wallet', 'reject_wallet', 'delete_wallet',
            'view_user', 'view_profile', 'view_team', 'view_dashboard', 'view_bio', 'view_settings',
            'view_notifications', 'view_logs',  'view_collection', 'view_EGI', 'view_collection_header',
            'view_wallet', 'view_statistics',
            'manage_profile', 'manage_account', 'delete_account',
            'view_documentation',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account', 'view_activity_log',
            'view_breach_reports', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'access_dashboard', 'edit_own_profile_data',
            'edit_own_personal_data',
            'edit_own_organization_data',  // ✅ Creator può gestire org data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'create_multiple_collections',
            'priority_reservations',
            'full_auction_access',
            'manage_advanced_settings',
            'manage_own_biographies',
            'manage_bio_profile',
        ],

        'admin' => [
            // ═══ PERMESSI ESISTENTI DELL'ADMIN (NON TOCCARE) ═══
            'create_team', 'remove_team_member', 'modify_team_roles',
            'update_collection', 'update_collection_image_header', 'open_collection',
            'create_EGI', 'update_EGI', 'delete_EGI', 'manage_EGI',
            'view_user', 'view_profile', 'view_team', 'view_dashboard', 'view_bio', 'view_settings',
            'view_notifications', 'view_logs',  'view_collection', 'view_EGI', 'view_collection_header',
            'view_wallet', 'view_statistics',
            'manage_profile', 'manage_account', 'delete_account',
            'view_documentation',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account', 'view_activity_log',
            'view_breach_reports', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'access_dashboard',
            'manage_own_biographies',
            'manage_bio_profile',
        ],

        'editor' => [
            // ═══ PERMESSI ESISTENTI DELL'EDITOR (NON TOCCARE) ═══
            'update_EGI', 'manage_EGI',
            'update_collection_image_header', 'open_collection',
            'view_profile', 'view_team', 'view_dashboard', 'view_collection', 'view_EGI', 'view_collection_header', 'view_documentation', 'view_statistics',
            'manage_consents', 'manage_privacy',
            'access_dashboard'
        ],

        'guest' => [
            // ═══ PERMESSI ESISTENTI DEL GUEST (NON TOCCARE) ═══
            'view_collection_header', 'view_dashboard', 'view_documentation', 'view_statistics',
            'view_EGI',
            'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'access_dashboard'
        ],

        // ═══ NUOVI RUOLI (AGGIUNGERE) ═══

        'patron' => [
            // ✅ HA create_collection (può creare per supportare creators)
            'create_collection', 'update_collection', 'open_collection',

            // Base permissions
            'access_dashboard', 'view_dashboard', 'view_collection', 'view_EGI',
            'view_statistics', 'view_documentation', 'view_collection_header',

            // Patron specific
            'support_creators', 'view_creator_projects', 'make_donations', 'patronage_management',

            // Marketplace (può comprare per supportare)
            'view_marketplace', 'browse_marketplace', 'buy_egi', 'make_offers', 'accept_offers', 'rate_transactions',

            // Profile & GDPR
            'manage_profile', 'manage_account', 'view_profile',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account',
            'view_activity_log', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'edit_own_profile_data',
            'edit_own_personal_data',
            // ❌ Patron NON ha organization data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'priority_reservations',
            'full_auction_access',
            'manage_own_biographies',
            'manage_bio_profile',
        ],

        'collector' => [
            // ❌ NON HA create_collection (solo colleziona, non crea)

            // Base permissions
            'access_dashboard', 'view_dashboard', 'view_collection', 'view_EGI',
            'view_statistics', 'view_documentation', 'view_collection_header',

            // Collector specific
            'buy_egi', 'manage_personal_collection', 'trade_egi', 'collection_wishlist',

            // Marketplace (focus principale)
            'view_marketplace', 'browse_marketplace', 'make_offers', 'accept_offers', 'rate_transactions',

            // Profile & GDPR
            'manage_profile', 'manage_account', 'view_profile',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account',
            'view_activity_log', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'edit_own_profile_data',
            'edit_own_personal_data',
            // ❌ Collector NON ha organization data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'priority_reservations',
            'full_auction_access',
        ],

        'enterprise' => [
            // ✅ HA create_collection (può creare per business)
            'create_collection', 'update_collection', 'delete_collection', 'open_collection',

            // Team management (come creator)
            'create_team', 'update_team', 'delete_team',
            'add_team_member', 'remove_team_member', 'modify_team_roles',

            // EGI management (può creare EGI aziendali)
            'create_EGI', 'update_EGI', 'delete_EGI', 'manage_EGI',

            // Wallet management
            'create_wallet', 'update_wallet', 'view_wallet',

            // Base permissions
            'access_dashboard', 'view_dashboard', 'view_collection', 'view_EGI',
            'view_statistics', 'view_documentation', 'view_collection_header',
            'view_user', 'view_team', 'view_notifications', 'view_logs',

            // Enterprise specific
            'manage_corporate_data', 'issue_invoices', 'bulk_operations',
            'corporate_analytics', 'manage_business_profile',

            // Marketplace
            'view_marketplace', 'browse_marketplace', 'buy_egi', 'make_offers', 'accept_offers', 'rate_transactions',

            // Profile & GDPR
            'manage_profile', 'manage_account', 'view_profile',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account',
            'view_activity_log', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'edit_own_profile_data',
            'edit_own_personal_data',
            'edit_own_organization_data',  // ✅ Enterprise può gestire org data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'create_multiple_collections',
            'priority_reservations',
            'full_auction_access',
            'manage_advanced_settings',
        ],

        'trader_pro' => [
            // ❌ NON HA create_collection (solo trading)

            // Base permissions
            'access_dashboard', 'view_dashboard', 'view_collection', 'view_EGI',
            'view_statistics', 'view_documentation', 'view_collection_header',

            // Trading specific (focus principale)
            'advanced_trading', 'view_trading_analytics', 'bulk_trade_operations',
            'access_pro_tools', 'trading_algorithms',

            // Marketplace (con strumenti avanzati)
            'view_marketplace', 'browse_marketplace', 'buy_egi', 'trade_egi',
            'make_offers', 'accept_offers', 'rate_transactions',

            // Profile & GDPR
            'manage_profile', 'manage_account', 'view_profile',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account',
            'view_activity_log', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'edit_own_profile_data',
            'edit_own_personal_data',
            // ❌ Trader Pro NON ha organization data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'priority_reservations',
            'full_auction_access',
            'manage_advanced_settings',
        ],

        'epp_entity' => [
            // ❌ NON HA create_collection (solo progetti EPP)

            // Base permissions
            'access_dashboard', 'view_dashboard', 'view_EGI',
            'view_statistics', 'view_documentation',

            // EPP specific (focus principale)
            'create_epp_projects', 'manage_epp_projects', 'allocate_epp_points',
            'certify_sustainability', 'environmental_reporting',

            // Può vedere collections per certificare sostenibilità
            'view_collection', 'view_collection_header',

            // Profile & GDPR
            'manage_profile', 'manage_account', 'view_profile',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account',
            'view_activity_log', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'edit_own_profile_data',
            'edit_own_personal_data',
            'edit_own_organization_data',  // ✅ EPP Entity può gestire org data
            'manage_own_documents',
            'manage_own_invoice_preferences',
            'upload_identity_documents',
            'verify_document_status',
            'download_own_documents',
            'configure_invoice_preferences',
            'view_own_invoices',
            'download_own_invoices',
            'access_full_dashboard',
            'view_own_wallet_address',
            'create_multiple_collections',
            'priority_reservations',
            'full_auction_access',
            'manage_advanced_settings',
        ],

        'weak_connect' => [
            // Accesso base
            'view_own_profile',
            'view_own_wallet_address',
            'access_weak_dashboard',

            // Interazioni limitate
            'view_EGI',
            'like_EGI',
            'reserve_EGI',                    // Con priorità bassa
            'update_EGI',                   // Solo se ha creato l'EGI

            // Collection di default (solo 1)
            'view_collection',
            'edit_own_collection',           // Solo la sua collection default
            'create_EGI',                     // Solo nella sua collection
            'edit_own_EGI',
            'delete_own_EGI',

            // Processo di upgrade
            'upgrade_account_to_strong',

            // Base profile management
            'edit_own_profile_data',

            // NO: create_multiple_collections
            // NO: priority_reservations
            // NO: full_auction_access
            // NO: organization_data, documents, invoice_preferences
        ],

        // ═══ NUOVO RUOLO LEGAL (AGGIUNGERE) ═══
        'legal' => [
            // Dashboard access
            'access_dashboard',
            'legal.dashboard.access',

            // Legal terms management (core functionality)
            'legal.terms.view',
            'legal.terms.edit',
            'legal.terms.create_version',
            'legal.terms.approve_version',
            'legal.terms.publish_version',

            // Content management
            'legal.content.validate',
            'legal.content.backup',
            'legal.content.restore',

            // History and audit access
            'legal.history.view',
            'legal.history.export',
            'legal.audit.access',

            // Jurisdiction management
            'legal.jurisdiction.manage',
            'legal.jurisdiction.review',

            // Compliance monitoring
            'legal.compliance.monitor',
            'legal.compliance.report',

            // Basic platform access
            'view_dashboard',
            'view_documentation',
            'view_profile',
            'manage_profile',

            // GDPR rights (for legal team members)
            'manage_consents',
            'view_privacy_policy',
            'view_activity_log',

            // Translation management (future feature)
            'legal.translations.view',
            'legal.translations.manage',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Creare tutti i permessi (inclusi i nuovi)
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Creare i ruoli e assegnare i permessi (inclusi i nuovi)
        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (in_array('all', $rolePermissions)) {
                $role->givePermissionTo(Permission::all());
            } else {
                // Sync permissions (rimuove vecchi, aggiunge nuovi)
                $role->syncPermissions($rolePermissions);
            }
        }
        $this->createLegalUser();

        $this->command->info('Ruoli e permessi creati/aggiornati con successo.');
        $this->command->info('Nuovi ruoli aggiunti: patron, collector, enterprise, trader_pro, epp_entity');
        $this->command->info('create_collection permission assegnato a: creator, patron, enterprise');
    }

    private function createLegalUser(): void
    {
        $legalUser = User::firstOrCreate(
            ['email' => 'legal@florenceegi.com'],
            [
                'name' => 'Legal Editor',
                'email' => 'legal@florenceegi.com',
                'password' => Hash::make('legal2025!FEG'),
                'email_verified_at' => now(),
                'created_via' => 'seeder'
            ]
        );

        $legalUser->assignRole('legal');
        $this->command->info("Legal user created: legal@florenceegi.com");
    }
}