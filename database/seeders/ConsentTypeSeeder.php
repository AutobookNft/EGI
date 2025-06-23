<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsentType;
use Illuminate\Support\Facades\DB;

class ConsentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Popola la tabella dei tipi di consenso con la configurazione di base.
     * Il testo (nome/descrizione) viene gestito tramite i file di traduzione,
     * usando lo 'slug' come chiave.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $items = [
                // ACCETTAZIONI E CONSENSI OBBLIGATORI
                [
                    'slug' => 'platform-services',
                    'legal_basis' => 'contract',
                    'is_required' => true,
                    'is_active' => true,
                    'priority_order' => 1,
                    'processing_purposes' => json_encode([
                        'account_management', 'service_delivery', 'legal_compliance', 'customer_support'
                    ]),
                ],
                [
                    'slug' => 'terms-of-service',
                    'legal_basis' => 'contract',
                    'is_required' => true,
                    'priority_order' => 10,
                ],
                [
                    'slug' => 'privacy-policy',
                    'legal_basis' => 'legal_obligation',
                    'is_required' => true,
                    'priority_order' => 20,
                ],
                [
                    'slug' => 'age-confirmation',
                    'legal_basis' => 'contract',
                    'is_required' => true,
                    'priority_order' => 30,
                ],

                // CONSENSI GDPR OPZIONALI
                [
                    'slug' => 'analytics',
                    'legal_basis' => 'consent',
                    'is_required' => false,
                    'priority_order' => 40,
                ],
                [
                    'slug' => 'marketing',
                    'legal_basis' => 'consent',
                    'is_required' => false,
                    'priority_order' => 50,
                ],
                [
                    'slug' => 'personalization',
                    'legal_basis' => 'consent',
                    'is_required' => false,
                    'priority_order' => 60,
                ]
            ];

            foreach ($items as $item) {
                ConsentType::updateOrCreate(
                    ['slug' => $item['slug']], // Cerca per slug
                    $item  // E crea/aggiorna con gli altri dati
                );
            }
        });
    }
}
