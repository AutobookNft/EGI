<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegui la migrazione.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_payload_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade'); // Associazione alla collection
            $table->foreignId('proposer_id')->unsignedBigInteger()->nullable()->constrained('users')->onDelete('cascade'); // Chi propone la modifica
            $table->foreignId('receiver_id')->unsignedBigInteger()->nullable()->constrained('users')->onDelete('cascade'); // Chi riceve la modifica (approva o rifiuta)
            $table->string('email'); // Email dell'invitato
            $table->string('role'); // Ruolo proposto per l'invitato
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending'); // Stato dell'invito
            $table->timestamps();
        });
    }

    /**
     * Elimina la migrazione.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_payload_invitations');
    }
};
