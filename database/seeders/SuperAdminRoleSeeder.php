<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminRoleSeeder extends Seeder
{
    /**
     * Esegue il seeder per creare il ruolo SuperAdmin con permessi.
     *
     * @return void
     */
    public function run()
    {
        // Permessi da creare e assegnare al SuperAdmin
        $permissions = [
            'view_users',
            'edit_users',
            'delete_users',
            'create_users',
            'manage_roles',
            'view_dashboard',
            'manage_settings',
            'create_collection',
            'read_collection',
            'update_collection',
            'delete_collection',
        ];

        // Creare i permessi se non esistono già
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Creare il ruolo SuperAdmin se non esiste già
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);

        // Assegnare tutti i permessi al ruolo SuperAdmin
        $superAdminRole->syncPermissions($permissions);

        $this->command->info('Ruolo SuperAdmin creato e permessi assegnati con successo.');
    }
}
