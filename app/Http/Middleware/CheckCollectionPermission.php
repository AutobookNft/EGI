<?php

namespace App\Http\Middleware;

use App\Helpers\FegiAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CheckCollectionPermission {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null) {
        // Recupera l'utente autenticato
        $user = FegiAuth::user();

        if (!$user) {
            Log::channel('florenceegi')->error('Utente non autenticato', [
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
            ]);
            abort(403, 'Utente non autenticato.');
        }

        // Verifica se l'utente è un amministratore
        $userModel = User::find($user->id);
        if (!$userModel) {
            Log::channel('florenceegi')->error('Utente non trovato', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
            ]);
            abort(403, 'Utente non trovato.');
        }

        // Verifica se la rotta è 'collections.open'
        $rotta = $request->route()->getName();

        if ($rotta === 'collections.open') {
            // Trova tutte le collection associate all'utente tramite collection_user
            $collections = Collection::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            // Se esiste una sola collection, usala per il controllo
            if ($collections->count() === 1) {
                $collection = $collections->first();
            } else {
                // Se ci sono più collection, consenti l'accesso per il selettore/carousel
                return $next($request);
            }
        } else {

            Log::channel('florenceegi')->info('User:currentCollection', [
                'user_id' => $user->id,
                'current_collection_id' => $userModel->current_collection_id,
            ]);

            // 🎯 SPECIAL CASE: Collection creation when user has no current collection
            if ($permission === 'create_collection' && !$userModel->current_collection_id) {
                // For collection creation, check user's global permissions instead of collection-specific ones
                if ($userModel->can($permission)) {
                    Log::channel('florenceegi')->info('Collection creation permission granted (no current collection)', [
                        'user_id' => $user->id,
                        'permission' => $permission,
                        'user_roles' => $userModel->roles->pluck('name')->toArray(),
                    ]);
                    return $next($request);
                } else {
                    Log::channel('florenceegi')->error('User lacks global permission for collection creation', [
                        'user_id' => $user->id,
                        'permission' => $permission,
                        'user_roles' => $userModel->roles->pluck('name')->toArray(),
                    ]);
                    abort(403, 'Non hai i permessi necessari per eseguire questa azione.');
                }
            }

            $collection = $userModel->currentCollection;

            // Se la collection non esiste, restituisci un errore 404
            if (!$collection) {
                Log::channel('florenceegi')->error('Collection non trovata', [
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                    'route' => $request->route()->getName(),
                    'permission' => $permission,
                ]);
                abort(404, 'CheckCollectionPermission: Collection non trovata.');
            }
        }

        // Verifica se l'utente è membro della collection tramite collection_user
        $membership = $collection->users()->where('user_id', $user->id)->first();

        if (!$membership) {
            Log::channel('florenceegi')->error('Utente non membro della collection', [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
            ]);
            abort(403, 'Non sei un membro della collection associata.');
        }

        // Recupera il ruolo dell'utente nella collection
        $roleName = $membership->pivot->role;

        // Verifica se il ruolo esiste in Spatie
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            Log::channel('florenceegi')->error('Ruolo non trovato', [
                'role_name' => $roleName,
                'user_id' => $user->id,
            ]);
            abort(403, 'Ruolo non valido.');
        }

        // Verifica se il ruolo ha il permesso richiesto
        if ($permission && !$role->hasPermissionTo($permission)) {
            Log::channel('florenceegi')->error('Permesso negato', [
                'user_id' => $user->id,
                'permission' => $permission,
                'role_name' => $roleName,
            ]);
            abort(403, 'Non hai i permessi necessari per eseguire questa azione.');
        }

        Log::channel('florenceegi')->info('Permesso concesso', [
            'user_id' => $user->id,
            'collection_id' => $collection->id,
            'permission' => $permission,
            'role_name' => $roleName,
        ]);

        return $next($request);
    }
}