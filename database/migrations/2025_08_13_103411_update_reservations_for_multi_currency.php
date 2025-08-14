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
        Schema::table('reservations', function (Blueprint $table) {
            // ===== MULTI-CURRENCY FIELDS =====

            // 1. Original currency (user's preferred currency when creating the reservation)
            if (!Schema::hasColumn('reservations', 'original_currency')) {
                $table->string('original_currency', 3)->default('USD')->comment('Original currency used by user (ISO 4217)');
            }

            // 2. Original price in user's currency
            if (!Schema::hasColumn('reservations', 'original_price')) {
                $table->decimal('original_price', 15, 8)->comment('Original price in user currency');
            }

            // 3. ALGO price (microALGO - source of truth)
            if (!Schema::hasColumn('reservations', 'algo_price')) {
                $table->unsignedBigInteger('algo_price')->comment('Price in microALGO (source of truth)');
            }

            // 4. Exchange rate used for conversion
            if (!Schema::hasColumn('reservations', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->comment('Exchange rate FIAT->ALGO at transaction time');
            }

            // 5. Rate timestamp
            if (!Schema::hasColumn('reservations', 'rate_timestamp')) {
                $table->timestamp('rate_timestamp')->nullable()->comment('Timestamp of the exchange rate');
            }

            // ===== LEGACY SUPPORT =====

            // Keep existing fiat_currency for backward compatibility
            if (!Schema::hasColumn('reservations', 'fiat_currency')) {
                $table->string('fiat_currency', 3)->default('USD')->comment('Display currency (legacy)');
            }

            // Keep existing offer_amount_fiat for backward compatibility
            if (!Schema::hasColumn('reservations', 'offer_amount_fiat')) {
                $table->decimal('offer_amount_fiat', 10, 2)->comment('Display price in FIAT (legacy)');
            }

            // Keep existing exchange_timestamp for backward compatibility
            if (!Schema::hasColumn('reservations', 'exchange_timestamp')) {
                $table->timestamp('exchange_timestamp')->nullable()->comment('Legacy timestamp field');
            }
        });

        // Add indexes for performance
        Schema::table('reservations', function (Blueprint $table) {
            if (!$this->indexExists('reservations', 'idx_reservations_original_currency')) {
                $table->index('original_currency', 'idx_reservations_original_currency');
            }
            if (!$this->indexExists('reservations', 'idx_reservations_algo_price')) {
                $table->index('algo_price', 'idx_reservations_algo_price');
            }
            if (!$this->indexExists('reservations', 'idx_reservations_fiat_currency')) {
                $table->index('fiat_currency', 'idx_reservations_fiat_currency');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            // Remove indexes
            $indexesToRemove = [
                'idx_reservations_original_currency',
                'idx_reservations_algo_price',
                'idx_reservations_fiat_currency'
            ];

            foreach ($indexesToRemove as $index) {
                if ($this->indexExists('reservations', $index)) {
                    $table->dropIndex($index);
                }
            }

            // Remove new columns (keep legacy ones for safety)
            $columnsToRemove = [];
            if (Schema::hasColumn('reservations', 'original_currency')) {
                $columnsToRemove[] = 'original_currency';
            }
            if (Schema::hasColumn('reservations', 'original_price')) {
                $columnsToRemove[] = 'original_price';
            }
            if (Schema::hasColumn('reservations', 'algo_price')) {
                $columnsToRemove[] = 'algo_price';
            }
            if (Schema::hasColumn('reservations', 'rate_timestamp')) {
                $columnsToRemove[] = 'rate_timestamp';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
