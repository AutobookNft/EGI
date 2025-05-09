<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as JetstreamAuthController;
use Laravel\Fortify\Contracts\LoginResponse;

/**
 * Estensione del controller di autenticazione Jetstream.
 * Aggiunge funzionalità di connessione automatica del wallet durante il login.
 *
 * @gdpr-purpose Connette automaticamente il wallet dell'utente al login, se disponibile
 */
class AuthenticatedSessionController extends JetstreamAuthController
{
    /**
     * Sovrascrive il metodo di login standard per aggiungere la connessione wallet
     */
    public function store(Request $request)
    {
        // Esegui il login standard di Jetstream
        $response = parent::store($request);

        Log::channel('upload')->info('Login attempt.', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'session_id' => $request->session()->getId()
        ]);

        // Se il login è riuscito, gestisci la connessione wallet
        if (auth()->check() && !empty(auth()->user()->wallet)) {
            // Salva l'indirizzo wallet in sessione
            session(['connected_wallet' => auth()->user()->wallet]);

            // Se la richiesta vuole una risposta JSON, restituisci info aggiuntive
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'wallet_connected' => true,
                    'connected_wallet' => auth()->user()->wallet
                ]);
            }
        }

        // Ritorna la risposta standard per le richieste non-AJAX
        return $response;
    }
}
