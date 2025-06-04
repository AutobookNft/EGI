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
            $table->string('username', 40)->nullable()->unique();
            $table->string('usertype', 20)->default('creator');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_collection_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('created_via', 100)->nullable();

            // Add language and wallet fields
            $table->string('language', 2)->nullable();
            $table->text('wallet')->nullable();
            $table->string('personal_secret')->nullable();
            $table->boolean('is_weak_auth')->unique();
            $table->decimal('wallet_balance', 20, 4)->default(0.00)->nullable();
            $table->boolean('terms')->default(false)->nullable();

            $table->string('icon_style', 20)->nullable();

            // Biography fields
            $table->string('bio_title', 50)->nullable();
            $table->text('bio_story')->nullable();

            // Job fields
            $table->string('title', 50)->nullable();
            $table->string('job_role', 40)->nullable();


            // Address fields
            $table->string('street')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('zip', 20)->nullable();

            // Phone fields
            $table->string('home_phone', 20)->nullable();
            $table->string('cell_phone', 20)->nullable();
            $table->string('work_phone', 20)->nullable();

            // Social media fields
            $table->text('site_url')->nullable();
            $table->text('facebook')->nullable();
            $table->text('social_x')->nullable();
            $table->text('tiktok')->nullable();
            $table->text('instagram')->nullable();
            $table->text('snapchat')->nullable();
            $table->text('twitch')->nullable();
            $table->text('linkedin')->nullable();
            $table->text('discord')->nullable();
            $table->text('telegram')->nullable();
            $table->string('other')->nullable();

            // Personal info
            $table->date('birth_date')->nullable();

            // Tax and document fields
            $table->string('fiscal_code', 16)->nullable()->unique();
            $table->string('tax_id_number', 11)->nullable()->unique();
            $table->string('doc_typo', 30)->nullable();
            $table->string('doc_num', 30)->nullable()->unique();
            $table->date('doc_issue_date')->nullable();
            $table->date('doc_expired_date')->nullable();
            $table->string('doc_issue_from')->nullable();
            $table->string('doc_photo_path_f', 2048)->nullable();
            $table->string('doc_photo_path_r', 2048)->nullable();

            // Organization fields
            $table->string('org_name')->nullable();
            $table->string('org_email', 256)->nullable();
            $table->string('org_street')->nullable();
            $table->string('org_city', 100)->nullable();
            $table->string('org_region', 100)->nullable();
            $table->string('org_state', 20)->nullable();
            $table->string('org_zip', 20)->nullable();
            $table->string('org_site_url', 2048)->nullable();
            $table->text('annotation')->nullable();
            $table->string('org_phone_1', 20)->nullable();
            $table->string('org_phone_2', 20)->nullable();
            $table->string('org_phone_3', 20)->nullable();
            $table->string('rea', 30)->nullable()->unique();
            $table->string('org_fiscal_code', 20)->nullable()->unique();
            $table->string('org_vat_number', 20)->nullable()->unique();

            $table->timestamps();
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
