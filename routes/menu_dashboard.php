<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardStaticController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EgiController;
use App\Livewire\Collections\CreateCollection;

Route::prefix('collections')->name('collections.')->group(function () {
    Route::get('/', [CollectionsController::class, 'index'])->name('index');

    Route::post('/store', [CollectionsController::class, 'store'])->name('store');

    // Altre rotte per le collezioni...
});


// Dashboard e sezioni principali (tutte protette da autenticazione)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Dashboard principale
    // Route::get('/dashboard', [DashboardStaticController::class, 'index'])->name('dashboard'); // Ritorna al nome originale 'dashboard'

    // Collections routes
    Route::prefix('collections')->name('collections.')->group(function () {
        // Route::post('/create', [CreateCollection::class, 'create'])->name('create');
        Route::get('/{collection}/staff', [CollectionsController::class, 'staff'])->name('staff');
        // Altre rotte per le collezioni...
    });

    // Statistics routes - CORRETTE PER IL NUOVO CONTROLLER
    // Il prefisso 'statistics' e il nome 'statistics.' rimangono locali a questo gruppo.
    // L'URL base per questo gruppo sarà /statistics (non /dashboard/statistics a meno che non sia in un gruppo esterno con quel prefisso)
    Route::prefix('dashboard/statistics')->name('statistics.')->group(function () {
        // Rotta per visualizzare la PAGINA HTML delle statistiche (scheletro)
        // URL: /statistics
        // Nome: statistics.index
        Route::get('/', [StatisticsController::class, 'showStatisticsPage'])->name('index');

        // Rotta API per ottenere i DATI JSON delle statistiche (chiamata da JavaScript)
        // URL: /statistics/data
        // Nome: statistics.data.json
        Route::get('/data', [StatisticsController::class, 'getStatisticsDataAsJson'])->name('data.json');

        // Rotta per pulire la cache
        // URL: /statistics/clear-cache
        // Nome: statistics.clear-cache
        Route::post('/clear-cache', [StatisticsController::class, 'clearCache'])->name('clear-cache');

        // Commentata perché non abbiamo implementato il metodo 'details' nel controller
        // Route::get('/{collection}/details', [StatisticsController::class, 'details'])->name('details');

        // La tua rotta summary originale era '/api/statistics/summary', la manteniamo così per ora
        // Se vuoi che sia sotto /statistics/summary-json, decommenta la riga sotto
        // e assicurati che il JS chiami il nome corretto (es. route('statistics.summary.json'))
        // Route::get('/summary-json', [StatisticsController::class, 'summary'])->name('summary.json');
    });

    // Personal data routes
    // Route::prefix('personal-data')->name('personal-data.')->group(function () {
    //     Route::get('/account', [PersonalDataController::class, 'account'])->name('account');
    //     Route::get('/bio', [PersonalDataController::class, 'bio'])->name('bio');
    //     Route::put('/update-account', [PersonalDataController::class, 'updateAccount'])->name('update-account');
    //     Route::put('/update-bio', [PersonalDataController::class, 'updateBio'])->name('update-bio');
    //     // Altre rotte dati personali...
    // });

    // Wallet routes
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        // Altre rotte wallet...
    });

    // GDPR & Privacy routes
    // Route::prefix('gdpr')->name('gdpr.')->group(function () {
    //     Route::get('/consents', [GdprController::class, 'consents'])->name('consents');
    //     Route::get('/delete-account', [GdprController::class, 'deleteAccount'])->name('delete-account');
    //     Route::post('/update-consents', [GdprController::class, 'updateConsents'])->name('update-consents');
    //     Route::post('/download-data', [GdprController::class, 'downloadData'])->name('download-data');
    //     Route::delete('/account', [GdprController::class, 'destroyAccount'])->name('destroy-account');
    //     // Altre rotte GDPR...
    // });

    // Documentation routes
    Route::prefix('documentation')->name('documentation.')->group(function () {
        Route::get('/', [DocumentationController::class, 'index'])->name('index');
        // Altre rotte documentazione...
    });

    // EGI routes
    Route::prefix('egi')->name('egi.')->group(function () {
        Route::get('/upload', [EgiController::class, 'uploadPage'])->name('upload.page');
        Route::post('/upload', [EgiController::class, 'upload'])->name('upload');
        // Altre rotte EGI...
    });

    // LA TUA ROTTA ORIGINALE PER SUMMARY, FUORI DAL GRUPPO statistics
    // Questa rotta era definita DENTRO il gruppo statistics ma con un path che la portava fuori (api/statistics/summary)
    // La sposto qui per renderla coerente con il path.
    // URL: /api/statistics/summary
    // Nome: statistics.summary (come nel tuo file originale)
    // Se il controller per questa è StatisticsController, e il gruppo middleware è lo stesso, può stare qui.
    // Altrimenti, se deve essere un endpoint API "puro" senza il middleware 'verified' o 'auth:sanctum' web,
    // andrebbe definita in routes/api.php con middleware 'auth:api' (o Sanctum per API).
    // Per ora la lascio qui assumendo che i middleware siano appropriati.
    Route::get('/api/statistics/summary', [StatisticsController::class, 'summary'])->name('statistics.summary');
}); // Fine del gruppo Route::middleware(['auth:sanctum', 'verified'])