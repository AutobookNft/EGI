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

        // Relazioni
        $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('cascade'); // Creator della collection
        $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade');   // Owner della collection

        // Identificatori esterni
        $table->bigInteger('epp_id')->nullable()->index();          // ID del progetto EPP
        $table->bigInteger('EGI_asset_id')->nullable()->index();    // ID dell'asset EGI

        // Dati della collection
        $table->string('collection_name')->index()->nullable();     // Nome della collection
        $table->text('description')->nullable();                   // Descrizione
        $table->string('type', 25)->index()->nullable();           // Tipo (es. standard, single_egi)

        // Immagini e percorsi
        $table->string('image_banner', 1024)->nullable();          // Banner
        $table->string('image_card', 1024)->nullable();            // Card
        $table->string('image_avatar', 1024)->nullable();          // Avatar
        $table->string('path_image_to_ipfs')->nullable();          // Percorso immagine IPFS
        $table->string('url_image_ipfs')->nullable();              // URL immagine IPFS
        $table->string('url_collection_site')->nullable();         // URL del sito della collection

        // Stato e pubblicazione
        $table->string('status')->default('draft')->index();       // Stato: draft, pending_approval, published
        $table->string('created_via', 255);                        // Metodo di creazione (es. web, api)
        $table->boolean('is_published')->default(false)->index();  // Booleano per indicare se è pubblicata

        // Dati associati agli EGI
        $table->integer('position')->nullable();                  // Posizione della collection
        $table->integer('EGI_number')->nullable();                // Numero di EGI nella collection
        $table->float('floor_price')->nullable();                 // Prezzo minimo
        $table->text('EGI_asset_roles')->nullable();              // Ruoli relativi all'EGI

        // Timestamps
        $table->timestamps();
        $table->softDeletes(); // Soft delete per gestione più flessibile delle eliminazioni
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
