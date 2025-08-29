<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for utilities table
 * Creates table for storing utility information associated with EGIs
 * 
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Utility System)
 * @date 2025-08-29
 * @purpose Creates utilities table for managing physical goods, services, 
 *          digital content, and hybrid utilities associated with EGIs
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('utilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('egi_id')->unique();
            $table->enum('type', ['physical', 'service', 'hybrid', 'digital']);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            
            // Physical shipping fields
            $table->boolean('requires_shipping')->default(false);
            $table->string('shipping_type', 50)->nullable();
            $table->integer('estimated_shipping_days')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->boolean('fragile')->default(false);
            $table->boolean('insurance_recommended')->default(false);
            $table->text('shipping_notes')->nullable();
            
            // Escrow configuration
            $table->enum('escrow_tier', ['immediate', 'standard', 'premium'])->default('standard');
            
            // Service fields
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);
            $table->text('activation_instructions')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('egi_id')->references('id')->on('egis')->onDelete('cascade');
            
            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index('escrow_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilities');
    }
};
