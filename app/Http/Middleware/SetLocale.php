<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Prendi la lingua dall'utente autenticato o dalla sessione o usa quella di default
        $locale = Auth::check() && Auth::user()->language
            ? Auth::user()->language
            : session('locale', config('app.locale'));

        // Verifica che la lingua sia tra quelle supportate
        if (in_array($locale, config('app.languages'))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
} 
