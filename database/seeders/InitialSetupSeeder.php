<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUserWithRole([
            'id' => 1,
            'name' => 'Natan',
            'email' => 'natan@gmail.com',
            'password' => 'password',
            'role' => 'superadmin',
        ]);

        $this->createUserWithRole([
            'id' => 2,
            'name' => 'EPP',
            'email' => 'epp@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->command->info('Superadmin e Admin creati con successo.');
    }

    private function createUserWithRole(array $data): void
    {
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

        $role = Role::firstOrCreate(['name' => $data['role']]);

        if (!$user->hasRole($data['role'])) {
            $user->assignRole($role);
        }
    }
}
