<?php
// database/migrations/2025_06_06_add_status_to_user_consents.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Gdpr\ConsentStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_consents', function (Blueprint $table) {
            $table->string('status', 20)->default(ConsentStatus::ACTIVE->value)->after('consent_type');
            $table->timestamp('withdrawn_at')->nullable()->after('withdrawal_method');
        });
    }

    public function down(): void
    {
        Schema::table('user_consents', function (Blueprint $table) {
            $table->dropColumn(['status', 'withdrawn_at']);
        });
    }
};
