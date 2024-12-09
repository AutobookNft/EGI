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
        Schema::create('team_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Collegamento all'utente
            // user_role
            $table->string('user_role', 25)->nullable($value = true);
            // indirizzo wallet
            $table->string('wallet', 255)->nullable($value = true);
            // dividendo all'atto della prima vendita
            $table->float('royalty_mint')->nullable($value = true);
            // royalty del secondo mercato
            $table->float('royalty_rebind')->nullable($value = true);
            $table->boolean('status')->nullable($value = true);

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
        Schema::dropIfExists('team_wallets');
    }
};
