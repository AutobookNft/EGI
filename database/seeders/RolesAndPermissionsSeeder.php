<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permessi e ruoli predefiniti.
     */
    private $permissions = [
        // Permessi Generali

        'manage_roles',
        'manage_permissions',

        // Permessi per il Team
        'create_team',
        'update_team',
        'delete_team',
        'add_team_member',
        'remove_team_member',
        'modify_team_roles',

        // Permessi per le Collection
        'create_collection',
        'update_collection',
        'update_collection_image_header',
        'delete_collection',
        'read_collection_header',

        // Permessi per gli EGI
        'create_EGI',
        'update_EGI',
        'delete_EGI',

        // Permessi per i Wallet
        'create_wallet',
        'update_wallet',
        'approve_wallet',
        'reject_wallet',
        'delete_wallet',

        // Permessi per le views
        'view_dashboard',
        'view_team',
        'view_collection',
        'view_collection_header',
        'view_EGI',
        'view_user',
        'view_profile',
        'view_bio',
        'view_settings',
        'view_notifications',
        'view_logs',
    ];

    private $roles = [
        'superadmin' => ['all'],
        'creator' => [
            // Team
            'create_team', 'update_team', 'delete_team',
            'add_team_member', 'remove_team_member', 'modify_team_roles',

            // Collection
            'create_collection', 'update_collection', 'delete_collection','update_collection_image_header',

            // EGI
            'create_EGI', 'update_EGI', 'delete_EGI',

            // Wallet
            'create_wallet', 'update_wallet', 'approve_wallet', 'reject_wallet', 'delete_wallet',

            // Views
            'view_user', 'view_profile', 'view_team', 'view_dashboard', 'view_bio', 'view_settings',
            'view_notifications', 'view_logs',  'view_collection', 'view_EGI', 'view_collection_header'
        ],

        'admin' => [
            // Team
            'add_team_member', 'remove_team_member', 'modify_team_roles',

            // Collection
            'update_collection', 'update_collection_image_header',

            // EGI
            'create_EGI', 'update_EGI', 'delete_EGI',

            // Views
            'view_user', 'view_profile', 'view_team', 'view_dashboard', 'view_bio', 'view_settings',
            'view_notifications', 'view_logs',  'view_collection', 'view_EGI', 'view_collection_header'
        ],

        'editor' => [

            // EGI
            'update_EGI',

            // Collection
            'update_collection_image_header',

            // Views
            'view_profile', 'view_team', 'view_dashboard', 'view_collection', 'view_EGI', 'view_collection_header'
        ],

        'guest' => [
            // Collection
            'view_collection_header', 'view_dashboard',

            // EGI
            'view_EGI',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Creare tutti i permessi
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Creare i ruoli e assegnare i permessi
        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (in_array('all', $rolePermissions)) {
                $role->givePermissionTo(Permission::all());
            } else {
                $role->givePermissionTo($rolePermissions);
            }
        }

        $this->command->info('Ruoli e permessi creati con successo.');
    }
}
