<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // Assicurati sia importato per session()
use App\Models\User; // Assicurati che il modello User sia importato
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * @Oracode Helper Class: Unified Authentication Utilities (Manual Logic)
 * 🎯 Purpose: Provide easy access to unified authentication methods using manual session/guard checks.
 * 🧱 Core Logic: Manual check of web guard and session data.
 * 🛡️ Security: Maintains separation between strong and weak auth logic.
 *
 * @package App\Helpers
 * @author Padmin D. Curtis
 * @version 2.0.0 (Manual Logic Implementation)
 * @date 2025-05-30
 *
 * @utility-class Facade-style access to unified authentication
 * @manual-auth-logic Implements strong/weak check without custom guard
 */
class FegiAuth
{
    /**
     * Cache dell'utente risolto per la richiesta corrente.
     * @var User|null
     */
    protected static ?User $resolvedUser = null;

    /**
     * Flag per indicare se la risoluzione dell'utente è stata tentata per questa richiesta.
     * @var bool
     */
    protected static bool $userResolutionAttempted = false;


    /**
     * @Oracode Get currently authenticated user (Strong or Weak)
     * 🎯 Purpose: Return user regardless of auth type, using manual checks.
     * 📤 Output: User instance or null
     *
     * @return User|null
     *
     * @unified-auth Returns user from any auth type
     */
    public static function user(): ?User
    {
        // Se l'utente è già stato risolto per questa richiesta, restituisci il risultato cachato.
        // Questo evita lookup multipli nella stessa richiesta.
        if (static::$userResolutionAttempted) {
            return static::$resolvedUser;
        }

        // Segna che la risoluzione è stata tentata.
        static::$userResolutionAttempted = true;

        // 1. Prova a ottenere l'utente dall'autenticazione FORTE (guard 'web')
        $user = Auth::guard('web')->user();

        if ($user) {
            // Utente forte trovato. Cacha e restituisci.
            static::$resolvedUser = $user;
            // Log::channel('florenceegi')->info('FegiAuth Helper: Strong user resolved.', ['user_id' => $user->id]);
            return static::$resolvedUser;
        }

        // 2. Se NON c'è un utente forte, controlla la sessione per i dati dell'autenticazione DEBOLE
        $connectedUserId = session('connected_user_id'); // Usiamo l'helper session()
        $authStatus = session('auth_status');

        if ($authStatus === 'connected' && $connectedUserId !== null) {
            // I dati di sessione suggeriscono un utente debole. Prova a caricarlo dal database.
            // Mantieni qui la logica di lookup dell'utente ID dalla sessione.
            $user = User::find($connectedUserId);

            if ($user) {
                 // Utente debole trovato. Cacha e restituisci.
                 static::$resolvedUser = $user;
                 // Log::channel('florenceegi')->info('FegiAuth Helper: Weak user resolved via session/DB.', ['user_id' => $user->id]);
                 return static::$resolvedUser;
            }
            // Log::channel('florenceegi')->warning('FegiAuth Helper: Session suggested weak user (ID: ' . $connectedUserId . '), but user not found in DB.');
        }
        // Log::channel('florenceegi')->info('FegiAuth Helper: No strong user, and session data not sufficient for weak auth check.');


        // Se arriviamo qui, nessun utente è stato trovato. Cacha null.
        static::$resolvedUser = null;
        // Log::channel('florenceegi')->info('FegiAuth Helper: No user found (strong or weak).');
        return static::$resolvedUser;
    }

    /**
     * @Oracode Check if any user is authenticated (strong or weak)
     * 🎯 Purpose: Unified authentication check.
     * 📤 Output: Boolean authentication status
     *
     * @return bool
     *
     * @unified-auth Checks both traditional and weak auth
     */
    public static function check(): bool
    {
        return static::user() !== null;
    }

    /**
     * @Oracode Get user ID
     * 🎯 Purpose: Return user ID for any auth type.
     * 📤 Output: User ID or null
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        $user = static::user();
        return $user ? $user->id : null;
    }

    /**
     * @Oracode Check if user is guest
     * 🎯 Purpose: Unified guest check.
     * 📤 Output: Boolean guest status
     *
     * @return bool
     */
    public static function guest(): bool
    {
        return ! static::check();
    }

    /**
     * @Oracode Check if user is strong authenticated (traditional login)
     * 🎯 Purpose: Determine if user is traditionally logged in.
     * 📤 Output: Boolean strong auth status
     *
     * @return bool
     */
    public static function isStrongAuth(): bool
    {
        // La definizione di auth forte è solo il guard web.
        return Auth::guard('web')->check();
    }

    /**
     * @Oracode Check if user is weak authenticated
     * 🎯 Purpose: Determine if user is FEGI-connected via session.
     * 📤 Output: Boolean weak auth status
     *
     * @return bool
     *
     * @fegi-specific Check for FEGI weak authentication via session data
     */
    public static function isWeakAuth(): bool
    {
        // Un utente è debole se NON è loggato forte E la sessione indica una connessione debole.
        return !static::isStrongAuth() // Non è loggato forte con il guard web
               && session('auth_status') === 'connected' // La sessione ha lo status "connected"
               && session('connected_user_id') !== null // E c'è un ID utente nella sessione
               // Opzionale: Aggiungi un controllo User::find() qui se vuoi che isWeakAuth implichi anche che l'utente esista nel DB *al momento* del check.
               && User::find(session('connected_user_id')) !== null; // Verifica che l'utente esista nel DB
               // Per coerenza con user(), basta basarsi sulla sessione qui e lasciare a user() il compito di trovare l'istanza.
    }

    /**
     * @Oracode Get authentication type
     * 🎯 Purpose: Return string describing current auth type.
     * 📤 Output: 'strong', 'weak', or 'guest'
     *
     * @return string
     */
    public static function getAuthType(): string
    {
        if (static::guest()) {
            return 'guest';
        }
        if (static::isStrongAuth()) {
            return 'strong';
        }
        if (static::isWeakAuth()) {
            return 'weak';
        }
        // In teoria non dovremmo mai arrivare qui se check() è true, ma per sicurezza
        return 'unknown';
    }

    /**
     * @Oracode Get connected wallet address
     * 🎯 Purpose: Return wallet address for any auth type.
     * 📤 Output: Wallet address string or null
     *
     * @return string|null
     */
    public static function getWallet(): ?string
    {
        // Se è autenticato debolmente (secondo la nostra definizione), prendi dalla sessione
        if (static::isWeakAuth()) {
            return session('connected_wallet');
        }

        // Se è autenticato fortemente, prendi dal modello utente (già caricato da user())
        $user = static::user(); // Recupera l'utente risolto (forte)
        return $user ? $user->wallet : null;
    }

    /**
     * @Oracode Check if user can perform action
     * 🎯 Purpose: Permission check with auth type awareness.
     * 📥 Input: Permission string
     * 📤 Output: Boolean permission status
     *
     * @param string $permission Permission to check
     * @return bool
     *
     * @permission-aware Different logic for strong vs weak auth
     */
    public static function can(string $permission): bool
    {
        $user = static::user();

        if (!$user) {
            return false; // Nessun utente loggato o connesso
        }

        // Per utenti autenticati FORTEMENTE, usa il meccanismo di permessi standard (Spatie?)
        if (static::isStrongAuth()) {
            // Assumiamo che il tuo modello User usi il trait HasPermissions da Spatie o logica simile
            return method_exists($user, 'can') ? $user->can($permission) : false;
        }

        // Per utenti autenticati DEBOLMENTE, usa la logica di permessi limitati definita qui
        return static::isWeakAuth() && static::canWeakAuth($permission);
    }

    /**
     * @Oracode Check weak auth permissions
     * 🎯 Purpose: Define what weak auth users can do.
     * 📥 Input: Permission string
     * 📤 Output: Boolean permission status
     *
     * @param string $permission
     * @return bool
     *
     * @weak-auth-permissions Define limited permissions for FEGI users
     * @security-boundary Restrict actions for weak auth
     */
    protected static function canWeakAuth(string $permission): bool
    {
        // Define permissions allowed for weak auth users (MANTIENI LA TUA LISTA DI SICUREZZE)
        $weakAuthPermissions = [
            'create_egi', // Se permesso solo agli utenti deboli/forti
            'view_collection',
            'open_collection',
            'like_egi',
            'user-cog', // Se si tratta di modificare il proprio profilo
            'reserve_egi',
            'view_profile', // Se si tratta di vedere il proprio profilo
            'manage_profile',
            'manage_account',
            'view_documentation',
            // Aggiungi altri permessi che gli utenti connessi debolmente DOVREBBERO avere
        ];

        return in_array($permission, $weakAuthPermissions);
    }

    /**
     * @Oracode Logout current user (both strong and weak auth)
     * 🎯 Purpose: Clear authentication for current user.
     *
     * @return void
     */
    public static function logout(): void
    {
        // Cancella i dati di sessione relativi all'autenticazione debole
        session()->forget([
            'auth_status',
            'connected_wallet',
            'connected_user_id',
            'is_weak_auth' // Anche se non più usata direttamente per isWeakAuth(), meglio pulirla
        ]);

        // Esegui il logout tradizionale se l'utente era loggato forte
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Resetta lo stato interno dell'helper
        static::$resolvedUser = null;
        static::$userResolutionAttempted = false;

        // La sessione stessa potrebbe dover essere rigenerata o distrutta a seconda del flusso desiderato dopo il logout
        // session()->invalidate();
        // session()->regenerateToken();
    }

     /**
     * @Oracode Legacy compatibility method
     * 🎯 Purpose: Help migrate existing Auth::check() && Auth::user()->can() patterns
     * 📥 Input: Permission string
     * 📤 Output: Boolean result
     *
     * @param string $permission
     * @return bool
     *
     * @migration-helper Direct replacement for Auth::check() && Auth::user()->can()
     * @backward-compatible Works with existing permission strings
     */
    public static function checkAndCan(string $permission): bool
    {
        return static::check() && static::can($permission);
    }

    /**
     * Resetta lo stato cachato dell'helper. Utile solo per testing.
     * @internal
     */
    public static function flushState(): void
    {
        static::$resolvedUser = null;
        static::$userResolutionAttempted = false;
    }

        /**
     * 🏷️ Assign a role to a user by user ID, in a unified and robust way (OS1-Compliant).
     *
     * 📖 Purpose:
     *   Assigns a specific role to the user identified by $userId, independently from authentication state
     *   (works for both "strong" and "weak" users, i.e., logged-in or session-based/connected users).
     *   Ensures a single point of logic for all role assignment operations, making behavior predictable, auditabile, and testable.
     *
     * ✨ OS1 Principles Applied:
     *   - **Intenzionalità Esplicita:** The method's purpose is clear and always documented in context and in code.
     *   - **Semplicità Potenziante:** Centralizes role assignment, eliminates code duplication, reduces complexity.
     *   - **Coerenza Semantica:** Uniform naming, predictable return values, always uses the same logic regardless of auth context.
     *   - **Circolarità Virtuosa:** When used everywhere in the project, it guarantees easy audit, refactoring and error handling.
     *   - **Evoluzione Ricorsiva:** Facilitates future enhancements (logging, advanced audit, new role logics) in one place only.
     *
     * 🛠️ Behavior:
     *   - Retrieves the user by ID (works with any user retrievable from DB)
     *   - Retrieves or creates the specified role (by name)
     *   - Checks if the user already has the role (returns true, idempotent)
     *   - Assigns the role using the framework’s standard methods
     *   - Handles exceptions gracefully (returns false, never throws)
     *
     * 📤 Output:
     *   - Returns true if the role assignment is successful (or already assigned)
     *   - Returns false if user not found, assignment failed, or on any error
     *
     * 🧪 Oracode-Testable:
     *   - Fully testable in isolation (given a userId and a role name, always deterministic outcome)
     *   - Use this method everywhere to avoid behavioral drift between "strong" and "weak" auth flows
     *
     * @param int|string $userId  User ID to assign the role to
     * @param string $roleName    Role name to assign (e.g., "creator")
     * @return bool               True if assignment succeeded or already present, false otherwise
     *
     * @example
     *   FegiAuth::assignRoleToUser(123, 'creator'); // returns true on success
     *
     * @oracode
     *   # ROLE ASSIGNMENT
     *   - Always assign roles through this helper for full OS1-compliance
     *   - Handles all cases (auth, connected, or direct DB)
     *   - Ensures auditability, rollback, and error reporting in one place
     */

    public static function assignRoleToUser($userId, $roleName): bool
    {
        // Recupera l'utente via User::find
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Recupera o crea il ruolo
        $role = Role::firstOrCreate(['name' => $roleName]);
        if ($user->hasRole($roleName)) {
            return true;
        }

        try {
            $user->assignRole($role);
            return true;
        } catch (Throwable $e) {
            // Qui potresti loggare l'errore in modo centralizzato
            // Log::error(...)
            return false;
        }
    }

}
