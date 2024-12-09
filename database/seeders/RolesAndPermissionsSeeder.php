<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permessi e ruoli predefiniti.
     */
    private $permissions = [
        'manage_roles',
        'create_collection',
        'read_collection',
        'update_collection',
        'delete_collection',
        'view_users',
        'edit_users',
        'delete_users',
        'create_users',
        'view_dashboard',
        'manage_settings',
    ];

    private $roles = [
        'superadmin' => ['all'],
        'admin' => [
            'manage_roles',
            'create_collection',
            'read_collection',
            'update_collection',
            'delete_collection',
        ],
        'creator' => [
            'create_collection',
            'read_collection',
            'update_collection',
            'delete_collection',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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
