<?php

// ════════════════════════════════════════════════════════════════════════════════════
// AGGIORNARE il file database/seeders/RolesAndPermissionsSeeder.php
// AGGIUNGERE i nuovi permessi e ruoli SENZA toccare quelli esistenti
// ════════════════════════════════════════════════════════════════════════════════════

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
            'access_dashboard'
        ],

        'admin' => [
            // ═══ PERMESSI ESISTENTI DELL'ADMIN (NON TOCCARE) ═══
            'add_team_member', 'remove_team_member', 'modify_team_roles',
            'update_collection', 'update_collection_image_header', 'open_collection',
            'create_EGI', 'update_EGI', 'delete_EGI', 'manage_EGI',
            'view_user', 'view_profile', 'view_team', 'view_dashboard', 'view_bio', 'view_settings',
            'view_notifications', 'view_logs',  'view_collection', 'view_EGI', 'view_collection_header',
            'view_wallet', 'view_statistics',
            'manage_profile', 'manage_account', 'delete_account',
            'view_documentation',
            'manage_consents', 'manage_privacy', 'export_personal_data', 'delete_account', 'view_activity_log',
            'view_breach_reports', 'view_privacy_policy', 'edit_personal_data', 'limit_data_processing',
            'access_dashboard'
        ],

        'editor' => [
            // ═══ PERMESSI ESISTENTI DELL'EDITOR (NON TOCCARE) ═══
            'update_EGI', 'manage_EGI',
            'update_collection_image_header', 'open_collection',
            'view_profile', 'view_team', 'view_dashboard', 'view_collection', 'view_EGI', 'view_collection_header', 'view_documentation', 'view_statistics',
            'manage_consents', 'manage_privacy', 'manage_privacy_settings', 'manage_privacy_policies', 'manage_privacy_requests',
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

        $this->command->info('Ruoli e permessi creati/aggiornati con successo.');
        $this->command->info('Nuovi ruoli aggiunti: patron, collector, enterprise, trader_pro, epp_entity');
        $this->command->info('create_collection permission assegnato a: creator, patron, enterprise');
    }
}
