<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package   Database\Migrations
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Creates the table to store user consent confirmations, providing a clear and specific audit trail.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_consent_confirmations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Link to the specific consent record being confirmed
            $table->foreignId('user_consent_id')->constrained('user_consents')->onDelete('cascade');

            // Link to the notification that prompted this confirmation
            $table->char('notification_id', 32)->constrained('notifications')->onDelete('cascade');

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('confirmation_method')->default('notification_click');

            $table->timestamp('confirmed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consent_confirmations');
    }
};
