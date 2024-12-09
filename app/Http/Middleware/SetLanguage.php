<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1) Verifica se l'utente è autenticato e ha un valore 'language' nel DB

        // Log::channel('florenceegi')->info('USER: '.Auth::user());
        // Log::channel('florenceegi')->info('LINGUA CORRENTE DA DB: '.Auth::user()->language);

        if (Auth::user() && Auth::user()->language) {
            $modalita = 'DB';
            $lang = trim(Auth::user()->language);
            App::setLocale($lang);

            // 2) Se l'utente non è autenticato o non ha una lingua impostata, verifica il cookie 'language'
        } elseif (Cookie::has('language')) {
            $modalita = 'COOKIE';
            $lang = trim(Cookie::get('language'));
            App::setLocale($lang);
            // 3) Se nessuna delle opzioni precedenti è disponibile, usa la lingua predefinita dal file di configurazione
        } else {
            $modalita = 'CONFIG';
            $lang = trim(config('app.locale'));
            App::setLocale($lang);
        }

        session(['language__'.$modalita => App::getLocale()]);
        // Log::channel('florenceegi')->info('LINGUA CORRENTE: '.$modalita.' '.$lang);

        return $next($request);
    }
}
