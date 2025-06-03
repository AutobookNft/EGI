<?php

namespace App\Auth\Guards;

use App\Models\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Custom Auth Guard: FEGI Weak Authentication Guard
 * 🎯 Purpose: Extend Laravel Auth to recognize weak authenticated users
 * 🧱 Core Logic: Check both traditional auth and FEGI weak auth
 * 🛡️ Security: Maintains separation between strong and weak auth
 *
 * @package App\Auth\Guards
 * @author Padmin D. Curtis
 * @version 1.2.0 (Fixed Session Access)
 * @date 2025-05-29
 *
 * @auth-extension Extends Laravel's authentication system
 * @weak-auth-support Recognizes FEGI-connected users
 * @session-based Uses session data for weak authentication
 */
class FegiGuard implements Guard
{
    use GuardHelpers;

    /** @var Request The current request */
    protected Request $request;

    /** @var bool Whether user has been resolved */
    protected bool $userResolved = false;

    /**
     * @Oracode Constructor
     * @param UserProvider $provider User provider instance
     * @param Request $request Current HTTP request
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * @Oracode Get the currently authenticated user with session fix
     * 🎯 Purpose: Return user for both strong auth and weak auth
     * 📤 Output: User instance or null
     *
     * @return User|null
     */

    public function user(): ?User
    {
        // Se un utente STRONG è stato risolto e cachato, restituiscilo immediatamente.
        // NOTA: GuardHelpers gestisce automaticamente $userResolved. Se $this->user non è null, $userResolved è true.
        if ($this->user !== null) {
            Log::channel('florenceegi')->info('FegiGuard: Returning cached STRONG user', ['user_id' => $this->user->id]);
            return $this->user;
        }

        // Se $this->user è null, la prima risoluzione (o una precedente) non ha trovato un utente,
        // O l'utente trovato era weak e non vogliamo cachare quel risultato così rigidamente se i dati weak appaiono tardi.
        // Rimuovi o commenta la riga `$this->userResolved = true;` se l'avevi messa qui prima del check strong auth.
        // Il GuardHelpers trait gestisce $userResolved = true quando $this->user viene settato (non null).

        // Debug session access - Controlla lo stato della sessione ADESSO
        $sessionDebug = [
            'auth_status' => session('auth_status'),
            'connected_user_id' => session('connected_user_id'),
            'is_weak_auth' => session('is_weak_auth'),
            'connected_wallet' => session('connected_wallet'),
            'session_id' => session()->getId(),
            // 'user_resolved_initial' => $this->userResolved, // Non necessario con il nuovo flusso
            // 'cached_user_initial' => $this->user ? 'exists' : 'null', // Sempre null qui dopo il primo if
        ];

        Log::channel('florenceegi')->info('FegiGuard: Attempting to resolve user (REVISED LOGIC)', [
            'session_data' => $sessionDebug
        ]);

        // 1. Prova prima l'autenticazione forte (web guard).
        // Auth::guard('web')->user() non è cachato internamente dal nostro FegiGuard,
        // ma invoca la risoluzione del 'web' guard.
        // Se trovato, setta $this->user e GuardHelpers setta $userResolved.
        $this->user = auth()->guard('web')->user();

        if ($this->user) {
            Log::channel('florenceegi')->info('FegiGuard: Found user via WEB guard (Strong Auth).', [
                'user_id' => $this->user->id
            ]);
            return $this->user;
        }

        // 2. Se non trovato tramite strong auth, controlla i dati di sessione per la weak auth.
        // Fai questa verifica *sempre* se $this->user è ancora null, perché i dati weak potrebbero essere apparsi.
        $connectedUserId = session('connected_user_id');
        $authStatus = session('auth_status');
        $isWeakAuthSession = session('is_weak_auth');

        if ($authStatus === 'connected' && $connectedUserId) {
            Log::channel('florenceegi')->info('FegiGuard: Session data suggests weak auth attempt is valid NOW.', [
                'connected_user_id' => $connectedUserId,
                'auth_status' => $authStatus,
                'is_weak_auth_session' => $isWeakAuthSession,
                'current_session_id' => session()->getId()
            ]);
            // Chiama getWeakAuthUser che contiene la tua logica dettagliata e log aggiuntivi.
            // getWeakAuthUser userà lo stato attuale della sessione.
            $this->user = $this->getWeakAuthUser();

            if ($this->user) {
                Log::channel('florenceegi')->info('FegiGuard: Successfully found WEAK auth user.', ['user_id' => $this->user->id]);
                // GuardHelpers setta $userResolved = true automaticamente quando $this->user non è null
                return $this->user;
            } else {
                // Questo log si attiverebbe se getWeakAuthUser (nonostante i dati sessione) non trova l'utente nel DB o fallisce il suo check interno.
                Log::channel('florenceegi')->warning('FegiGuard: getWeakAuthUser returned null despite session data suggesting weak auth.');
            }
        } else {
            Log::channel('florenceegi')->info('FegiGuard: Session data not sufficient for weak auth attempt (still null or not connected).');
        }


        // Se arriviamo qui, nessun utente è stato trovato (strong o weak) in questo ciclo.
        // GuardHelpers imposterà $this->user = null e $userResolved = true per questa richiesta.
        Log::channel('florenceegi')->info('FegiGuard: No user found after all checks for this request, caching null.');
        return $this->user; // Sarà null
    }



    /**
     * @Oracode Validate user credentials
     * 🎯 Purpose: Handle credential validation (not used for weak auth)
     * 📥 Input: Credentials array
     * 📤 Output: Boolean validation result
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * @Oracode Get weak auth user from session with robust checking
     * 🎯 Purpose: Retrieve user based on weak auth session data
     * 📤 Output: User instance or null
     *
     * @return User|null
     */
    protected function getWeakAuthUser(): ?User
    {
        // Use Laravel session() helper instead of injected session
        $authStatus = session('auth_status');
        $connectedUserId = session('connected_user_id');
        $isWeakAuth = session('is_weak_auth');

        Log::channel('florenceegi')->info('FegiGuard: getWeakAuthUser - Session Check', [
            'auth_status' => $authStatus,
            'connected_user_id' => $connectedUserId,
            'is_weak_auth' => $isWeakAuth,
            'current_session_id' => session()->getId() // Log current session ID here too
        ]);


        if ($authStatus === 'connected' && $connectedUserId) {
            // Retrieve user by ID
            $user = User::find($connectedUserId);

            Log::channel('florenceegi')->info('FegiGuard: User lookup result (ROBUST)', [
                'user_found' => $user ? true : false,
                'user_id' => $user ? $user->id : null,
                'user_has_is_weak_auth_field' => $user ? isset($user->is_weak_auth) : false,
                'user_is_weak_auth_value' => $user ? $user->is_weak_auth : null,
                'session_is_weak_auth' => $isWeakAuth
            ]);

            if ($user) {
                // ROBUST CHECK: Accept user if session says weak auth, even if DB field missing
                $userIsWeakAuth = $user->is_weak_auth ?? false;
                $sessionSaysWeakAuth = $isWeakAuth ?? false;

                if ($userIsWeakAuth || $sessionSaysWeakAuth) {
                    Log::channel('florenceegi')->info('FegiGuard: Accepting weak auth user', [
                        'user_id' => $user->id,
                        'user_is_weak_auth' => $userIsWeakAuth,
                        'session_says_weak' => $sessionSaysWeakAuth
                    ]);
                    return $user;
                } else {
                    Log::channel('florenceegi')->warning('FegiGuard: User found but not marked as weak auth', [
                        'user_id' => $user->id,
                        'user_is_weak_auth' => $userIsWeakAuth,
                        'session_is_weak_auth' => $sessionSaysWeakAuth
                    ]);
                }
            } else {
                Log::channel('florenceegi')->error('FegiGuard: User not found in database', [
                    'requested_user_id' => $connectedUserId,
                    'total_users_count' => User::count()
                ]);
            }
        }

        return null;
    }

    /**
     * @Oracode Check if current user is weak auth
     * 🎯 Purpose: Determine if user is authenticated via FEGI (weak auth)
     * 📤 Output: Boolean weak auth status
     *
     * @return bool
     */
    public function isWeakAuth(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        // Check if user is marked as weak auth AND session confirms it
        return $user->is_weak_auth && session('is_weak_auth', false);
    }

    /**
     * @Oracode Check if current user is strong auth (traditional login)
     * 🎯 Purpose: Determine if user is traditionally logged in
     * 📤 Output: Boolean strong auth status
     *
     * @return bool
     */
    public function isStrongAuth(): bool
    {
        return $this->check() && !$this->isWeakAuth();
    }

    /**
     * @Oracode Get authentication type
     * 🎯 Purpose: Return string describing current auth type
     * 📤 Output: Auth type string
     *
     * @return string 'strong', 'weak', or 'guest'
     */
    public function getAuthType(): string
    {
        if ($this->guest()) {
            return 'guest';
        }

        return $this->isWeakAuth() ? 'weak' : 'strong';
    }

    /**
     * @Oracode Get connected wallet address
     * 🎯 Purpose: Return wallet address for weak auth users
     * 📤 Output: Wallet address string or null
     *
     * @return string|null
     */
    public function getConnectedWallet(): ?string
    {
        if ($this->isWeakAuth()) {
            return session('connected_wallet');
        }

        // For strong auth users, return wallet from user model
        $user = $this->user();
        return $user ? $user->wallet : null;
    }

    /**
     * @Oracode Logout user (both strong and weak auth)
     * 🎯 Purpose: Clear authentication for current user
     *
     * @return void
     */
    public function logout(): void
    {
        // Clear weak auth session data using session helper
        session()->forget([
            'auth_status',
            'connected_wallet',
            'connected_user_id',
            'is_weak_auth'
        ]);

        // Clear traditional auth if present
        if (auth()->guard('web')->check()) {
            auth()->guard('web')->logout();
        }

        // Reset internal state (uses GuardHelpers property)
        $this->user = null;
        $this->userResolved = false;
    }
}
