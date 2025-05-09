<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WalletConnectController extends Controller
{
    /**
     * Handle the wallet connection request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function connect(Request $request): JsonResponse
    {
        Log::channel('florenceegi')->info('Wallet Connect attempt initiated.');

        try {
            // 1. Validare l'input
            $validated = $request->validate([
                // Aggiungere una regex più specifica per Algorand se disponibile
                'wallet_address' => ['required', 'string', 'size:58'], // Dimensione standard Algorand Address
            ]);

            $walletAddress = $validated['wallet_address'];
            Log::channel('florenceegi')->info('Wallet address received for connection:', ['address' => $walletAddress]);

            // 2. Cercare l'utente per indirizzo wallet
            $user = User::where('wallet', $walletAddress)->first();

            if ($user) {
                // 3. Utente Trovato: Stabilire sessione "connected"
                Log::channel('florenceegi')->info('Existing user found for wallet address.', ['user_id' => $user->id]);
                $this->establishConnectedSession($request, $user, $walletAddress);

                return response()->json([
                    'success' => true,
                    'message' => __('Wallet connected successfully.'),
                    'user_status' => $user->email === config('app.wallet_only_user_email') ? 'wallet_only' : 'registered', // Indica se è solo wallet o registrato
                    'user_name' => $user->name // Potrebbe essere utile per UI
                ]);

            } else {
                // 4. Utente Non Trovato: Creare utente "wallet-only"
                Log::channel('florenceegi')->info('No user found for wallet address. Creating wallet-only user.');

                // Dati di default per utente wallet-only
                // **ATTENZIONE ALLA SICUREZZA**: La password generica è un rischio se questo utente
                // potesse in qualche modo fare login diretto. Assicurati che non sia possibile.
                // Potresti usare una password molto lunga e randomica o null se il driver auth lo permette.
                $defaultEmail = config('app.wallet_only_user_email', 'wallet_user@placeholder.egi'); // Meglio se configurabile
                $defaultPassword = Hash::make(Str::random(60)); // Password forte e randomica
                $defaultName = 'User-' . Str::substr($walletAddress, 0, 6); // Nome generico

                // **Controllo importante**: Evita email duplicate se più wallet usano la stessa email placeholder
                // Questo approccio con email fissa è problematico per utenti multipli.
                // Considera di generare email uniche basate sul wallet o usare null se possibile.
                // *** SOLUZIONE TEMPORANEA: Aggiungere un suffisso random all'email placeholder ***
                $uniqueEmail = Str::replace('@', '+' . Str::lower(Str::random(8)) . '@', $defaultEmail);

                $newUser = User::create([
                    'name' => $defaultName,
                    // 'email' => $defaultEmail, // <-- PROBLEMATICO PER DUPLICATI
                    'email' => $uniqueEmail, // <-- Usa email unica
                    'password' => $defaultPassword, // Password forte (non usata per login diretto)
                    'wallet' => $walletAddress,
                    'email_verified_at' => null, // Non verificato
                    'usertype' => 'wallet_only', // Aggiungi un tipo per identificarli
                    // Assegna ruolo 'guest' o un ruolo specifico 'connected'
                ]);

                 // Assegna ruolo 'guest' (o crea/usa un ruolo 'connected_wallet')
                $guestRole = \Spatie\Permission\Models\Role::where('name', 'guest')->first();
                if ($guestRole) {
                    $newUser->assignRole($guestRole);
                }

                Log::channel('florenceegi')->info('Wallet-only user created.', ['user_id' => $newUser->id, 'email' => $uniqueEmail]);

                // Stabilire sessione "connected"
                $this->establishConnectedSession($request, $newUser, $walletAddress);

                return response()->json([
                    'success' => true,
                    'message' => __('Wallet connected and temporary profile created.'),
                    'user_status' => 'wallet_only',
                    'user_name' => $newUser->name
                ]);
            }

        } catch (ValidationException $e) {
            Log::channel('florenceegi')->warning('Wallet Connect Validation failed:', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first('wallet_address') ?: __('Invalid wallet address format.'),
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Error during wallet connection:', [
                'message' => $e->getMessage(),
                'trace_snippet' => $e->getTraceAsString(5) // Snippet dello stack trace
            ]);
            return response()->json([
                'success' => false,
                'message' => __('An unexpected error occurred. Please try again.')
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Establishes the "connected" session state.
     *
     * @param Request $request
     * @param User $user
     * @param string $walletAddress
     * @return void
     */
    protected function establishConnectedSession(Request $request, User $user, string $walletAddress): void
    {
        // Rigenera ID sessione per sicurezza
        // $request->session()->regenerate();

        // Imposta dati sessione per lo stato "connected"
        $request->session()->put([
            'auth_status' => 'connected', // Stato specifico per "weak auth"
            'connected_wallet' => $walletAddress,
            'connected_user_id' => $user->id,
            // Non usare Auth::login($user) qui per non fare "strong auth"
        ]);

        Log::channel('florenceegi')->info('Connected session established.', [
            'user_id' => $user->id,
            'wallet' => $walletAddress,
            'session_id' => $request->session()->getId()
        ]);

        // Potresti anche impostare un cookie specifico se necessario,
        // ma la sessione è solitamente sufficiente se il frontend fa chiamate successive.
    }

    /**
     * Disconnette il wallet dell'utente.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disconnect(Request $request)
    {
        // Se stai usando una sessione per il wallet di guest, cancellala
        if (session()->has('connected_wallet')) {
            session()->forget('connected_wallet');
            session()->forget('auth_status');
            session()->forget('connected_user_id');

        }

        // Rimuovi il cookie 'connected_wallet'
        Cookie::queue(Cookie::forget('connected_wallet'));

        return response()->json([
            'success' => true,
            'message' => 'Wallet disconnesso con successo'
        ]);
    }

    // In WalletController.php
    public function status(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'connected_wallet' => $user->wallet,
                'is_authenticated' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'connected_wallet' => null,
            'is_authenticated' => false
        ]);
    }
}
