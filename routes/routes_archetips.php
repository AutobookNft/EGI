<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ArcheTips Routes (standalone)
|--------------------------------------------------------------------------
|
| File di rotte separato per il minisito Collector e gli endpoint a cui
| punta il template. Lo registrerai tu (es. in RouteServiceProvider oppure
| in routes/web.php con: require base_path('routes/archetips.php'); )
|
| Nota: se alcuni endpoint sono già definiti altrove, rimuovi o commenta
| le voci duplicate qui sotto per evitare conflitti di naming.
*/

Route::middleware('web')->group(function () {
    // Minisito Collector (Blade standalone: resources/views/collector.blade.php)
    Route::get('/home/collector', function () {
        return view('collector');
    })->name('collector.page');

    // Catalogo: EGI da attivare
    Route::get('/egi/attivare', [\App\Http\Controllers\EgiCatalogController::class, 'index'])
        ->name('egi.catalogo.attivare');

    // Profilo Collector
    Route::get('/collector/profile', [\App\Http\Controllers\CollectorProfileController::class, 'show'])
        ->name('collector.profile');

    // Community Board
    Route::get('/community', [\App\Http\Controllers\Community\BoardController::class, 'index'])
        ->name('community.board');

    // Policy: Privacy
    Route::get('/policy/privacy', [\App\Http\Controllers\PolicyController::class, 'privacy'])
        ->name('policy.privacy');
});