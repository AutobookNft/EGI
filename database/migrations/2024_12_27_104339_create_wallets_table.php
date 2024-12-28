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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade'); // Relazione con collections
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Nullable per wallet anonimi
            $table->string('wallet', 255)->nullable(); // Indirizzo del wallet
            $table->string('platform_role', 25)->nullable(); // Tipo di wallet (es. 'metamask', 'sollet')
            $table->float('royalty_mint')->nullable(); // Percentuale della prima vendita
            $table->float('royalty_rebind')->nullable(); // Percentuale del mercato secondario
            $table->boolean('is_anonymous')->default(true); // Indica se il wallet è anonimo
            $table->json('metadata')->nullable(); // Eventuali dati dinamici
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
