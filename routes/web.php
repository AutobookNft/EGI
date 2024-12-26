<?php

use App\Livewire\Collections\CollectionCarousel;
use App\Livewire\Collections\CollectionEdit;
use App\Livewire\Collections\CollectionUserTeam;
use App\Livewire\Collections\CollectionWallet;
use App\Livewire\Collections\CreateCollection;
use App\Livewire\Collections\HeadImagesManager;
use App\Livewire\TeamManager;
use Illuminate\Support\Facades\Route;
use App\Livewire\PhotoUploader;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\IconAdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DropController;
use App\Http\Middleware\SetLanguage;
use App\Livewire\Collections\CollectionOpen;
use Illuminate\Support\Facades\Log;
use UltraProject\UConfig\Http\Controllers\UConfigController;


// Rotta per PhotoUploader
Route::get('/photo-uploader', PhotoUploader::class)->name('photo-uploader');

// Rotta per la home
Route::get('/', function () {
    return view('welcome');
});

// Rotte protette da middleware
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

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
                ->middleware('team_can:read_collection')
                ->name('collections.carousel');

            // Rotta per aprire vista della collezione
            Route::get('/{id}/edit', CollectionEdit::class)
                ->middleware('team_can:update_collection')
                ->name('collections.edit');

            // Rotta per discernere se mostrare il carousel o la vista della collezione
            Route::get('/open', CollectionOpen::class)
                ->middleware('team_can:view_collection_header')
                ->name('collections.open');

            Route::get('/{id}/head-images', HeadImagesManager::class)
                ->middleware('team_can:view_collection_header')
                ->name('collections.head_images');

            Route::get('/create', CreateCollection::class)
                ->middleware('team_can:create_collection')
                ->name('collections.create');

            Route::get('/{id}/{teamId}/user-team', CollectionUserTeam::class)
                ->middleware('team_can:view_collection_header')
                ->name('collections.user_team');

        });




        // Route::get('/teams', TeamManager::class)
        //     ->middleware(['can:read_collection'])
        //     ->name('teams');

        // Rotte per Wallet
        Route::post('/wallets/{id}/approve', [WalletController::class, 'approve'])
            ->name('wallets.approve')
            ->middleware(['can:approve_wallet']);

        Route::post('/wallets/{id}/reject', [WalletController::class, 'reject'])
            ->name('wallets.reject')
            ->middleware(['can:reject_wallet']);

        // Rotte per Drop
        Route::post('/drops/{id}/join', [DropController::class, 'join'])
            ->name('drops.join')
            ->middleware(['can:join_drop']);

        // Rotta di debug per visualizzare la sessione
        Route::get('/session', function () {
            dd((session()->all()));
        });
    });
