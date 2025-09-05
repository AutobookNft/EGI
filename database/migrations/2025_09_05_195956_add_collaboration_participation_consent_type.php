<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Aggiungi il tipo di consenso per la partecipazione alle collaborazioni
        DB::table('consent_types')->updateOrInsert(
            ['slug' => 'collaboration_participation'],
            [
                'slug' => 'collaboration_participation',
                'legal_basis' => 'consent',
                'data_categories' => json_encode([
                    'collaboration_data',
                    'collection_metadata',
                    'activity_data'
                ]),
                'processing_purposes' => json_encode([
                    'collaboration',
                    'data_sharing_within_collection',
                    'notifications',
                    'activity_tracking',
                    'collaborative_editing'
                ]),
                'recipients' => json_encode([
                    'collection_collaborators',
                    'collection_owner'
                ]),
                'international_transfers' => false,
                'is_required' => false,
                'is_granular' => true,
                'can_withdraw' => true,
                'withdrawal_effect_days' => 7,
                'retention_period' => 'collaboration_duration',
                'deletion_method' => 'hard_delete',
                'priority_order' => 50,
                'is_active' => true,
                'requires_double_opt_in' => false,
                'requires_age_verification' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Rimuovi il tipo di consenso per la partecipazione alle collaborazioni
        DB::table('consent_types')
            ->where('slug', 'collaboration_participation')
            ->delete();
    }
};
