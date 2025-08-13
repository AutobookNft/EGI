<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            // 1. Rimuovere il vecchio campo offer_amount_eur se esiste ancora
            if (Schema::hasColumn('reservations', 'offer_amount_eur')) {
                $table->dropColumn('offer_amount_eur');
            }

            // 2. Modificare offer_amount_algo da DECIMAL a BIGINT per microALGO
            if (Schema::hasColumn('reservations', 'offer_amount_algo')) {
                $table->dropColumn('offer_amount_algo');
            }
            $table->unsignedBigInteger('offer_amount_algo')->after('fiat_currency')->comment('Prezzo in microALGO (fonte di verità)');

            // 3. Aggiungere i campi mancanti per la gestione completa della valuta
            if (!Schema::hasColumn('reservations', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->after('offer_amount_algo')->comment('Tasso di cambio ALGO->FIAT al momento della transazione');
            }
            if (!Schema::hasColumn('reservations', 'exchange_timestamp')) {
                $table->timestamp('exchange_timestamp')->after('exchange_rate')->comment('Timestamp del tasso di cambio');
            }

            // 4. Aggiungere indici per performance
            $table->index('fiat_currency', 'idx_reservations_fiat_currency');
            $table->index('offer_amount_algo', 'idx_reservations_offer_amount_algo');
            $table->index(['egi_id', 'offer_amount_algo'], 'idx_reservations_egi_algo_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            // Rimuovere i nuovi campi
            $table->dropIndex('idx_reservations_fiat_currency');
            $table->dropIndex('idx_reservations_offer_amount_algo');
            $table->dropIndex('idx_reservations_egi_algo_amount');

            $table->dropColumn(['offer_amount_fiat', 'fiat_currency', 'offer_amount_algo', 'exchange_rate', 'exchange_timestamp']);

            // Ripristinare il vecchio campo (opzionale, per sicurezza)
            $table->decimal('offer_amount_eur', 10, 2)->nullable()->comment('Importo offerta in EUR (deprecato)');
        });
    }
};
