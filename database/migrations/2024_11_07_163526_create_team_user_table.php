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
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable();
            $table->string('wallet', 255)->nullable($value = true);
            // dividendo all'atto della prima vendita
            $table->float('royalty_mint')->nullable($value = true);
            // royalty del secondo mercato
            $table->float('royalty_rebind')->nullable($value = true);
            $table->string('approval', 25)->default('approved');
            $table->json('previous_values')->nullable();
            $table->string('status', 25)->nullable($value = true);
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
