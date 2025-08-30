<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for actual EGI Traits
 * 
 * @package FlorenceEGI\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('egi_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('trait_categories');
            $table->foreignId('trait_type_id')
                ->constrained('trait_types');
            $table->string('value', 255);
            $table->string('display_value', 255)->nullable();
            $table->decimal('rarity_percentage', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('ipfs_hash', 255)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['egi_id', 'is_locked']);
            $table->index(['trait_type_id', 'value']); // For rarity calculation
            $table->index(['category_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('egi_traits');
    }
};
