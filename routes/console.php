<?php

use App\Notifications\NotificationExpired;
use App\Services\Notifications\Wallets\CeckAndSetExpired;
use App\Services\Notifications\Wallets\CheckAndSetExpired;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\CustomDatabaseNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

### 📌 1️⃣ COMANDO ARTISAN: NOTIFICHE ATTIVE ###
Artisan::command('notifications:summary', function () {
    $pendingCount = CustomDatabaseNotification::where('outcome', 'pending')->count();
    $expiringSoon = CustomDatabaseNotification::where('outcome', 'pending')
                                ->where('created_at', '<', Carbon::now()->subHours(config('app.notifications.expiration_hours', 72) - 5))
                                ->count();

    $this->info("📌 Notifiche in sospeso: {$pendingCount}");
    $this->info("⏳ Notifiche che scadranno nelle prossime 5 ore: {$expiringSoon}");
})->purpose('Mostra un riepilogo delle notifiche attive')->hourly();


### 📌 2️⃣ JOB AUTOMATICO: SCADENZA DELLE NOTIFICHE ###
Schedule::call(function () {
    app()->make(CheckAndSetExpired::class)->checkAndSetExpired();
})
->name('check-and-set-expired')
->everyMinute()
->withoutOverlapping()
->onOneServer();

### 📌 3️⃣ JOB AUTOMATICO: NOTIFICHE SCADUTE ###
Schedule::command('reservations:process-rankings')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();