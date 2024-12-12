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
        Schema::create('egi_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('egi_id')->constrained('egi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Utente che ha effettuato la modifica
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('action'); // create, update, delete
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_audits');
    }
};
