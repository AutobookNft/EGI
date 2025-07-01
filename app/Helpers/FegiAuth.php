<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * @Oracode Helper Class: Unified Authentication Utilities (Fixed Permissions Logic)
 * 🎯 Purpose: Provide easy access to unified authentication methods using Spatie permissions
 * 🧱 Core Logic: Uses Spatie role/permission system as Single Source of Truth
 * 🛡️ Security: Maintains separation between strong and weak auth logic with consistency
 *
 * @package App\Helpers
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 2.1.0 (Fixed Permissions Consistency)
 * @date 2025-06-29
 *
 * @utility-class Facade-style access to unified authentication
 * @single-source-truth Uses Spatie permissions instead of hardcoded lists
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
        if (static::$userResolutionAttempted) {
            return static::$resolvedUser;
        }

        // Segna che la risoluzione è stata tentata.
        static::$userResolutionAttempted = true;

        // 1. Prova a ottenere l'utente dall'autenticazione FORTE (guard 'web')
        $user = Auth::guard('web')->user();

        if ($user) {
            static::$resolvedUser = $user;
            return static::$resolvedUser;
        }

        // 2. Se NON c'è un utente forte, controlla la sessione per i dati dell'autenticazione DEBOLE
        $connectedUserId = session('connected_user_id');
        $authStatus = session('auth_status');

        if ($authStatus === 'connected' && $connectedUserId !== null) {
            $user = User::find($connectedUserId);

            if ($user) {
                // Assicurati che l'utente weak abbia il ruolo corretto
                static::ensureWeakAuthRole($user);

                static::$resolvedUser = $user;
                return static::$resolvedUser;
            }
        }

        static::$resolvedUser = null;
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
        return !static::isStrongAuth() // Non è loggato forte con il guard web
               && session('auth_status') === 'connected' // La sessione ha lo status "connected"
               && session('connected_user_id') !== null // E c'è un ID utente nella sessione
               && User::find(session('connected_user_id')) !== null; // Verifica che l'utente esista nel DB
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
        if (static::isWeakAuth()) {
            return session('connected_wallet');
        }

        $user = static::user();
        return $user ? $user->wallet : null;
    }

    /**
     * @Oracode Check if user can perform action - FIXED VERSION
     * 🎯 Purpose: Permission check with auth type awareness using Spatie as Single Source of Truth.
     * 📥 Input: Permission string
     * 📤 Output: Boolean permission status
     *
     * @param string $permission Permission to check
     * @return bool
     *
     * @permission-aware Uses Spatie permissions consistently for both strong and weak auth
     * @single-source-truth No more hardcoded permission lists
     */
    public static function can(string $permission): bool
    {
        $user = static::user();

        if (!$user) {
            return false; // Nessun utente loggato o connesso
        }

        // Per utenti autenticati FORTEMENTE, usa il meccanismo di permessi standard
        if (static::isStrongAuth()) {
            return method_exists($user, 'can') ? $user->can($permission) : false;
        }

        // Per utenti autenticati DEBOLMENTE, usa la logica di permessi limitati
        return static::isWeakAuth() && static::canWeakAuth($permission);
    }

    /**
     * @Oracode Check weak auth permissions - REFACTORED TO USE SPATIE
     * 🎯 Purpose: Use Spatie role system instead of hardcoded permissions list.
     * 📥 Input: Permission string
     * 📤 Output: Boolean permission status
     *
     * @param string $permission
     * @return bool
     *
     * @weak-auth-permissions Uses Spatie 'weak_connect' role as Single Source of Truth
     * @backward-compatible Maintains existing method signature
     */
    protected static function canWeakAuth(string $permission): bool
    {
        $user = static::user();

        if (!$user) {
            return false;
        }

        // Assicurati che l'utente weak abbia il ruolo corretto
        static::ensureWeakAuthRole($user);

        // Usa Spatie per controllare i permessi del ruolo weak_connect
        return method_exists($user, 'can') ? $user->can($permission) : false;
    }

    /**
     * @Oracode Ensure weak auth user has correct role - NUOVA FUNZIONE
     * 🎯 Purpose: Assicura che gli utenti weak auth abbiano il ruolo weak_connect
     * 📥 Input: User instance
     * 📤 Output: Void (side effect: assigns role if missing)
     *
     * @param User $user
     * @return void
     *
     * @role-management Ensures consistency between session state and Spatie roles
     * @idempotent Safe to call multiple times
     */
    protected static function ensureWeakAuthRole(User $user): void
    {
        try {
            // Se l'utente non ha già il ruolo weak_connect, assegnalo
            if (!$user->hasRole('weak_connect')) {
                $weakRole = Role::firstOrCreate(['name' => 'weak_connect']);
                $user->assignRole($weakRole);
            }
        } catch (Throwable $e) {
            // Log silenzioso dell'errore, ma non bloccare l'esecuzione
            // Log::warning('Failed to assign weak_connect role', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
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
            'is_weak_auth'
        ]);

        // Esegui il logout tradizionale se l'utente era loggato forte
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Resetta lo stato interno dell'helper
        static::$resolvedUser = null;
        static::$userResolutionAttempted = false;
    }

    /**
     * @Oracode Assign a role to a user by user ID
     * 🎯 Purpose: Assigns a specific role to the user identified by $userId
     *
     * @param int|string $userId  User ID to assign the role to
     * @param string $roleName    Role name to assign (e.g., "creator")
     * @return bool               True if assignment succeeded or already present, false otherwise
     *
     * @single-point-assignment Centralized role assignment logic
     */
    public static function assignRoleToUser($userId, $roleName): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $role = Role::firstOrCreate(['name' => $roleName]);
        if ($user->hasRole($roleName)) {
            return true;
        }

        try {
            $user->assignRole($role);
            return true;
        } catch (Throwable $e) {
            return false;
        }
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
}
