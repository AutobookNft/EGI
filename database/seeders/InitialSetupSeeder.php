<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;


class InitialSetupSeeder extends Seeder
{
    public function run()
    {
        // Creazione dell'utente Natan (Superadmin)
        $natan = User::updateOrCreate(
            ['id' => 1], // Verifica se l'utente esiste tramite l'ID
            [
                'name' => 'Natan',
                'email' => 'natan@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Creazione del ruolo "superadmin"
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Assegna il ruolo "superadmin" a Natan
        if (!$natan->hasRole('superadmin')) {
            $natan->assignRole($superadminRole);
        }

        // Reset cached roles and permissions
       // Create permissions
       $permissions = [
        'manage_roles',
        'create_collection',
        'read_collection',
        'update_collection',
        'delete_collection',
       ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $superadminRole->givePermissionTo(Permission::all());

        // Creazione dell'utente EPP (Admin)
        $epp = User::updateOrCreate(
            ['id' => 2], // Verifica se l'utente esiste tramite l'ID
            [
                'name' => 'EPP',
                'email' => 'epp@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Creazione del ruolo "admin"
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Assegna il ruolo "admin" a EPP
        if (!$epp->hasRole('admin')) {
            $epp->assignRole($adminRole);
        }

        // Messaggi di log
        $this->command->info('Superadmin e Admin creati con successo.');
    }
}
