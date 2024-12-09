<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // ... altri middleware
        \App\Http\Middleware\SetLanguage::class,
        // \App\Http\Middleware\AddViewDebugInfo::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // ... altri middleware
            // \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetLanguage::class,
            // \App\Http\Middleware\DisableCache::class,
            // \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class, // Assicurati che questo sia presente
        ],
    ];

}
