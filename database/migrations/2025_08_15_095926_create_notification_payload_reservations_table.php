<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Reservation Notifications)
 * @date 2025-08-15
 * @purpose Create payload table for reservation notifications
 */
return new class extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_payload_reservations', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade')
                ->comment('Associated reservation');

            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade')
                ->comment('Associated EGI');

            $table->foreignId('user_id')->constrained()->onDelete('cascade')
                ->comment('User receiving the notification');

            // Notification type and status
            $table->string('type', 50)
                ->comment('Type: reservation_expired, superseded, highest, rank_changed');

            $table->enum('status', ['info', 'success', 'warning', 'error', 'pending'])
                ->default('info')
                ->comment('Notification status/severity');

            // Notification data
            $table->json('data')
                ->comment('Notification payload data (amounts, ranks, etc)');

            // Optional message override
            $table->text('message')->nullable()
                ->comment('Custom message if different from default');

            // Tracking
            $table->timestamp('read_at')->nullable()
                ->comment('When user read/acknowledged the notification');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'read_at'], 'idx_user_unread');
            $table->index(['reservation_id', 'type'], 'idx_reservation_type');
            $table->index('type', 'idx_type');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_payload_reservations');
    }
};
