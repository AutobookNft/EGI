<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->char('id', 36)->primary(); // UUID come chiave primaria
            $table->string('type', 255); // Tipo di notifica (es. class name)
            $table->string('model_type', 255); // Modello polimorfico per i payloads
            $table->unsignedBigInteger('model_id'); // ID del modello polimorfico per i payloads
            $table->string('notifiable_type', 255); // Modello polimorfico
            $table->unsignedBigInteger('notifiable_id'); // ID del modello polimorfico. Se User, l'id corrisponde a chi riceve la notifica
            $table->unsignedBigInteger('sender_id'); // ID di chi invia la notifica
            $table->text('data'); // Dati aggiuntivi in formato JSON
            $table->string('outcome', 25)->default('pending'); // Stato della notifica
            $table->timestamp('read_at')->nullable(); // Data di lettura
            $table->timestamps(); // `created_at` e `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
