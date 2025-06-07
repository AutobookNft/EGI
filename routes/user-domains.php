<?php

/**
 * @Oracode Routes: User Domains System (OS1-Compliant)
 * 🎯 Purpose: Define routes for all user domain management (Personal Data, Organization, Documents, etc.)
 * 🛡️ Privacy: GDPR-compliant routing with authentication and permission checks
 * 🧱 Core Logic: FegiAuth middleware integration with domain-specific permissions
 * 🌍 Scale: Scalable routing structure for 6 MVP countries and future expansion
 * ⏰ MVP: Critical routing for 30 June deadline
 *
 * @package routes
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - User Domains)
 * @deadline 2025-06-30
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PersonalDataController;
use App\Http\Controllers\User\OrganizationDataController;
use App\Http\Controllers\User\DocumentsController;
use App\Http\Controllers\User\InvoicePreferencesController;

/*
|--------------------------------------------------------------------------
| User Domains Routes
|--------------------------------------------------------------------------
|
| These routes handle all user domain management functionality including:
| - Personal Data Management (GDPR-compliant)
| - Organization Data (for business users)
| - Documents Management
| - Invoice Preferences
|
| All routes require authentication (weak or strong via FegiAuth)
| and appropriate domain-specific permissions.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Personal Data Domain Routes
    |--------------------------------------------------------------------------
    |
    | Handles GDPR-compliant personal data management with:
    | - Real-time country-specific validation
    | - Consent management
    | - Data export (Right to Data Portability)
    | - Data deletion requests (Right to Erasure)
    | - Full audit trail
    |
    */
    Route::prefix('user/domains')->name('user.domains.')->group(function () {
        Route::get('personal-data', [PersonalDataController::class, 'index'])
            ->name('personal-data');
        Route::put('personal-data', [PersonalDataController::class, 'update'])
            ->name('personal-data.update');
        Route::get('personal-data/export', [PersonalDataController::class, 'export'])
            ->name('personal-data.export');
        Route::delete('personal-data', [PersonalDataController::class, 'destroy'])
            ->name('personal-data.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Organization Data Domain Routes
    |--------------------------------------------------------------------------
    |
    | Handles business/organization data management for:
    | - Creator accounts
    | - Enterprise accounts
    | - EPP Entity accounts
    |
    */
    Route::prefix('user/domains/organization-data')->name('user.domains.organization-data.')->group(function () {

        // Main organization data management page
        Route::get('/', [OrganizationDataController::class, 'index'])
            ->name('index')
            ->middleware('can:edit_own_organization_data');

        // Update organization data
        Route::put('/', [OrganizationDataController::class, 'update'])
            ->name('update')
            ->middleware('can:edit_own_organization_data');

        // Verify business information (for seller verification)
        Route::post('/verify-business', [OrganizationDataController::class, 'verifyBusiness'])
            ->name('verify-business')
            ->middleware(['can:edit_own_organization_data', 'strong_auth_required']);

        // Upload business documents
        Route::post('/upload-documents', [OrganizationDataController::class, 'uploadDocuments'])
            ->name('upload-documents')
            ->middleware(['can:edit_own_organization_data', 'strong_auth_required']);
    });

    /*
    |--------------------------------------------------------------------------
    | Documents Domain Routes
    |--------------------------------------------------------------------------
    |
    | Handles user document management including:
    | - ID documents
    | - Business certificates
    | - Tax documents
    | - Custom user documents
    |
    */
    Route::prefix('user/domains/documents')->name('user.domains.documents.')->group(function () {

        // Main documents management page
        Route::get('/', [DocumentsController::class, 'index'])
            ->name('index')
            ->middleware('can:manage_own_documents');

        // Upload new document
        Route::post('/upload', [DocumentsController::class, 'upload'])
            ->name('upload')
            ->middleware('can:manage_own_documents');

        // Update document metadata
        Route::put('/{document}', [DocumentsController::class, 'update'])
            ->name('update')
            ->middleware('can:manage_own_documents');

        // Download document
        Route::get('/{document}/download', [DocumentsController::class, 'download'])
            ->name('download')
            ->middleware('can:manage_own_documents');

        // Delete document
        Route::delete('/{document}', [DocumentsController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:manage_own_documents');

        // Verify document authenticity
        Route::post('/{document}/verify', [DocumentsController::class, 'verify'])
            ->name('verify')
            ->middleware(['can:manage_own_documents', 'strong_auth_required']);
    });

    /*
    |--------------------------------------------------------------------------
    | Invoice Preferences Domain Routes
    |--------------------------------------------------------------------------
    |
    | Handles user invoice and billing preferences:
    | - Invoice format preferences
    | - Billing address management
    | - Tax settings
    | - Payment method preferences
    |
    */
    Route::prefix('user/domains/invoice-preferences')->name('user.domains.invoice-preferences.')->group(function () {

        // Main invoice preferences page
        Route::get('/', [InvoicePreferencesController::class, 'index'])
            ->name('index')
            ->middleware('can:manage_own_invoice_preferences');

        // Update invoice preferences
        Route::put('/', [InvoicePreferencesController::class, 'update'])
            ->name('update')
            ->middleware('can:manage_own_invoice_preferences');

        // Preview invoice format
        Route::post('/preview', [InvoicePreferencesController::class, 'preview'])
            ->name('preview')
            ->middleware('can:manage_own_invoice_preferences');

        // Export invoice history
        Route::post('/export-history', [InvoicePreferencesController::class, 'exportHistory'])
            ->name('export-history')
            ->middleware('can:manage_own_invoice_preferences');
    });

    /*
    |--------------------------------------------------------------------------
    | Common User Domain Routes
    |--------------------------------------------------------------------------
    |
    | Shared functionality across all user domains:
    | - Identity verification
    | - Account upgrade (weak to strong auth)
    | - Domain summary/dashboard
    |
    */
    Route::prefix('user/domains')->name('user.domains.')->group(function () {

        // User domains dashboard/summary
        Route::get('/', function () {
            return redirect()->route('profile.show');
        })->name('index');

        // Identity verification for sensitive operations
        Route::get('/identity-verification', function () {
            return view('user.domains.identity-verification');
        })->name('identity-verification');

        // Process identity verification
        Route::post('/identity-verification', function () {
            // Verify user identity (password confirmation, 2FA, etc.)
            session(['identity_verified_at' => now()]);

            $returnUrl = session('verification_return_url', route('profile.show'));
            return redirect($returnUrl)->with('success', __('user_domains.identity_verified'));
        })->name('identity-verification.process');

        // Account upgrade (weak to strong auth)
        Route::get('/upgrade', function () {
            return view('user.domains.upgrade');
        })->name('upgrade');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes for User Domains
|--------------------------------------------------------------------------
|
| API endpoints for AJAX requests from TypeScript domain logic.
| These routes return JSON responses for frontend consumption.
|
*/
Route::middleware(['auth:sanctum', 'verified'])->prefix('api/user/domains')->name('api.user.domains.')->group(function () {

    // Personal Data API endpoints
    Route::prefix('personal-data')->name('personal-data.')->group(function () {

        // Validate single field (real-time validation)
        Route::post('/validate-field', [PersonalDataController::class, 'validateField'])
            ->name('validate-field')
            ->middleware('can:manage_profile');

        // Get validation rules for country
        Route::get('/validation-rules/{country}', [PersonalDataController::class, 'getValidationRules'])
            ->name('validation-rules')
            ->middleware('can:manage_profile');

        // Check data completeness
        Route::get('/completeness', [PersonalDataController::class, 'getDataCompleteness'])
            ->name('completeness')
            ->middleware('can:manage_profile');
    });

    // Organization Data API endpoints
    Route::prefix('organization-data')->name('organization-data.')->group(function () {

        // Validate business information
        Route::post('/validate-business', [OrganizationDataController::class, 'validateBusiness'])
            ->name('validate-business')
            ->middleware('can:edit_own_organization_data');

        // Check seller verification status
        Route::get('/seller-status', [OrganizationDataController::class, 'getSellerStatus'])
            ->name('seller-status')
            ->middleware('can:edit_own_organization_data');
    });

    // Documents API endpoints
    Route::prefix('documents')->name('documents.')->group(function () {

        // Get document list
        Route::get('/', [DocumentsController::class, 'apiIndex'])
            ->name('index')
            ->middleware('can:manage_own_documents');

        // Check document verification status
        Route::get('/{document}/status', [DocumentsController::class, 'getVerificationStatus'])
            ->name('status')
            ->middleware('can:manage_own_documents');
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes for User Domains
|--------------------------------------------------------------------------
|
| Webhook endpoints for external services (document verification,
| business verification, etc.)
|
*/
Route::prefix('webhooks/user-domains')->name('webhooks.user-domains.')->group(function () {

    // Document verification webhook
    Route::post('/document-verification', [DocumentsController::class, 'handleVerificationWebhook'])
        ->name('document-verification')
        ->middleware('webhook_signature');

    // Business verification webhook
    Route::post('/business-verification', [OrganizationDataController::class, 'handleVerificationWebhook'])
        ->name('business-verification')
        ->middleware('webhook_signature');
});
