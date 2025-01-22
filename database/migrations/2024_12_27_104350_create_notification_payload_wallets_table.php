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
        Schema::create('notification_payload_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->unsignedBigInteger()->nullable(); // Relazione con il wallet
            $table->foreignId('proposer_id')->unsignedBigInteger()->nullable()->constrained('users')->onDelete('cascade'); // Chi propone la modifica
            $table->foreignId('receiver_id')->unsignedBigInteger()->nullable()->constrained('users')->onDelete('cascade'); // Chi riceve la modifica (approva o rifiuta)
            $table->string('wallet', 255)->nullable(); // Indirizzo del wallet
            $table->float('royalty_mint')->nullable(); // Percentuale della prima vendita
            $table->float('royalty_rebind')->nullable(); // Percentuale del mercato secondario
            $table->string('status')->default('pending'); // Valori: 'pending', 'approved', 'rejected'
            $table->string('type')->default('update'); // Valori: 'update', 'create' , 'delete'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_payload_wallets');
    }
};
