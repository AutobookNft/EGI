<?php

use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Notifications\Gdpr\GdprNotificationResponseController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Multi-Currency API Routes (Mixed Security Approach)
|--------------------------------------------------------------------------
|
| Implements "Think FIAT, Operate ALGO" architecture with mixed security:
| - PUBLIC routes: Anonymous users can access default USD rates for header badge
| - PROTECTED routes: Authenticated users get personalized currency preferences
|
| Security Model:
| 🔓 Public: /currency/* (no auth required)
| 🔒 Protected: /user/* (auth required)
|
*/

// === PUBLIC Currency Routes (No Authentication Required) ===
// Perfect for anonymous users viewing header currency badge
Route::prefix('currency')->name('api.currency.')->group(function () {
    // Get specific currency rate (EUR, USD, GBP)
    Route::get('/rate/{fiatCurrency}', [App\Http\Controllers\Api\CurrencyController::class, 'getRate'])
        ->name('rate.specific');

    // Get all supported currency rates
    Route::get('/rates/all', [App\Http\Controllers\Api\CurrencyController::class, 'getAllRates'])
        ->name('rates.all');

    // Get default USD rate for anonymous users
    Route::get('/rate/default', [App\Http\Controllers\Api\CurrencyController::class, 'getDefaultRate'])
        ->name('rate.default');

    // FIAT to ALGO conversion utility
    Route::post('/convert/fiat-to-algo', [App\Http\Controllers\Api\CurrencyController::class, 'convertFiatToAlgo'])
        ->name('convert.fiat-to-algo');

    // Legacy route per retrocompatibilità
    Route::get('/algo-exchange-rate', [App\Http\Controllers\Api\CurrencyController::class, 'getAlgoExchangeRate'])
        ->name('legacy.algo-rate');
});

// === PROTECTED Legacy Currency Route (for authenticated users) ===
// MOVED TO web.php - queste sono chiamate interne, non API esterne

// 🚀 Portfolio API Routes - NEW (usando auth session invece di sanctum per ora)
Route::middleware(['web'])->group(function () {
    Route::get('/portfolio/status-updates', [App\Http\Controllers\Api\PortfolioApiController::class, 'getStatusUpdates'])
        ->name('api.portfolio.status-updates');
    Route::get('/portfolio', [App\Http\Controllers\Api\PortfolioApiController::class, 'getPortfolio'])
        ->name('api.portfolio.get');
    Route::get('/portfolio/egi/{egiId}/status', [App\Http\Controllers\Api\PortfolioApiController::class, 'getEgiStatus'])
        ->name('api.portfolio.egi-status');
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // ... altre rotte autenticate

    // Notification Status Routes
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Notifications\NotificationStatusController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // Rotta per marcare come letta (già esistente)
    Route::patch('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\Notifications\NotificationDetailsController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // Rotta per marcare come letta (per notifiche semplici)
    Route::patch('/notifications/{notification}/mark-as-read', [App\Http\Controllers\Notifications\NotificationDetailsController::class, 'markAsRead'])->name('notifications.mark-as-read.patch');

    // // === GDPR Interactive Notification Routes ===
    // Route::prefix('notifications/{notification}/gdpr')
    //     ->name('notifications.gdpr.')
    //     ->group(function () {

    //         // // Rotta per la conferma semplice (rate limit standard)
    //         Route::patch('/confirm', [GdprNotificationResponseController::class, 'confirm'])->name('confirm');

    //         // Rotta per la revoca semplice (rate limit standard)
    //         Route::patch('/revoke', [GdprNotificationResponseController::class, 'revoke'])->name('revoke');

    //         // Fortino Digitale #2: Rate Limiting restrittivo per l'azione di sicurezza
    //         // Permette massimo 3 chiamate ogni ora per prevenire abusi del protocollo di allerta.
    //         Route::patch('/disavow', [GdprNotificationResponseController::class, 'disavow'])
    //             ->name('disavow')
    //             ->middleware('throttle:3,60');
    //     });
});

// API Routes
Route::name('api.')->group(function () {




    // API di configurazione per le definizioni degli errori
    Route::get('/error-definitions', [App\Http\Controllers\Api\AppConfigController::class, 'getErrorDefinitions'])
        ->name('error.definitions');

    // Currency system configuration
    Route::get('/currency-config', [App\Http\Controllers\Api\AppConfigController::class, 'getCurrencyConfig'])
        ->name('currency.config');
});


// === GDPR Interactive Notification Routes ===

/*
|--------------------------------------------------------------------------
| Biography API Routes (API-First)
|--------------------------------------------------------------------------
|
| RESTful API endpoints for biography management
| Authentication: Sanctum
| Version: 2.0.0 (API-First)
|
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // Upload immagini da Trix editor
    Route::post('/biographies/trix-image', [App\Http\Controllers\Api\BiographyController::class, 'uploadTrixImage']);

    // Biography CRUD
    Route::post('/biographies', [App\Http\Controllers\Api\BiographyController::class, 'save']);
    Route::get('/biographies/{id}', [App\Http\Controllers\Api\BiographyController::class, 'fetch']);
    Route::delete('/biographies/{id}', [App\Http\Controllers\Api\BiographyController::class, 'delete']);
    Route::get('/biographies', [App\Http\Controllers\Api\BiographyController::class, 'list']);
});