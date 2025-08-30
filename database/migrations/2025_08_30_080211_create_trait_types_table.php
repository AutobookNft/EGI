<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for EGI Trait Types (predefined values)
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
        Schema::create('trait_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('trait_categories')
                ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->enum('display_type', ['text', 'number', 'percentage', 'date', 'boost_number'])
                ->default('text');
            $table->string('unit', 20)->nullable(); // kg, cm, %, etc
            $table->json('allowed_values')->nullable(); // Strict predefined values
            $table->boolean('is_system')->default(false);
            $table->foreignId('collection_id')->nullable()
                ->constrained('collections')
                ->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['category_id', 'slug']);
            $table->index(['collection_id', 'is_system']);
            $table->unique(['category_id', 'slug', 'collection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('trait_types');
    }
};
