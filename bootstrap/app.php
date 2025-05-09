<?php

use App\Http\Middleware\CheckCollectionPermission;
use App\Http\Middleware\CheckPendingWallet;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;


// 🔥 Laravel/Symfony bugfix: APP_URL viene letto troppo presto (Request::create())
// Se contiene \x3a (":" encoded male) o backslash, Symfony crasha.
// Questo fix pulisce la variabile ENV prima che Laravel la legga.$url = $_ENV['APP_URL'] ?? ($_SERVER['APP_URL'] ?? 'http://localhost');

$url = str_replace(['\\x3a', '\x3a'], ':', $url); // fix encoding malato
$url = str_replace('\\', '/', $url);              // fix backslash grezzi

$_ENV['APP_URL'] = $url;
$_SERVER['APP_URL'] = $url;
putenv('APP_URL=' . $url);

$requestUri = $_ENV['APP_URL'] ?? 'undefined';
try {
    \Illuminate\Http\Request::create($requestUri);
} catch (\Throwable $e) {
    file_put_contents(
        __DIR__.'/../who-breaks-me.log',
        "Tried to create request from: $requestUri\nException: " . $e->getMessage() . "\n"
    );
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'collection_can' => CheckCollectionPermission::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'check.pending.wallet' => CheckPendingWallet::class,
        ]);

        $middleware->web(replace: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class => EncryptCookies::class,
        ]);


        // Middleware groups per le rotte web
        $middleware->appendToGroup('web',[SetLanguage::class]);


    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
