<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('collections', function (Blueprint $table) {
            // Campo boolean per indicare se la Collection può essere inclusa nel carousel guest
            $table->boolean('featured_in_guest')->default(false)->index()->after('is_published')
                ->comment('Indica se la Collection può essere inclusa nel carousel guest');

            // Campo per posizione forzata nel carousel (1-10), null = posizione automatica
            $table->tinyInteger('featured_position')->nullable()->index()->after('featured_in_guest')
                ->comment('Posizione forzata nel carousel guest (1-10), null = posizione automatica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['featured_in_guest', 'featured_position']);
        });
    }
};
