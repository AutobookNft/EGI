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
        Schema::create('bar_context_summaries', function (Blueprint $table) {
            $table->id(); // Chiave primaria auto-incrementante
            $table->string('context'); // Collegato a `bar_contexts`
            $table->string('summary');
            $table->boolean('head')->default(false); // campo head: true or false
            $table->integer('position')->nullable();
            $table->string('route')->nullable();
            $table->string('permission')->nullable();
            $table->text('tip')->nullable();
            $table->text('icon')->nullable();
            $table->timestamps();

            // Indice unico composto
            $table->unique(['context', 'summary']);

            // Foreign key constraint
            $table->foreign('context')
                  ->references('context')
                  ->on('bar_contexts')
                  ->onDelete('cascade');
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_context_summaries');
    }
};
