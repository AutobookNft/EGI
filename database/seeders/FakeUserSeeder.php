<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Collection;
use App\Traits\HasCreateDefaultCollectionWallets;

class FakeUserSeeder extends Seeder
{
    use HasCreateDefaultCollectionWallets;

    public function run()
    {
        User::factory(5)->create()->each(function ($user) {
            // Creazione della collection
            $collection = Collection::factory()->create([
                'creator_id' => $user->id,
                'owner_id' => $user->id,
            ]);

            // Associazione nella tabella pivot collection_user
            $collection->users()->attach($user->id, ['role' => 'creator', 'is_owner' => true]);

            // Creazione dei wallet di default
            $this->generateDefaultWallets($collection, $user->wallet, $user->id);
        });

        $this->command->info('Creati 5 utenti con relative collection e wallet di default.');
    }
}
