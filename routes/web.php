<?php

use App\Actions\Jetstream\UpdateTeamName;
use App\Enums\NotificationStatus;
use App\Http\Controllers\Formazione;
use App\Http\Controllers\Notifications\Invitations\NotificationInvitationResponseController;
use App\Http\Controllers\Notifications\NotificationDetailsController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletResponseController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletRequestController;
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
use App\Http\Controllers\IconAdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DropController;
use App\Http\Middleware\SetLanguage;
use App\Livewire\Collections\CollectionOpen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

use Livewire\Livewire;
use Ultra\EgiModule\Http\Controllers\EgiUploadController;
use Ultra\EgiModule\Http\Controllers\EgiUploadPageController;


// Route::view('/home', 'home')
//      ->name('home');

Route::get('/home', [DropController::class, 'index'])->name('home');

// Rotta per PhotoUploader
Route::get('/photo-uploader', PhotoUploader::class)->name('photo-uploader');

// Rotta per la home
// Route::get('/', function () {
//     return view('welcome');
// });

// Rotta per phpinfo
Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/debug/livewire/{component}', function ($component) {
    return Livewire::test($component)->render();
});

//  // Rotte per Drop
//  Route::post('/drops/{id}/join', [DropController::class, 'join'])
//  ->name('drops.join')
//  ->middleware(['can:join_drop']);

// Rotta di debug per visualizzare la sessione
Route::get('/session', function () {
 dd((session()->all()));
});


// Rotte protette da middleware
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

        Route::get('/api/check-upload-authorization', [Ultra\UploadManager\Controllers\Config\ConfigController::class, 'checkUploadAuthorization'])
            ->name('upload.authorization');

        Route::get('/upload/egi', [EgiUploadPageController::class, 'showUploadPage'])
            // ->middleware(['auth', 'collection_can:manage_egi']) // Auth qui è ridondante se già nel gruppo app? Meglio solo permessi specifici.
            ->name('egi.upload.page');

        Route::post('/upload/egi', [EgiUploadController::class, 'handleUpload'])
            // ->middleware(['auth', 'collection_can:manage_egi']) // Auth qui è ridondante se già nel gruppo app? Meglio solo permessi specifici.
            ->name('egi.upload.store');


        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Dashboard statica temporanea per test
        Route::get('/dashboard-static', [App\Http\Controllers\DashboardStaticController::class, 'index'])->name('dashboard.static');

        Route::get('/debug-context', function () {
            return Route::currentRouteName();
        })->name('debug.context');

        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('roles', RoleController::class)
                ->middleware(['role_or_permission:manage_roles']);

            Route::resource('icons', IconAdminController::class)
                ->middleware(['role_or_permission: manage_icons']);

            Route::get('/assign-role', [RoleController::class, 'showAssignRoleForm'])
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

        Route::prefix('collections')->group(function () {

            // Rotte per visualizzare il carousel delle collezioni, viene usata solamente se il team corrente ha più di una collezione associata
            Route::get('/carousel', CollectionCarousel::class)
                ->middleware('collection_can:read_collection')
                ->name('collections.carousel');

            // Rotta per aprire vista della collezione
            Route::get('/{id}/edit', CollectionEdit::class)
                ->middleware('collection_can:view_collection_header')
                ->name('collections.edit');

            // Rotta per discernere se mostrare il carousel o la vista della collezione
            Route::get('/open', CollectionOpen::class)
                ->middleware('collection_can:view_collection_header')
                ->name('collections.open');

            Route::get('/{id}/head-images', HeadImagesManager::class)
                ->middleware('collection_can:view_collection_header')
                ->name('collections.head_images');

            Route::get('/create', CreateCollection::class)
                ->middleware('collection_can:create_collection')
                ->name('collections.create');

            Route::get('/{id}/members', CollectionUserMember::class)
                ->middleware('collection_can:view_collection_header')
                ->name('collections.collection_user');

            // Rotta per la fetch per eliminare una proposal invitation
            Route::delete('/{id}/invitations/{invitationId}', [CollectionUserMember::class, 'deleteProposalInvitation'])
                ->name('invitations.delete')
                ->middleware(['collection_can:add_team_member']);

            // Rotte per fetch, per eliminazione della proposal wallet
            Route::delete('/{id}/wallets/{walletId}', [CollectionUserMember::class, 'deleteProposalWallet'])
                ->name('wallets.delete')
                ->middleware(['collection_can:delete_wallet']);

            // Route per fetch per creare un nuovo wallet
            Route::post('/{id}/wallets/create', [NotificationWalletRequestController::class, 'requestCreateWallet'])
                ->name('wallets.create')
                ->middleware(['collection_can:create_wallet', 'check.pending.wallet']);

            Route::post('/{id}/wallets/update', [NotificationWalletRequestController::class, 'requestUpdateWallet'])
                ->name('wallets.update')
                ->middleware(['collection_can:update_wallet', 'check.pending.wallet']);

            Route::post('/{id}/wallets/donation', [NotificationWalletRequestController::class, 'requestDonation'])
                ->name('wallets.update')
                ->middleware(['collection_can:update_wallet', 'check.pending.wallet']);

        });

    // Rotte per la gestione delle notifiche
    Route::prefix('notifications')->group(function () {

        Route::get('{id}/details', [NotificationDetailsController::class, 'show'])
            ->name('notifications.details');

        Route::get('/request', [NotificationWalletResponseController::class, 'fetchHeadThumbnailList'])->name('head.thumbnails.list');


        Route::prefix('wallet')->group(function () {

            Route::post('/response', [NotificationWalletResponseController::class, 'response'])->name('notifications.wallets.response');

            Route::post('/archive', [NotificationWalletResponseController::class, 'notificationArchive'])->name('notifications.wallets.notificationArchive');

        });

        Route::prefix('invitation')->group(function () {

            // Rotte per le Invitation
            Route::post('/response', [NotificationInvitationResponseController::class, 'response'])->name('notifications.invitations.response');

            Route::post('/archive', [NotificationInvitationResponseController::class, 'notificationArchive'])->name('notifications.invitations.notificationArchive');

        });

    });



});

Route::get('/translations.js', function () {
    $translations = [
        'notification' =>[
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

    return response("window.translations = " . json_encode($translations, JSON_PRETTY_PRINT) . ";")
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'no-cache, must-revalidate');
});

// Rotte per la gestione delle costanti enum
Route::get('/js/enums', function (Request $request) {

    Log::channel('florenceegi')->info('Richiesta costanti enum', [
        // 'user' => Auth::user()->id,
        'notificationStatus' => collect(NotificationStatus::cases())->mapWithKeys(fn($enum) => [$enum->name => $enum->value])
    ]);

    return response()->json([
        'NotificationStatus' => collect(NotificationStatus::cases())->mapWithKeys(fn($enum) => [$enum->name => $enum->value])
    ]);
});

// Rotta per le citazioni
Route::get('/api/quote', function () {
    $response = Http::get('https://zenquotes.io/api/random');
    return response($response->body())
              ->header('Content-Type', 'application/json')
              ->header('Access-Control-Allow-Origin', '*');
});




