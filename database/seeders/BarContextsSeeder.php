<?php

namespace Database\Seeders;

use App\Models\BarContext;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BarContextsSeeder extends Seeder
{
    public function run()
    {
        $contexts = [
            ['context' => 'dashboard', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['context' => 'collections', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['context' => 'admin', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['context' => 'teams', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        foreach ($contexts as $context) {
            BarContext::updateOrCreate(['context' => $context['context']]);
        }
    }
}
