<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Media Support to EgiTraits
 *
 * Adds optional columns for trait image metadata.
 * Media files are handled by Spatie Media Library.
 *
 * @package FlorenceEGI\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-09-01
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('egi_traits', function (Blueprint $table) {
            // Opzionale: aggiungiamo metadata per l'immagine
            $table->text('image_description')->nullable()->after('display_value');
            $table->string('image_alt_text')->nullable()->after('image_description');
            $table->timestamp('image_updated_at')->nullable()->after('image_alt_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('egi_traits', function (Blueprint $table) {
            $table->dropColumn(['image_description', 'image_alt_text', 'image_updated_at']);
        });
    }
};
