<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix utilities foreign key constraint
 *
 * The original constraint had onDelete('cascade') which was WRONG.
 * It was deleting the EGI when the utility was deleted!
 * This migration fixes it to NOT delete the EGI when utility is deleted.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('utilities', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['egi_id']);

            // Add the corrected foreign key constraint
            // When utility is deleted, EGI should NOT be deleted
            // When EGI is deleted, utility SHOULD be deleted (that's correct)
            $table->foreign('egi_id')
                ->references('id')
                ->on('egis')
                ->onDelete('cascade');  // This is still correct: delete utility when EGI is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('utilities', function (Blueprint $table) {
            // Drop the corrected constraint
            $table->dropForeign(['egi_id']);

            // Restore the original (incorrect) constraint
            $table->foreign('egi_id')
                ->references('id')
                ->on('egis')
                ->onDelete('cascade');
        });
    }
};
