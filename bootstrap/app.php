<?php

use App\Http\Middleware\CheckTeamPermission;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'team_can' => CheckTeamPermission::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Middleware groups per le rotte web
        $middleware->appendToGroup('web',[SetLanguage::class]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
