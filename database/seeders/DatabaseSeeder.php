<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            InitialSetupSeeder::class,
            RolesAndPermissionsSeeder::class,
            BarContextsSeeder::class,
            BarContextsSummariesSeeder::class,
            BarContextsMenuSeeder::class,
            IconSeeder::class,
        ]);
    }
}
