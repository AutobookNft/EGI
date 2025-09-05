<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('notification_payload_invitations', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('notification_payload_invitations', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
