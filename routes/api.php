<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Currency routes
Route::get('/algo-exchange-rate', [App\Http\Controllers\Api\CurrencyController::class, 'getAlgoExchangeRate']);
