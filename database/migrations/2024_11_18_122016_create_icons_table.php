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
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome dell'icona, es: 'camera', 'user'
            $table->string('style'); // Stile dell'icona, es: 'elegant', 'classic'
            $table->string('type'); // Tipo dell'icona, es: 'solid', 'regular', 'light', 'duotone', 'brands'
            $table->string('class'); // Altezza dell'icona, es: '448'
            $table->text('html'); // Contenuto HTML generico (SVG, tag <i>, ecc.)
            $table->string('host'); // Host di provenienza dell'icona, es: 'fontawesome.com'
            $table->string('name_on_host'); // Nome dell'icona sul host di provenienza, es: 'camera'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};
