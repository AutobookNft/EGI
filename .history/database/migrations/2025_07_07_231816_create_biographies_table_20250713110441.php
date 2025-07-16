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
        Schema::create('biographies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('single'); // 'single' or 'chapters'
            $table->string('title');
            $table->text('content')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_public']);
            $table->index(['slug']);
            $table->index(['type']);
            $table->index(['is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biographies');
    }
};
