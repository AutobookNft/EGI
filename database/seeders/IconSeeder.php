<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Icon;

class IconSeeder extends Seeder
{
    public function run()
    {
        // Carica le icone dal file di configurazione
        $icons = config('icons.styles.elegant');

        foreach ($icons as $icon) {
            Icon::updateOrCreate(
                [
                    'name' => $icon['name'],
                    'style' => 'elegant', // Stile fisso (puoi modificarlo dinamicamente se necessario)
                ],
                [
                    'type' => $icon['type'],
                    'class' => $icon['class'],
                    'html' => $icon['html'],
                ]
            );
        }
    }
}

