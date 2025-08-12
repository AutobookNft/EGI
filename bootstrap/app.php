<?php

use App\Http\Middleware\CheckCollectionPermission;
use App\Http\Middleware\CheckPendingWallet;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Dotenv\Dotenv;

// 🔐 Load .env early to avoid "No application encryption key" errors
Dotenv::createImmutable(dirname(__DIR__))->load();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/menu_dashboard.php',
            __DIR__.'/../routes/gdpr.php',
            __DIR__.'/../routes/auth.php',
            __DIR__.'/../routes/user-domains.php',
            __DIR__.'/../routes/gdpr_legal.php',
            __DIR__.'/../routes/creator.php',
            __DIR__.'/../routes/biography.php',
            __DIR__.'/../routes/archetips.php'
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'collection_can'       => CheckCollectionPermission::class,
            'role_or_permission'   => RoleOrPermissionMiddleware::class,
            'check.pending.wallet' => CheckPendingWallet::class,
        ]);

        $middleware->web(replace: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class => EncryptCookies::class,
        ]);

        $middleware->appendToGroup('web', [SetLanguage::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // definisci eventuali eccezioni qui
    })
    ->create();