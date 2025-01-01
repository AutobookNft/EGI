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
        Schema::create('wallet_change_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade')->unsignedBigInteger(); // Relazione con il wallet
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade'); // Chi richiede la modifica
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->onDelete('cascade'); // Chi approva (se esiste)
            $table->string('change_type'); // Esempi: 'creation', 'update', 'delete'
            $table->json('change_details'); // Dettagli della modifica (es. vecchi e nuovi valori)
            $table->string('status')->default('pending'); // Valori: 'pending', 'approved', 'rejected'
            $table->string('approval')->default('approved'); // Valori: 'pending', 'approved'
            $table->string('type')->default('update'); // Valori: 'update', 'create'
            $table->json('previous_values')->nullable(); // Per tenere traccia dei vecchi valori in caso di rifiuto
            $table->text('rejection_reason')->nullable(); // Motivo del rifiuto, se applicabile
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_change_approvals');
    }
};
