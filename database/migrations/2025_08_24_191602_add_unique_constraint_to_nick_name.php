<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Prima pulisco eventuali duplicati di nickname
        // Trova tutti i nickname duplicati e li rende unici aggiungendo un numero
        $duplicates = DB::table('users')
            ->select('nick_name')
            ->whereNotNull('nick_name')
            ->where('nick_name', '!=', '')
            ->groupBy('nick_name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nick_name');

        foreach ($duplicates as $nickname) {
            $users = DB::table('users')
                ->where('nick_name', $nickname)
                ->orderBy('id')
                ->get();

            $counter = 1;
            foreach ($users as $user) {
                if ($counter > 1) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['nick_name' => $nickname . '_' . $counter]);
                }
                $counter++;
            }
        }

        // Ora aggiungo il constraint di unicità
        Schema::table('users', function (Blueprint $table) {
            $table->unique('nick_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nick_name']);
        });
    }
};
