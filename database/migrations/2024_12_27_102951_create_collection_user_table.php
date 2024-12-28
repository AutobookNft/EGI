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
        Schema::create('collection_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade'); // Relazione con collections
            $table->string('status')->default('pending')->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relazione con users
            $table->string('role')->nullable(); // Ruolo (es. admin, editor, viewer)
            $table->boolean('is_owner')->default(false); // Indica il proprietario della collection
            $table->timestamp('joined_at')->nullable(); // Data di aggiunta
            $table->timestamp('removed_at')->nullable(); // Data di rimozione
            $table->json('metadata')->nullable(); // Eventuali dati dinamici
            $table->timestamps();
            $table->unique(['collection_id', 'user_id']); // Relazione unica per collection-user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_user');
    }
};
