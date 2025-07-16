<?php

use App\Actions\Jetstream\UpdateTeamName;
use App\Enums\NotificationStatus;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\EgiController;
use App\Http\Controllers\EPPController;
use App\Http\Controllers\Formazione;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Notifications\Invitations\NotificationInvitationResponseController;
use App\Http\Controllers\Notifications\NotificationDetailsController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletResponseController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletRequestController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\User\UserCollectionController;
use App\Http\Controllers\WalletConnectController;
use App\Livewire\Collections\CollectionCarousel;
use App\Livewire\Collections\CollectionEdit;
use App\Livewire\Collections\CollectionUserMember;
use App\Livewire\Collections\CreateCollection;
use App\Livewire\Collections\HeadImagesManager;
use App\Livewire\Notifications\Wallets\EditWalletModal;
use Illuminate\Support\Facades\Route;
use App\Livewire\PhotoUploader;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EgiReservationCertificateController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\IconAdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Notifications\Gdpr\GdprNotificationResponseController;
use App\Http\Middleware\SetLanguage;
use App\Livewire\Collections\CollectionOpen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

use Livewire\Livewire;
use Ultra\EgiModule\Http\Controllers\EgiUploadController;
use Ultra\EgiModule\Http\Controllers\EgiUploadPageController;
use Ultra\UploadManager\Controllers\Config\ConfigController;
use App\Http\Controllers\Web\BiographyWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 📜 Oracode Routes: FlorenceEGI Application Routes
| Organized by functionality and access level for clarity
|
*/



Route::get('/test-loadstats', function () {
    Log::channel('florenceegi')->info('Direct auth test', [
        'auth_check' => Auth::check(),
        'auth_id' => Auth::id(),
        'auth_user' => Auth::user()?->name,
        'session_auth_status' => session('auth_status'),
        'session_user_id' => session('connected_user_id'),
    ]);

    return 'Check the logs!';
});

Route::get('/test-create-fegi', function () {
    $request = request();
    $request->merge(['create_new' => true]);

    $controller = new \App\Http\Controllers\WalletConnectController(
        app(\Ultra\UltraLogManager\UltraLogManager::class),
        app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class),
        app(\App\Services\CollectionService::class)
    );

    return $controller->connect($request);
});

// Aggiungi questa route in web.php per debug
Route::get('/debug-user-lookup', function () {
    $userId = session('connected_user_id');

    if (!$userId) {
        return ['error' => 'No user ID in session'];
    }

    $user = \App\Models\User::find($userId);

    return [
        'session_user_id' => $userId,
        'user_found' => $user ? true : false,
        'user_data' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_weak_auth' => $user->is_weak_auth,
            'wallet' => $user->wallet,
            'created_at' => $user->created_at
        ] : null,
        'users_count' => \App\Models\User::count(),
        'weak_auth_users' => \App\Models\User::where('is_weak_auth', true)->get(['id', 'name', 'email'])
    ];
});

Route::get('/debug-session-direct', function () {
    return [
        'session_direct' => [
            'auth_status' => session('auth_status'),
            'user_id' => session('connected_user_id'),
        ],
        'fegi_guard_debug' => [
            'user_resolved' => Auth::guard('fegi')->user(),
            'check' => Auth::guard('fegi')->check(),
        ]
    ];
});


/*
|--------------------------------------------------------------------------
| Public Routes - Homepage & Redirects
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/archetypes/patron', function () {
    return view('archetypes.patron');
})->name('archetypes.patron');

/*
|--------------------------------------------------------------------------
| Public Routes - Collections & EGIs
|--------------------------------------------------------------------------
*/

Route::middleware('collection_can:view_collection_header')->group(function () {
    Route::get('collections/{id}/edit', CollectionEdit::class)
        ->name('collections.edit');

    Route::get('collections/open', CollectionOpen::class)
        ->name('collections.open');

    Route::get('/{id}/head-images', HeadImagesManager::class)
        ->name('collections.head_images');
});


Route::prefix('home')->name('home.')->group(function () {
    // Public collection viewing (accessible to all)
    Route::get('/collections', [CollectionsController::class, 'index'])->name('collections.index');
    Route::get('/collections/{id}', [CollectionsController::class, 'show'])->name('collections.show');

    // Collection management (restricted to creators)
    // Route::middleware(['can:manage-collections'])->group(function () {
    //     Route::get('/collections/create', [CollectionsController::class, 'create'])->name('collections.create');
    //     Route::post('/collections', [CollectionsController::class, 'store'])->name('collections.store');
    //     Route::get('/collections/{id}/edit', [CollectionsController::class, 'edit'])->name('collections.edit');
    //     Route::put('/collections/{collection}', [CollectionsController::class, 'update'])->name('collections.update');
    //     Route::delete('/collections/{collection}', [CollectionsController::class, 'destroy'])->name('collections.destroy');
    // });

    // // Collection interaction
    // Route::post('/collections/{collection}/report', [CollectionsController::class, 'report'])->name('collections.report');
});

// EGI routes

Route::group(['prefix' => 'egis'], function () {

    // Mostra singolo EGI (già esistente, confermo per completezza)
    Route::get('/{egi}', [App\Http\Controllers\EgiController::class, 'show'])
        ->name('egis.show');

    // Update EGI - PATCH per aggiornamento parziale
    Route::put('/{egi}', [App\Http\Controllers\EgiController::class, 'update'])
        ->name('egis.update');

    // Delete EGI - DELETE per eliminazione
    Route::delete('/{egi}', [App\Http\Controllers\EgiController::class, 'destroy'])
        ->name('egis.destroy');
});

// EPP routes
Route::get('/epps', [EppController::class, 'index'])->name('epps.index');
Route::get('/epps/{epp}', [EppController::class, 'show'])->name('epps.show');
Route::get('/epps/dashboard', [EppController::class, 'dashboard'])->name('epps.dashboard');

/*
|--------------------------------------------------------------------------
| Wallet & Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/wallet/connect', [WalletConnectController::class, 'connect'])->name('wallet.connect');
Route::post('/api/wallet/disconnect', [WalletConnectController::class, 'disconnect'])->name('wallet.disconnect');
Route::get('/api/wallet/status', [WalletConnectController::class, 'status'])->name('wallet.status');

/*
|--------------------------------------------------------------------------
| Upload Routes
|--------------------------------------------------------------------------
*/
Route::post('/upload/egi', [EgiUploadController::class, 'handleUpload'])
    ->name('egi.upload.store');

// Photo uploader component
Route::get('/photo-uploader', PhotoUploader::class)->name('photo-uploader');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| User Collections API
|--------------------------------------------------------------------------
*/
Route::prefix('api/user')->name('api.user.')->group(function () {
    Route::get('/accessible-collections', [UserCollectionController::class, 'getAccessibleCollections'])
        ->name('accessible.collections');

    Route::post('/set-current-collection/{collection}', [UserCollectionController::class, 'setCurrentCollection'])
        ->name('setCurrentCollection');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {


        // Override Jetstream profile route with our GDPR-compliant version
        Route::get('/user/profile', [GdprController::class, 'showProfile'])
            ->name('profile.show');

        // Alternative route for direct access
        Route::get('/profile', [GdprController::class, 'showProfile'])
            ->name('gdpr.profile');

        // Upload authorization check
        Route::get('/api/check-upload-authorization', [Ultra\UploadManager\Controllers\Config\ConfigController::class, 'checkUploadAuthorization'])
            ->name('upload.authorization');

        // Dashboard statica temporanea per test
        Route::get('/dashboard-static', [App\Http\Controllers\DashboardStaticController::class, 'index'])->name('dashboard.static');

        Route::get('/debug-context', function () {
            return Route::currentRouteName();
        })->name('debug.context');
        // EGI upload routes
        Route::middleware('collection_can:manage_egi')->group(function () {
            // Upload routes are defined here when needed
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('roles', RoleController::class)
                ->middleware(['role_or_permission:manage_roles']);

            Route::resource('icons', IconAdminController::class)
                ->middleware(['role_or_permission: manage_icons']);

            Route::get('/assign-role/form', [RoleController::class, 'showAssignRoleForm'])
                ->name('assign.role.form')
                ->middleware(['role_or_permission:manage_roles']);

            Route::post('/assign-role', [RoleController::class, 'assignRole'])
                ->name('assign.role')
                ->middleware(['role_or_permission:manage_roles']);

            Route::get('/assign-permissions', [RoleController::class, 'showAssignPermissionsForm'])
                ->name('assign.permissions.form')
                ->middleware(['role_or_permission:manage_roles']);

            Route::post('/assign-permissions', [RoleController::class, 'assignPermissions'])
                ->name('assign.permissions')
                ->middleware(['role_or_permission:manage_roles']);
        });

        /*
        |--------------------------------------------------------------------------
        | Collections Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('collections')->group(function () {
            // Read collection permission
            Route::middleware('collection_can:read_collection')->group(function () {
                Route::get('/carousel', CollectionCarousel::class)
                    ->name('collections.carousel');
            });

            // View collection header permission
            Route::middleware('collection_can:view_collection_header')->group(function () {

                Route::get('/{id}/members', CollectionUserMember::class)
                    ->name('collections.collection_user');
            });

            // Create collection permission
            Route::middleware('collection_can:create_collection')->group(function () {
                Route::post('/create', [CollectionsController::class, 'create'])
                    ->name('collections.create');
            });

            // Add team member permission
            Route::middleware('collection_can:add_team_member')->group(function () {
                Route::delete('/{id}/invitations/{invitationId}', [CollectionUserMember::class, 'deleteProposalInvitation'])
                    ->name('invitations.delete');
            });

            // Delete wallet permission
            Route::middleware('collection_can:delete_wallet')->group(function () {
                Route::delete('/{id}/wallets/{walletId}', [CollectionUserMember::class, 'deleteProposalWallet'])
                    ->name('wallets.delete');
            });

            // Create wallet permission
            Route::middleware('collection_can:create_wallet')->group(function () {
                Route::post('/{id}/wallets/create', [NotificationWalletRequestController::class, 'requestCreateWallet'])
                    ->name('wallets.create')
                    ->middleware('check.pending.wallet');
            });

            // Update wallet permission
            Route::middleware('collection_can:update_wallet')->group(function () {
                Route::post('/{id}/wallets/update', [NotificationWalletRequestController::class, 'requestUpdateWallet'])
                    ->name('wallets.update')
                    ->middleware('check.pending.wallet');

                Route::post('/{id}/wallets/donation', [NotificationWalletRequestController::class, 'requestDonation'])
                    ->name('wallets.donation')
                    ->middleware('check.pending.wallet');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Notifications Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('notifications')->group(function () {
            Route::get('{id}/details', [NotificationDetailsController::class, 'show'])
                ->name('notifications.details');

            Route::get('/request', [NotificationWalletResponseController::class, 'fetchHeadThumbnailList'])
                ->name('head.thumbnails.list');

            // Wallet notifications
            Route::prefix('wallet')->group(function () {
                Route::post('/response', [NotificationWalletResponseController::class, 'response'])
                    ->name('notifications.wallets.response');

                Route::post('/archive', [NotificationWalletResponseController::class, 'notificationArchive'])
                    ->name('notifications.wallets.notificationArchive');
            });

            // Invitation notifications
            Route::prefix('invitation')->group(function () {
                Route::post('/response', [NotificationInvitationResponseController::class, 'response'])
                    ->name('notifications.invitations.response');

                Route::post('/archive', [NotificationInvitationResponseController::class, 'notificationArchive'])
                    ->name('notifications.invitations.notificationArchive');
            });
        });
    });


// ===================================================================
// 📖 BIOGRAPHY DISPLAY ROUTES
// ===================================================================
/**
 * Biography listing page
 * Public access with authentication awareness
 */
Route::get('/biographies', [BiographyWebController::class, 'index'])
    ->name('biography.index');

/**
 * Biography detail page with slug-based routing
 * Access control handled in controller (public vs private biographies)
 */
Route::get('/biographies/{biography:slug}', [BiographyWebController::class, 'show'])
    ->name('biography.public.show')
    ->where('biography', '[a-z0-9\-]+'); // SEO-friendly slug

/*
|--------------------------------------------------------------------------
| Biography Routes (FlorenceEGI Brand Compliant)
|--------------------------------------------------------------------------
|
| User biography management and public viewing
| Authentication: Required for management, public for viewing
| Version: 1.0.0 (FlorenceEGI - Biography Integration)
|
*/

Route::middleware([])->group(function () {
    // Biography management (user's own biographies)
    Route::get('/biography/manage', [App\Http\Controllers\Web\BiographyController::class, 'manage'])
        ->name('biography.manage');
    Route::get('/biography/create', [BiographyController::class, 'create'])
        ->name('biography.create');
    Route::post('/biography/create', [App\Http\Controllers\Web\BiographyController::class, 'store'])
        ->name('biography.store');
    Route::get('/biography/{biography}/edit', [App\Http\Controllers\Web\BiographyController::class, 'edit'])
        ->name('biography.edit');
    Route::put('/biography/{biography}', [App\Http\Controllers\Web\BiographyController::class, 'update'])
        ->name('biography.update');

    Route::get('/biography/view', [App\Http\Controllers\Web\BiographyController::class, 'viewOwn'])
        ->name('biography.view');
});

// Public biography viewing (no auth required)
Route::get('/biography/{user}', [App\Http\Controllers\Web\BiographyController::class, 'show'])
    ->name('biography.user.show');

// Biography media upload route
Route::post('/biography/upload-media', [App\Http\Controllers\Web\BiographyController::class, 'uploadMedia'])
    ->name('biography.upload-media')
    ->middleware('auth');

// Biography remove media route
Route::delete('/biography/remove-media', [App\Http\Controllers\Web\BiographyController::class, 'removeMedia'])
    ->name('biography.remove-media')
    ->middleware('auth');

// Biography set avatar route
Route::post('/biography/set-avatar', [App\Http\Controllers\Web\BiographyController::class, 'setAvatar'])
    ->name('biography.set-avatar')
    ->middleware('auth');

// Biography delete route (web version for session auth)
Route::delete('/biography/{biography}', [App\Http\Controllers\Web\BiographyController::class, 'destroy'])
    ->name('biography.destroy')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Biography Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/biography.php';

/*
|--------------------------------------------------------------------------
| Natan Assistant Routes
|--------------------------------------------------------------------------
*/

Route::get('/why-cant-buy-egis', function () {
    return view('info.why-cant-buy-egis');
})->name('info.why-cant-buy-egis');


/*
|--------------------------------------------------------------------------
| Reservation, configuration and like routes
|--------------------------------------------------------------------------
*/

// Certificate routes
Route::prefix('egi-certificates')->name('egi-certificates.')->group(function () {
    Route::get('/{uuid}', [EgiReservationCertificateController::class, 'show'])
        ->name('show');
    Route::get('/{uuid}/download', [EgiReservationCertificateController::class, 'download'])
        ->name('download');
    Route::get('/{uuid}/verify', [EgiReservationCertificateController::class, 'verify'])
        ->name('verify');
    Route::get('/egi/{egiId}', [EgiReservationCertificateController::class, 'listByEgi'])
        ->name('list-by-egi');
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // User certificates
    Route::get('/my-certificates', [EgiReservationCertificateController::class, 'listByUser'])
        ->name('my-certificates');
});


Route::prefix('notifications/{notification}/gdpr')
    ->name('notifications.gdpr.')
    ->group(function () {

        // // Rotta per la conferma semplice (rate limit standard)
        Route::patch('/confirm', [GdprNotificationResponseController::class, 'confirm'])->name('confirm');

        // Rotta per la revoca semplice (rate limit standard)
        Route::patch('/revoke', [GdprNotificationResponseController::class, 'revoke'])->name('revoke');

        // Fortino Digitale #2: Rate Limiting restrittivo per l'azione di sicurezza
        // Permette massimo 3 chiamate ogni ora per prevenire abusi del protocollo di allerta.
        Route::patch('/disavow', [GdprNotificationResponseController::class, 'disavow'])
            ->name('disavow')
            ->middleware('throttle:3,60');
    });

Route::prefix('api')->name('api.')->group(function () {
    // Reservation API endpoints
    Route::post('/egis/{egiId}/reserve', [ReservationController::class, 'apiReserve'])
        ->name('egis.reserve');
    Route::delete('/reservations/{id}', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::get('/my-reservations', [ReservationController::class, 'listUserReservations'])
        ->name('my-reservations');
    Route::get('/egis/{egiId}/reservation-status', [ReservationController::class, 'getEgiReservationStatus'])
        ->name('egis.reservation-status');
    // Like/Unlike routes
    Route::post('/collections/{collectionId}/toggle-like', [LikeController::class, 'toggleCollectionLike'])
        ->name('toggle.collection.like');

    Route::post('/egis/{egi}/toggle-like', [LikeController::class, 'toggleEgiLike'])
        ->name('toggle.egi.like'); // Like/Unlike routes
    // API di configurazione
    Route::get('/app-config', [App\Http\Controllers\Api\AppConfigController::class, 'getAppConfig'])
        ->name('app.config');
});

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
*/
// Debug routes
Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/debug/livewire/{component}', function ($component) {
    return Livewire::test($component)->render();
});

Route::get('/session', function () {
    dd((session()->all()));
});

// CSRF refresh
Route::get('/api/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
});

// Translations JSON endpoint
Route::get('/translations.json', function () {
    $translations = [
        'notification' => [
            'no_notifications' => __('notification.no_notifications'),
            'select_notification' => __('notification.select_notification'),
            'notification_list_error' => __('collection.wallet.notification_list_error'),
        ],
        'collection' => [
            'wallet' => [
                'donation' => __('collection.wallet.donation'),
                'donation_success' => __('collection.wallet.donation_success'),
                'accept' => __('label.accept'),
                'decline' => __('label.decline'),
                'archived' => __('label.archived'),
                'save' => __('label.save'),
                'cancel' => __('label.cancel'),
                'address' => __('collection.wallet.address'),
                'royalty_mint' => __('collection.wallet.royalty_mint'),
                'royalty_rebind' => __('collection.wallet.royalty_rebind'),
                'confirmation_title' => __('collection.wallet.confirmation_title'),
                'confirmation_text' => __('collection.wallet.confirmation_text', ['walletId' => ':walletId']),
                'confirm_delete' => __('collection.wallet.confirm_delete'),
                'cancel_delete' => __('collection.wallet.cancel_delete'),
                'deletion_error' => __('collection.wallet.deletion_error'),
                'deletion_error_generic' => __('collection.wallet.deletion_error_generic'),
                'create_the_wallet' => __('collection.wallet.create_the_wallet'),
                'update_the_wallet' => __('collection.wallet.update_the_wallet'),
                'address_placeholder' => __('collection.wallet.address_placeholder'),
                'royalty_mint_placeholder' => __('collection.wallet.royalty_mint_placeholder'),
                'royalty_rebind_placeholder' => __('collection.wallet.royalty_rebind_placeholder'),
                'success_title' => __('collection.wallet.success_title'),
                'creation_success_detail' => __('collection.wallet.creation_success_detail'),
                'validation' => [
                    'address_required' => __('collection.wallet.validation.address_required'),
                    'mint_invalid' => __('collection.wallet.validation.mint_invalid'),
                    'rebind_invalid' => __('collection.wallet.validation.rebind_invalid'),
                ],
                'error' => [
                    'error_title' => __('errors.error'),
                    'creation_error_generic' => __('collection.wallet.creation_error_generic'),
                    'creation_error' => __('collection.wallet.creation_error'),
                    'permission_denied' => __('collection.wallet.permission_denied'),
                ],
                'creation_success' => __('collection.wallet.creation_success'),
            ],
            'invitation' => [
                'confirmation_title' => __('collection.invitation.confirmation_title'),
                'confirmation_text' => __('collection.invitation.confirmation_text', ['invitationId' => ':invitationId']),
                'confirm_delete' => __('collection.invitation.confirm_delete'),
                'cancel_delete' => __('collection.invitation.cancel_delete'),
                'deletion_error' => __('collection.invitation.deletion_error'),
                'deletion_error_generic' => __('collection.invitation.deletion_error_generic'),
                'create_invitation' => __('collection.invitation.create_invitation'),
            ]
        ]
    ];

    return response()->json($translations);
});

// Enums constants endpoint
Route::get('/js/enums', function (Request $request) {
    // Log::channel('florenceegi')->info('Richiesta costanti enum', [
    //     'notificationStatus' => collect(NotificationStatus::cases())->mapWithKeys(fn($enum) => [$enum->name => $enum->value])
    // ]);

    return response()->json([
        'NotificationStatus' => collect(NotificationStatus::cases())->mapWithKeys(fn($enum) => [$enum->name => $enum->value])
    ]);
});

// External API proxy
Route::get('/api/quote', function () {
    $response = Http::get('https://zenquotes.io/api/random');
    return response($response->body())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*');
});

Route::get('/under-construction/{key}', [App\Http\Controllers\UnderConstructionController::class, 'show'])->name('under_construction');

Route::post('/api/assistant/auto-open', [App\Http\Controllers\AssistantController::class, 'setAutoOpen']);
