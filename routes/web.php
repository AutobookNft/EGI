<?php

use Illuminate\Support\Facades\Route;


use App\Livewire\PhotoUploader;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\IconAdminController;
use App\Http\Middleware\SetLanguage;
use App\Livewire\CollectionManager;
use Illuminate\Support\Facades\Log;

use UltraProject\UConfig\Http\Controllers\UConfigController;

Route::get('/photo-uploader', PhotoUploader::class)->name('photo-uploader');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', SetLanguage::class])
->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class)
            ->middleware(['can:manage_roles']);

        Route::resource('icons', IconAdminController::class)
            ->middleware(['can:manage_icons']);

        // Route::get('/uconfig', [UConfigController::class, 'index'])->name('open_uconfig');

        Route::get('/assign-role', [RoleController::class, 'showAssignRoleForm'])->name('assign.role.form');

        Route::post('/assign-role', [RoleController::class, 'assignRole'])->name('assign.role');

        Route::get('/assign-permissions', [RoleController::class, 'showAssignPermissionsForm'])->name('assign.permissions.form');

        Route::post('/assign-permissions', [RoleController::class, 'assignPermissions'])->name('assign.permissions');

    });

    Route::get('/collections', CollectionManager::class)->name('collections.index'); //->middleware('web');

    Route::get('/session', function () {
        dd((session()->all()));
    });

});
