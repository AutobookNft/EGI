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
        Schema::create('wallet_change_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_wallet_id')->constrained('team_wallets')->onDelete('cascade');
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_user_id')->constrained('users')->onDelete('cascade');
            $table->string('change_type');
            $table->json('change_details');
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->timestamp('approved_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_change_approvals');
    }
};
