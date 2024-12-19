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
        $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');          // Team associato alla collection
        $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('cascade'); // Creator della collection (specifico)
        $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade');   // Owner della collection

        // campo del ruolo
        $table->string('role')->default('creator')->after('user_id')->index();

        // Campi per identificatori esterni
        $table->bigInteger('epp_id')->nullable()->index();          // ID del progetto EPP
        $table->bigInteger('EGI_asset_id')->nullable()->index();    // ID dell'asset EGI

        // Dati della collection
        $table->string('collection_name')->index()->nullable();
        $table->text('description')->nullable();
        $table->string('type', 10)->index()->nullable();

        // Immagini e Percorsi
        $table->string('path_image_banner', 1024)->nullable();
        $table->string('path_image_card', 1024)->nullable();
        $table->string('path_image_avatar', 1024)->nullable();
        $table->string('path_image_EGI', 1024)->nullable();
        $table->string('path_image_to_ipfs')->nullable();
        $table->string('url_image_ipfs')->nullable();
        $table->string('url_collection_site')->nullable();

        // Altri Campi
        $table->boolean('is_published')->index()->default(1);
        $table->boolean('personal_team')->nullable();
        $table->char('creator')->index()->nullable();
        $table->char('owner_wallet')->index()->nullable();
        $table->string('address', 100)->index()->nullable();
        $table->integer('position')->index()->nullable();
        $table->string('token')->index()->nullable();
        $table->integer('EGI_number')->nullable();
        $table->text('EGI_asset_roles')->nullable();
        $table->float('floor_price')->nullable();

        // Timestamps
        $table->timestamps();
        $table->softDeletes(); // Soft delete per una gestione più flessibile delle eliminazioni
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
