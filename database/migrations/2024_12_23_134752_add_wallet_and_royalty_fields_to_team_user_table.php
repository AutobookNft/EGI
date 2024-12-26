<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->string('wallet', 255)->nullable()->after('user_id');
            $table->float('royalty_mint')->nullable()->after('wallet');
            $table->float('royalty_rebind')->nullable()->after('royalty_mint');
            $table->string('approval', 25)->default('approved')->after('royalty_rebind');
            $table->json('previous_values')->nullable()->after('approval');
            $table->string('status',25)->nullable()->after('previous_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn('wallet');
            $table->dropColumn('royalty_mint');
            $table->dropColumn('royalty_rebind');
            $table->dropColumn('approval');
            $table->dropColumn('previous_values');
            $table->dropColumn('status');
        });
    }
};
