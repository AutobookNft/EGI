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
        Schema::create('bar_context_menus', function (Blueprint $table) {
            $table->id(); // Chiave primaria auto-incrementante
            $table->string('context');
            $table->string('summary');
            $table->integer('position')->nullable();
            $table->string('name');
            $table->string('route');
            $table->string('permission')->nullable();
            $table->text('tip')->nullable();
            $table->text('icon')->nullable();
            $table->timestamps();

            // Foreign key constraint con chiave composta
            $table->foreign(['context', 'summary'])
                  ->references(['context', 'summary'])
                  ->on('bar_context_summaries')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_has_menus');
    }
};
