<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @oracode Migration for processing restrictions table
     * @oracode-dimension technical
     * @value-flow Tracks user requests to limit data processing
     * @community-impact Empowers users to control their data processing
     * @transparency-level All restrictions are auditable and traceable
     */
    public function up(): void
    {
        Schema::create('processing_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('restriction_type', 50);
            $table->string('reason', 100);
            $table->text('details');
            $table->json('affected_data_categories')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('lifted_at')->nullable();
            $table->string('lifted_by')->nullable();
            $table->text('lift_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['restriction_type', 'is_active']);
            $table->index('lifted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processing_restrictions');
    }
};
