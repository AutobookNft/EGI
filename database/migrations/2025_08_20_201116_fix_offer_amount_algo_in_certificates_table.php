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
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Change offer_amount_algo from decimal(18,8) to unsigned bigint
            // This allows values up to 18,446,744,073,709,551,615 (much larger than microALGO needs)
            $table->unsignedBigInteger('offer_amount_algo')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Revert back to decimal(18,8)
            $table->decimal('offer_amount_algo', 18, 8)->change();
        });
    }
};
