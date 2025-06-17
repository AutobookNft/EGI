<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gdpr_notification_payloads', function (Blueprint $table) {
            $table->id();
            $table->string('gdpr_notification_type'); // Tipo di consenso, ad esempio 'marketing', 'privacy', etc.
            $table->text('previous_value')->nullable(); // Valore precedente del consenso
            $table->text('new_value'); // Nuovo valore del consenso
            $table->string('email')->nullable(); // Email dell'utente, se disponibile
            $table->string('role')->default('creator'); // Ruolo dell'utente, ad esempio 'creator', 'editor', etc.
            $table->text('message')->nullable(); // Messaggio associato alla notifica, se necessario
            $table->ipAddress('ip_address')->nullable(); // Indirizzo IP dell'utente
            $table->text('user_agent')->nullable(); // User agent dell'utente
            $table->enum('payload_status', [
                'pending_user_confirmation',
                'user_confirmed_action',
                'user_revoked_consent',
                'user_disavowed_suspicious',
                'error',
            ])->default('pending_user_confirmation'); // Stato del payload, con valori predefiniti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_payload_gdprs');
    }
};
