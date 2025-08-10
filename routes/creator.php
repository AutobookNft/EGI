<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


// Creator Home Routes
// routes/web.php - Aggiungi queste routes

// Creator Home Routes
Route::prefix('creator')->name('creator.')->group(function () {

    // Home page del creator
    Route::get('/{id}', [CreatorHomeController::class, 'home'])
        ->where('id', '[0-9]+')
        ->name('home');

    // Home page del creator
    Route::get('/', [CreatorHomeController::class, 'index'])->name('index');

    // Sezioni già funzionanti
    Route::get('/{id}/collections', [CreatorHomeController::class, 'collections'])
        ->where('id', '[0-9]+')
        ->name('collections');

    Route::get('/{id}/collection/{collection}', [CreatorHomeController::class, 'showCollection'])
        ->where(['id' => '[0-9]+', 'collection' => '[0-9]+']) // Validiamo entrambi come numerici
        ->name('collection.show');

    // Portfolio reale
    Route::get('/{id}/portfolio', [CreatorHomeController::class, 'portfolio'])
        ->where('id', '[0-9]+')
        ->name('portfolio');

    Route::get('/{id}/biography', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->name('biography');

    Route::get('/{id}/journey', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->name('journey');

    Route::get('/{id}/impact', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->name('impact');

    Route::get('/{id}/community', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->name('community');
});