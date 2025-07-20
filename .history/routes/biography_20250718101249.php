<?php

/**
 * @Oracode Biography Routes - Web Interface for Personal Biographies
 * 🎯 Purpose: Define all biography-related web routes with proper middleware
 * 🛡️ Security: Auth middleware for CRUD operations, guest access for public viewing
 * 🧱 Core Logic: RESTful routes for biography management + chapter sub-resources
 *
 * @package Routes\Biography
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-03
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BiographyController;
use App\Http\Controllers\Api\BiographyChapterController;
use App\Http\Controllers\Web\BiographyWebController;

// ===================================================================
// 📖 PUBLIC BIOGRAPHY ROUTES (Guest Access)
// ===================================================================

Route::group(['prefix' => 'biographies'], function () {

    /**
     * Public biography discovery and viewing
     */
    Route::get('/', [BiographyController::class, 'publicIndex'])
        ->name('biographies.public.index');

    Route::get('/discover', [BiographyController::class, 'discover'])
        ->name('biographies.discover');

    /**
     * Public biography viewing by slug
     */
    Route::get('/{creator_id}', [BiographyWebController::class, 'show'])
        ->name('biographies.public.show')
        ->where('creator_id', '[a-z0-9\-]+');

    /**
     * Public chapter viewing
     */
    Route::get('/{biography:slug}/chapters/{chapter:slug}', [BiographyChapterController::class, 'publicShow'])
        ->name('biographies.chapters.public.show')
        ->where(['biography' => '[a-z0-9\-]+', 'chapter' => '[a-z0-9\-]+']);
});

// ===================================================================
// 🔐 AUTHENTICATED BIOGRAPHY MANAGEMENT
// ===================================================================

// Route::middleware(['auth', 'verified'])->group(function () {

//     Route::group(['prefix' => 'my/biographies'], function () {

//         /**
//          * Personal biography dashboard
//          */
//         Route::get('/', [BiographyController::class, 'dashboard'])
//             ->name('biographies.dashboard');

//         /**
//          * Biography creation flow
//          */
//         Route::get('/create', [BiographyController::class, 'create'])
//             ->name('biographies.create');

//         Route::post('/create', [BiographyController::class, 'store'])
//             ->name('biographies.store');

//         /**
//          * Individual biography management
//          */
//         Route::get('/{biography}/edit', [BiographyController::class, 'edit'])
//             ->name('biographies.edit')
//             ->where('biography', '[0-9]+');

//         Route::put('/{biography}', [BiographyController::class, 'update'])
//             ->name('biographies.update')
//             ->where('biography', '[0-9]+');

//         Route::delete('/{biography}', [BiographyController::class, 'destroy'])
//             ->name('biographies.destroy')
//             ->where('biography', '[0-9]+');

//         /**
//          * Biography settings and privacy
//          */
//         Route::get('/{biography}/settings', [BiographyController::class, 'settings'])
//             ->name('biographies.settings')
//             ->where('biography', '[0-9]+');

//         Route::put('/{biography}/privacy', [BiographyController::class, 'updatePrivacy'])
//             ->name('biographies.privacy.update')
//             ->where('biography', '[0-9]+');

//         // ===================================================================
//         // 📝 CHAPTER MANAGEMENT (Nested Resource)
//         // ===================================================================

//         Route::group(['prefix' => '{biography}/chapters'], function () {

//             /**
//              * Chapter listing and management
//              */
//             Route::get('/', [BiographyChapterController::class, 'index'])
//                 ->name('biographies.chapters.index')
//                 ->where('biography', '[0-9]+');

//             /**
//              * Chapter creation
//              */
//             Route::get('/create', [BiographyChapterController::class, 'create'])
//                 ->name('biographies.chapters.create')
//                 ->where('biography', '[0-9]+');

//             Route::post('/', [BiographyChapterController::class, 'store'])
//                 ->name('biographies.chapters.store')
//                 ->where('biography', '[0-9]+');

//             /**
//              * Individual chapter management
//              */
//             Route::get('/{chapter}/edit', [BiographyChapterController::class, 'edit'])
//                 ->name('biographies.chapters.edit')
//                 ->where(['biography' => '[0-9]+', 'chapter' => '[0-9]+']);

//             Route::put('/{chapter}', [BiographyChapterController::class, 'update'])
//                 ->name('biographies.chapters.update')
//                 ->where(['biography' => '[0-9]+', 'chapter' => '[0-9]+']);

//             Route::delete('/{chapter}', [BiographyChapterController::class, 'destroy'])
//                 ->name('biographies.chapters.destroy')
//                 ->where(['biography' => '[0-9]+', 'chapter' => '[0-9]+']);

//             /**
//              * Chapter reordering
//              */
//             Route::put('/reorder', [BiographyChapterController::class, 'reorder'])
//                 ->name('biographies.chapters.reorder')
//                 ->where('biography', '[0-9]+');

//             /**
//              * Chapter media management
//              */
//             Route::post('/{chapter}/media', [BiographyChapterController::class, 'uploadMedia'])
//                 ->name('biographies.chapters.media.upload')
//                 ->where(['biography' => '[0-9]+', 'chapter' => '[0-9]+']);

//             Route::delete('/{chapter}/media/{media}', [BiographyChapterController::class, 'deleteMedia'])
//                 ->name('biographies.chapters.media.delete')
//                 ->where(['biography' => '[0-9]+', 'chapter' => '[0-9]+', 'media' => '[0-9]+']);
//         });

//         // ===================================================================
//         // 🖼️ BIOGRAPHY MEDIA MANAGEMENT
//         // ===================================================================

//         Route::group(['prefix' => '{biography}/media'], function () {

//             Route::post('/featured', [BiographyController::class, 'uploadFeaturedImage'])
//                 ->name('biographies.media.featured.upload')
//                 ->where('biography', '[0-9]+');

//             Route::delete('/featured', [BiographyController::class, 'deleteFeaturedImage'])
//                 ->name('biographies.media.featured.delete')
//                 ->where('biography', '[0-9]+');

//             Route::post('/gallery', [BiographyController::class, 'uploadGalleryImage'])
//                 ->name('biographies.media.gallery.upload')
//                 ->where('biography', '[0-9]+');

//             Route::delete('/gallery/{media}', [BiographyController::class, 'deleteGalleryImage'])
//                 ->name('biographies.media.gallery.delete')
//                 ->where(['biography' => '[0-9]+', 'media' => '[0-9]+']);
//         });
//     });
// });

// ===================================================================
// 🎨 BIOGRAPHY SHOWCASE & DISCOVERY (Marketing Routes)
// ===================================================================

Route::group(['prefix' => 'rinascimento-digitale'], function () {

    /**
     * Featured biographies showcase
     */
    Route::get('/storie-ispiratrici', [BiographyController::class, 'showcase'])
        ->name('biographies.showcase');

    /**
     * Biography creation onboarding for new users
     */
    Route::get('/crea-la-tua-storia', [BiographyController::class, 'onboarding'])
        ->name('biographies.onboarding');
});

// ===================================================================
// 🔧 AJAX & API SUPPORT ROUTES (for frontend functionality)
// ===================================================================

Route::middleware(['auth'])->group(function () {

    /**
     * Quick actions for authenticated users
     */
    Route::post('/biography/quick-create', [BiographyController::class, 'quickCreate'])
        ->name('biography.quick.create');

    Route::get('/biography/{biography}/preview', [BiographyController::class, 'preview'])
        ->name('biography.preview')
        ->where('biography', '[0-9]+');

    /**
     * Biography templates and suggestions
     */
    Route::get('/biography/templates', [BiographyController::class, 'templates'])
        ->name('biography.templates');
});
