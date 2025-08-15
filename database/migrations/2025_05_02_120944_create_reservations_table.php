<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Pre-Launch Reservation Queue System)
 * @date 2025-08-15
 * @purpose Create reservations table for pre-launch ranking system with EUR-canonical amounts
 *
 * SYSTEM: Pre-launch reservation queue with public ranking
 * - No immediate payments, only commitment amounts
 * - Multiple reservations per EGI allowed
 * - Public ranking visible to all
 * - EUR as canonical currency with multi-currency display
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // ===== RELATIONSHIPS =====
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade');

            // ===== RESERVATION TYPE & STATUS =====
            $table->enum('type', ['weak', 'strong'])->default('weak')
                ->comment('Reservation type - may be used for future priority logic');

            $table->enum('status', ['active', 'expired', 'completed', 'cancelled', 'withdrawn'])
                ->default('active')
                ->comment('Main reservation status');

            $table->enum('sub_status', [
                'pending',      // Reservation active and in queue
                'highest',      // Currently the highest offer
                'superseded',   // Outbid by another user
                'confirmed',    // User confirmed for mint (future)
                'minted',       // Successfully minted on-chain (future)
                'withdrawn',    // User withdrew their reservation
                'expired'       // Reservation expired without action
            ])->default('pending')
                ->comment('Detailed state for pre-launch queue system');

            // ===== CANONICAL AMOUNT (EUR) =====
            $table->decimal('amount_eur', 12, 2)
                ->comment('Canonical reservation amount in EUR (source of truth)');

            // ===== RANKING FIELDS =====
            $table->unsignedInteger('rank_position')->nullable()
                ->comment('Current position in the offer ranking for this EGI');

            $table->unsignedInteger('previous_rank')->nullable()
                ->comment('Previous position before last update');

            $table->boolean('is_highest')->default(false)
                ->comment('Quick flag for highest current offer');

            $table->boolean('is_current')->default(true)
                ->comment('Whether this is the current active reservation');

            // ===== DISPLAY CURRENCY (For transparency) =====
            $table->char('display_currency', 3)->default('EUR')
                ->comment('Currency used for display to this user');

            $table->decimal('display_amount', 12, 2)->nullable()
                ->comment('Amount in display currency for transparency');

            $table->decimal('display_exchange_rate', 20, 10)->nullable()
                ->comment('Exchange rate used for display conversion');

            // ===== USER INPUT TRACKING (Audit) =====
            $table->char('input_currency', 3)->default('EUR')
                ->comment('Original currency input by user');

            $table->decimal('input_amount', 12, 2)
                ->comment('Original amount input by user');

            $table->decimal('input_exchange_rate', 20, 10)->nullable()
                ->comment('Exchange rate at time of input if not EUR');

            $table->timestamp('input_timestamp')->nullable()
                ->comment('When the reservation was placed');

            // ===== SUPERSESSION TRACKING =====
            $table->foreignId('superseded_by_id')->nullable()
                ->constrained('reservations')->onDelete('set null')
                ->comment('ID of reservation that outbid this one');

            $table->timestamp('superseded_at')->nullable()
                ->comment('When this reservation was outbid');

            // ===== FUTURE MINT FIELDS (Pre-populated for future use) =====
            $table->timestamp('mint_window_starts_at')->nullable()
                ->comment('When this user can start minting (future)');

            $table->timestamp('mint_window_ends_at')->nullable()
                ->comment('When mint window expires (future)');

            $table->boolean('mint_confirmed')->default(false)
                ->comment('User confirmed intent to mint (future)');

            $table->timestamp('mint_confirmed_at')->nullable()
                ->comment('When user confirmed mint intent (future)');

            // ===== FUTURE PAYMENT FIELDS (Pre-populated for future use) =====
            $table->string('payment_method', 20)->nullable()
                ->comment('Future payment method (algo/card/bank)');

            $table->decimal('payment_amount_eur', 12, 2)->nullable()
                ->comment('Actual payment amount in EUR (future)');

            $table->string('payment_currency', 3)->nullable()
                ->comment('Currency used for payment (future)');

            $table->decimal('payment_amount', 12, 2)->nullable()
                ->comment('Amount in payment currency (future)');

            $table->decimal('payment_exchange_rate', 20, 10)->nullable()
                ->comment('Exchange rate at payment time (future)');

            $table->timestamp('payment_executed_at')->nullable()
                ->comment('When payment was executed (future)');

            // ===== ALGORAND FIELDS (Future on-chain) =====
            $table->unsignedBigInteger('algo_amount_micro')->nullable()
                ->comment('Amount in microALGO for on-chain (future)');

            $table->string('algo_tx_id', 128)->nullable()
                ->comment('Algorand transaction ID (future)');

            $table->string('asa_id', 64)->nullable()
                ->comment('Algorand Standard Asset ID if minted (future)');

            // ===== METADATA & NOTES =====
            $table->json('metadata')->nullable()
                ->comment('Additional metadata (contact info, notes, etc)');

            $table->text('user_note')->nullable()
                ->comment('Optional note from user about reservation');

            $table->text('admin_note')->nullable()
                ->comment('Internal admin notes');

            // ===== NOTIFICATION TRACKING =====
            $table->timestamp('last_notification_at')->nullable()
                ->comment('Last time user was notified about this reservation');

            $table->json('notification_history')->nullable()
                ->comment('History of notifications sent');

            // ===== LEGACY COMPATIBILITY FIELDS =====
            // Keep these for backward compatibility during transition
            $table->string('original_currency', 3)->default('EUR')
                ->comment('Legacy: Original currency used');

            $table->decimal('original_price', 15, 8)->nullable()
                ->comment('Legacy: Original price');

            $table->unsignedBigInteger('algo_price')->nullable()
                ->comment('Legacy: Price in microALGO');

            $table->decimal('exchange_rate', 18, 8)->nullable()
                ->comment('Legacy: Exchange rate');

            $table->timestamp('rate_timestamp')->nullable()
                ->comment('Legacy: Rate timestamp');

            $table->string('fiat_currency', 3)->default('EUR')
                ->comment('Legacy: Display currency');

            $table->decimal('offer_amount_fiat', 10, 2)->nullable()
                ->comment('Legacy: Display price in FIAT');

            $table->decimal('offer_amount_algo', 18, 8)->nullable()
                ->comment('Legacy: ALGO amount');

            $table->timestamp('exchange_timestamp')->nullable()
                ->comment('Legacy: Exchange timestamp');

            $table->timestamp('expires_at')->nullable()
                ->comment('Legacy: Expiration time');

            $table->json('contact_data')->nullable()
                ->comment('Legacy: Contact data for strong reservations');

            // ===== TIMESTAMPS =====
            $table->timestamps();

            // ===== INDEXES FOR PERFORMANCE =====
            // User queries
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['user_id', 'egi_id'], 'idx_user_egi');

            // EGI queries
            $table->index(['egi_id', 'status'], 'idx_egi_status');
            $table->index(['egi_id', 'is_current'], 'idx_egi_current');
            $table->index(['egi_id', 'is_highest'], 'idx_egi_highest');
            $table->index(['egi_id', 'rank_position'], 'idx_egi_rank');

            // Ranking queries
            $table->index(['egi_id', 'amount_eur', 'status'], 'idx_egi_amount_status');
            $table->index('rank_position', 'idx_rank');
            $table->index('is_highest', 'idx_highest');

            // Status queries
            $table->index(['status', 'sub_status'], 'idx_status_sub');
            $table->index('superseded_by_id', 'idx_superseded');
            $table->index('superseded_at', 'idx_superseded_time');

            // Future mint queries
            $table->index('mint_window_starts_at', 'idx_mint_start');
            $table->index('mint_window_ends_at', 'idx_mint_end');
            $table->index(['mint_confirmed', 'status'], 'idx_mint_confirmed_status');

            // Currency queries (legacy compatibility)
            $table->index('original_currency', 'idx_original_currency');
            $table->index('fiat_currency', 'idx_fiat_currency');
            $table->index('algo_price', 'idx_algo_price');
            $table->index(['egi_id', 'offer_amount_algo'], 'idx_egi_algo_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
