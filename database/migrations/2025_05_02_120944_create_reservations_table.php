<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade');

            // Reservation details
            $table->enum('type', ['weak', 'strong'])->default('weak');
            $table->enum('status', ['active', 'expired', 'completed', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            
            // Multi-Currency System Fields
            $table->string('original_currency', 3)->default('USD')->comment('Original currency used by user (ISO 4217)');
            $table->decimal('original_price', 15, 8)->comment('Original price in user currency');
            $table->unsignedBigInteger('algo_price')->comment('Price in microALGO (source of truth)');
            $table->decimal('exchange_rate', 18, 8)->comment('Exchange rate FIAT->ALGO at transaction time');
            $table->timestamp('rate_timestamp')->nullable()->comment('Timestamp of the exchange rate');
            
            // Legacy fields for backward compatibility
            $table->string('fiat_currency', 3)->default('USD')->comment('Display currency (legacy)');
            $table->decimal('offer_amount_fiat', 10, 2)->nullable()->comment('Display price in FIAT (legacy)');
            $table->decimal('offer_amount_algo', 18, 8)->nullable()->comment('Legacy ALGO amount');
            $table->timestamp('exchange_timestamp')->nullable()->comment('Legacy timestamp field');
            
            // Additional data
            $table->boolean('is_current')->default(true);
            $table->foreignId('superseded_by_id')->nullable()->constrained('reservations')->onDelete('set null');

            // For strong reservations, store additional contact data
            $table->json('contact_data')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'egi_id']);
            $table->index(['status', 'expires_at']);
            $table->index('superseded_by_id');
            $table->index(['egi_id', 'is_current']);
            $table->index('original_currency');
            $table->index('algo_price');
            $table->index('fiat_currency');
            $table->index(['egi_id', 'offer_amount_algo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};