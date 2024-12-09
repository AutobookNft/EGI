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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index(); // Creatore della collezione
            $table->foreignId('creator_id')->nullable()->index(); // Creatore della collezione
            $table->bigInteger('epp_id')->nullable()->index(); // ID dell'EPP
            $table->foreignId('owner_id')->nullable()->index(); // ID dell'owner della collection, (utilizzato solo in caso di EGI_Asset)
            $table->string('collection_name')->index()->nullable(); // Nome della collection (se diverso da name)
            $table->boolean('show')->index()->nullable()->default(1); // Visibilità della collezione
            $table->boolean('personal_team')->nullable(); // Per compatibilità con 'teams'
            $table->char('creator')->index()->nullable(); // Riferimento al creatore
            $table->char('owner_wallet')->index()->nullable(); // Wallet del proprietario
            $table->string('address', 100)->index()->nullable(); // Indirizzo
            $table->bigInteger('EGI_asset_id')->nullable()->index(); // ID dell'EGI Asset
            $table->text('description')->nullable(); // Descrizione
            $table->string('type', 10)->index()->nullable(); // Tipo della collection
            $table->string('path_image_banner', 1024)->nullable(); // Percorso immagine banner
            $table->string('path_image_card', 1024)->nullable(); // Percorso immagine card
            $table->string('path_image_avatar', 1024)->nullable(); // Percorso immagine avatar
            $table->string('path_image_EGI', 1024)->nullable(); // Percorso immagine EGI
            $table->string('url_collection_site')->nullable(); // URL sito della collection
            $table->integer('position')->index()->nullable(); // Posizione
            $table->string('token')->index()->nullable(); // Token associato
            $table->integer('EGI_number')->nullable(); // Numero EGI
            $table->text('EGI_asset_roles')->nullable(); // Ruoli EGI
            $table->float('floor_price')->nullable(); // Prezzo minimo
            $table->string('path_image_to_ipfs')->nullable(); // Percorso immagine per IPFS
            $table->string('url_image_ipfs')->nullable(); // URL immagine su IPFS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
