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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('usertype', 20)->default('creator'); // Default creator type
            $table->foreignId('current_collection_id')->nullable();
            $table->json('consent_summary')->nullable(); // Quick consent lookup
            $table->timestamp('consents_updated_at')->nullable();

            // Processing limitations
            $table->json('processing_limitations')->nullable();
            $table->timestamp('limitations_updated_at')->nullable();

            // Data subject requests tracking
            $table->boolean('has_pending_gdpr_requests')->default(false);
            $table->timestamp('last_gdpr_request_at')->nullable();

            // Account status
            $table->boolean('gdpr_compliant')->default(true);
            $table->timestamp('gdpr_status_updated_at')->nullable();

            // Data retention
            $table->timestamp('data_retention_until')->nullable();
            $table->enum('retention_reason', [
                'active_user',        // Active platform usage
                'legal_obligation',   // Legal requirement to retain
                'pending_request',    // GDPR request in progress
                'contract_obligation' // Contractual obligation
            ])->default('active_user');

            // Privacy preferences
            $table->json('privacy_settings')->nullable();
            $table->string('preferred_communication_method', 20)->default('email');

            // Audit and compliance
            $table->timestamp('last_activity_logged_at')->nullable();
            $table->unsignedInteger('total_gdpr_requests')->default(0);
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('created_via', 100)->nullable();
            // Add language and wallet fields
            $table->string('language', 2)->nullable();
            $table->text('wallet')->nullable();
            $table->string('personal_secret')->nullable();
            $table->boolean('is_weak_auth')->unique()->nullable();
            $table->decimal('wallet_balance', 20, 4)->default(0.00)->nullable();
            $table->boolean('consent')->default(false)->nullable();
            $table->string('icon_style', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Indexes for GDPR operations
            $table->index('has_pending_gdpr_requests');
            $table->index('gdpr_compliant');
            $table->index('last_gdpr_request_at');
            $table->index('data_retention_until');
            $table->index(['gdpr_compliant', 'has_pending_gdpr_requests']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};