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
            // 1. Aggiungere prima il campo fiat_currency se non esiste
            if (!Schema::hasColumn('reservations', 'fiat_currency')) {
                $table->string('fiat_currency', 3)->default('USD')->comment('Valuta FIAT per l\'offerta (ISO 4217)');
            }

            // 2. Aggiungere offer_amount_fiat se non esiste
            if (!Schema::hasColumn('reservations', 'offer_amount_fiat')) {
                $table->decimal('offer_amount_fiat', 10, 2)->comment('Prezzo in valuta FIAT');
            }

            // 3. Aggiungere i campi mancanti per la gestione completa della valuta
            if (!Schema::hasColumn('reservations', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->comment('Tasso di cambio ALGO->FIAT al momento della transazione');
            }
            if (!Schema::hasColumn('reservations', 'exchange_timestamp')) {
                $table->timestamp('exchange_timestamp')->nullable()->comment('Timestamp del tasso di cambio');
            }

            // 4. Modificare offer_amount_algo SOLO se necessario (da DECIMAL a BIGINT per microALGO)
            // Prima controlliamo se esiste e che tipo è
            $columns = DB::select("SHOW COLUMNS FROM reservations WHERE Field = 'offer_amount_algo'");
            if (!empty($columns)) {
                $currentType = $columns[0]->Type;
                // Se non è già bigint unsigned, lo modifichiamo
                if (strpos($currentType, 'bigint') === false) {
                    $table->unsignedBigInteger('offer_amount_algo')->change()->comment('Prezzo in microALGO (fonte di verità)');
                }
            } else {
                // Se la colonna non esiste, la creiamo
                $table->unsignedBigInteger('offer_amount_algo')->comment('Prezzo in microALGO (fonte di verità)');
            }
        });

        // 5. Aggiungere indici in una transazione separata per evitare conflitti
        Schema::table('reservations', function (Blueprint $table) {
            // Controllo se gli indici esistono già prima di crearli
            if (!$this->indexExists('reservations', 'idx_reservations_fiat_currency')) {
                $table->index('fiat_currency', 'idx_reservations_fiat_currency');
            }
            if (!$this->indexExists('reservations', 'idx_reservations_offer_amount_algo')) {
                $table->index('offer_amount_algo', 'idx_reservations_offer_amount_algo');
            }
            if (!$this->indexExists('reservations', 'idx_reservations_egi_algo_amount')) {
                $table->index(['egi_id', 'offer_amount_algo'], 'idx_reservations_egi_algo_amount');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return !empty($indexes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            // Rimuovere gli indici se esistono
            if ($this->indexExists('reservations', 'idx_reservations_fiat_currency')) {
                $table->dropIndex('idx_reservations_fiat_currency');
            }
            if ($this->indexExists('reservations', 'idx_reservations_offer_amount_algo')) {
                $table->dropIndex('idx_reservations_offer_amount_algo');
            }
            if ($this->indexExists('reservations', 'idx_reservations_egi_algo_amount')) {
                $table->dropIndex('idx_reservations_egi_algo_amount');
            }

            // Rimuovere le colonne aggiunte se esistono
            $columnsToRemove = [];
            if (Schema::hasColumn('reservations', 'offer_amount_fiat')) {
                $columnsToRemove[] = 'offer_amount_fiat';
            }
            if (Schema::hasColumn('reservations', 'fiat_currency')) {
                $columnsToRemove[] = 'fiat_currency';
            }
            if (Schema::hasColumn('reservations', 'exchange_rate')) {
                $columnsToRemove[] = 'exchange_rate';
            }
            if (Schema::hasColumn('reservations', 'exchange_timestamp')) {
                $columnsToRemove[] = 'exchange_timestamp';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};