<?php

use Illuminate\Http\Request;

/**
 * Polyfill for missing JSON constants in a broken PHP environment.
 * This ensures the application can run even if the PHP build
 * fails to define standard constants.
 */
if (!defined('JSON_SORT_KEYS')) {
    define('JSON_SORT_KEYS', 8);
}


define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());