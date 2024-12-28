<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Collection;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUserWithCollection([
            'id' => 1,
            'name' => 'Natan',
            'email' => 'natan@gmail.com',
            'password' => 'password',
            'role' => 'superadmin',
            'collection_name' => 'Natan\'s Collection',
        ]);

        $this->createUserWithCollection([
            'id' => 2,
            'name' => 'EPP',
            'email' => 'epp@gmail.com',
            'password' => 'password',
            'role' => 'admin',
            'collection_name' => 'EPP\'s Collection',
        ]);

        $this->createUserWithCollection([
            'id' => 3,
            'name' => 'Fabio',
            'email' => 'fabiocherici@gmail.com',
            'password' => 'password',
            'role' => 'creator',
            'collection_name' => 'Fabio\'s Collection',
        ]);

        $this->command->info('Tutti i primi account e relative collections creati con successo.');
    }

    private function createUserWithCollection(array $data): void
    {
        // Creazione o aggiornamento dell'utente
        $user = User::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Creazione o aggiornamento del ruolo
        $role = Role::firstOrCreate(['name' => $data['role']]);

        if (!$user->hasRole($data['role'])) {
            $user->assignRole($role);
        }

        // Creazione della collection
        $collection = Collection::updateOrCreate(
            [
                'creator_id' => $user->id,
                'collection_name' => $data['collection_name'],
            ],
            [
                'owner_id' => $user->id,
                'description' => "{$data['collection_name']} - Default collection for {$data['name']}",
                'type' => 'standard',
                'status' => 'draft',
                'is_published' => false,
                'position' => 1,
                'EGI_number' => 0,
                'floor_price' => 0.0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Associazione del ruolo di proprietario nella tabella pivot collection_user
        $collection->users()->attach($user->id, ['role' => 'creator', 'is_owner' => true]);
    }
}

