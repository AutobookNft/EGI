<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Ranking fields
            $table->integer('rank_position')->nullable()->after('amount_eur');
            $table->boolean('is_highest')->default(false)->after('rank_position');


            // Indexes for performance
            $table->index(['egi_id', 'rank_position']);
            $table->index(['egi_id', 'is_highest']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['egi_id', 'rank_position']);
            $table->dropIndex(['egi_id', 'is_highest']);
            $table->dropColumn(['rank_position', 'is_highest']);
        });
    }
};
