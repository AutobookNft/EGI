<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Aggiungere preferred_currency con default EUR per utenti europei
            $table->string('preferred_currency', 3)->default('EUR')->after('email')
                ->comment('Valuta preferita dall\'utente per visualizzazione prezzi (EUR, USD, etc.)');

            // Indice per performance nelle query
            $table->index('preferred_currency', 'idx_users_preferred_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_preferred_currency');
            $table->dropColumn('preferred_currency');
        });
    }
};
