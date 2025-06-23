<?php

// ═══════════════════════════════════════════════════════════════════════════════════
// AGGIUNGERE al file routes/gdpr.php ESISTENTE
// Route per il nuovo GdprLegalController
// ═══════════════════════════════════════════════════════════════════════════════════

// ═══ IMPORT del nuovo controller ═══
use App\Http\Controllers\GdprLegalController;
use Illuminate\Support\Facades\Route;

// ═══ LEGAL TERMS MANAGEMENT ROUTES ═══
// Protected routes for legal team
Route::middleware(['auth', 'permission:legal.dashboard.access'])
    ->prefix('legal')
    ->name('legal.')
    ->group(function () {

        // ═══ LEGAL EDITOR ROUTES ═══

        // Editor interface for legal team
        Route::get('/terms/edit/{userType}/{locale?}', [GdprLegalController::class, 'editTerms'])
            ->middleware('permission:legal.terms.edit')
            ->where('userType', 'collector|creator|patron|epp|company|trader_pro')
            ->where('locale', 'it|en|es|pt|fr|de')
            ->defaults('locale', 'it')
            ->name('edit');

        // Save new terms version
        Route::post('/terms/save/{userType}/{locale}', [GdprLegalController::class, 'saveTerms'])
            ->middleware('permission:legal.terms.create_version')
            ->where('userType', 'collector|creator|patron|epp|company|trader_pro')
            ->where('locale', 'it|en|es|pt|fr|de')
            ->name('save');

        // Version history for audit
        Route::get('/terms/history/{userType}/{locale?}', [GdprLegalController::class, 'termsHistory'])
            ->middleware('permission:legal.history.view')
            ->where('userType', 'collector|creator|patron|epp|company|trader_pro')
            ->where('locale', 'it|en|es|pt|fr|de')
            ->defaults('locale', 'it')
            ->name('history');
    });

// ═══ PUBLIC LEGAL ROUTES ═══
// Accessible to all users (including guests)
Route::prefix('legal')
    ->name('legal.')
    ->group(function () {

        // Public terms display
        Route::get('/terms/{userType}/{locale?}', [GdprLegalController::class, 'showTerms'])
            ->where('userType', 'collector|creator|patron|epp|company|trader_pro')
            ->where('locale', 'it|en|es|pt|fr|de')
            ->defaults('locale', 'it')
            ->name('terms');

        // Terms acceptance (requires auth)
        Route::post('/terms/accept/{userType}', [GdprLegalController::class, 'acceptTerms'])
            ->middleware('auth')
            ->where('userType', 'collector|creator|patron|epp|company|trader_pro')
            ->name('accept');
    });

// ═══ DASHBOARD INTEGRATION ROUTES ═══
// Quick access routes for dashboard menu system
Route::middleware(['auth', 'permission:legal.terms.edit'])
    ->group(function () {

        // Quick access routes for each user type
        Route::get('/legal/collector', function () {
            return redirect()->route('legal.edit', ['userType' => 'collector', 'locale' => 'it']);
        })->name('legal.collector');

        Route::get('/legal/creator', function () {
            return redirect()->route('legal.edit', ['userType' => 'creator', 'locale' => 'it']);
        })->name('legal.creator');

        Route::get('/legal/patron', function () {
            return redirect()->route('legal.edit', ['userType' => 'patron', 'locale' => 'it']);
        })->name('legal.patron');

        Route::get('/legal/epp', function () {
            return redirect()->route('legal.edit', ['userType' => 'epp', 'locale' => 'it']);
        })->name('legal.epp');

        Route::get('/legal/company', function () {
            return redirect()->route('legal.edit', ['userType' => 'company', 'locale' => 'it']);
        })->name('legal.company');

        Route::get('/legal/trader_pro', function () {
            return redirect()->route('legal.edit', ['userType' => 'trader_pro', 'locale' => 'it']);
        })->name('legal.trader_pro');

        // General legal dashboard
        Route::get('/legal/dashboard', function () {
            return redirect()->route('legal.edit', ['userType' => 'creator', 'locale' => 'it']);
        })->name('legal.dashboard');
    });

// ═══ API ROUTES (for future AJAX functionality) ═══
Route::middleware(['auth', 'permission:legal.dashboard.access'])
    ->prefix('api/legal')
    ->name('legal.api.')
    ->group(function () {

        // Get terms content via AJAX
        Route::get('/content/{userType}/{locale}', function (string $userType, string $locale) {
            $controller = app(GdprLegalController::class);
            // This would need a dedicated API method in the controller
            return response()->json(['message' => 'API endpoint for future implementation']);
        })->name('content');

        // Validate content security via AJAX
        Route::post('/validate', function () {
            // Future implementation for real-time validation
            return response()->json(['valid' => true]);
        })->name('validate');
    });

/*
═══ EXAMPLE COMPLETE INTEGRATION ═══

Esempio di come queste route si integrano con quelle GDPR esistenti:

Route::middleware(['auth'])->group(function () {

    // ═══ EXISTING GDPR ROUTES ═══
    Route::get('/gdpr/profile', [GdprController::class, 'showProfile'])->name('gdpr.profile');
    Route::get('/gdpr/consent', [GdprController::class, 'consent'])->name('gdpr.consent');
    Route::get('/gdpr/export-data', [GdprController::class, 'exportData'])->name('gdpr.export-data');
    Route::get('/gdpr/delete-account', [GdprController::class, 'deleteAccount'])->name('gdpr.delete-account');

    // ═══ NEW LEGAL ROUTES (integrate seamlessly) ═══
    // All the legal routes above work alongside existing GDPR functionality

});

Note:
- GdprController mantiene: consent, privacy, export data, breach reports
- GdprLegalController gestisce: terms editing, versioning, content management
- Perfect separation of concerns with shared services (UEM, ConsentService, AuditService)
*/
