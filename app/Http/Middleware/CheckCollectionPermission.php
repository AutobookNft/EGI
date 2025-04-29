<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class CheckCollectionPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();

        Log::channel('florenceegi')->info('Middleware: CheckCollectionPermission', [
            'user_id' => $user->id,
            'permission' => $permission,
            'user_name' => $user->name,
        ]);

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
            // Recupera l'ID della collection dalla richiesta per le altre rotte
            $collectionId = $request->route('id') ?? $request->route('collection');

            Log::channel('florenceegi')->info('Middleware: CheckCollectionPermission', [
                'collection_id' => $collectionId,
            ]);


            $collection = Collection::find($collectionId);


            // Se la collection non esiste, restituisci un errore 404
            if (!$collection) {
                abort(404, 'CheckCollectionPermission: Collection non trovata.');
            }
        }

        // Verifica se l'utente è membro della collection tramite collection_user
        $membership = $collection->users()->where('user_id', $user->id)->first();

        if (!$membership) {
            abort(403, 'Non sei un membro della collection associata.');
        }

        // Recupera il ruolo dell'utente nella collection
        $roleName = $membership->pivot->role;

        // Log::channel('florenceegi')->info('Middleware: CheckCollectionPermission', [
        //     'collection_id' => $collection->id,
        //     'role_name' => $roleName,
        // ]);

        // Verifica se il ruolo esiste in Spatie
        $role = Role::where('name', $roleName)->first();
        // log::channel('florenceegi')->info('Middleware: CheckCollectionPermission', [
        //     'role' => $role,
        // ]);

        if (!$role) {
            abort(403, 'Ruolo non valido.');
        }

        // Verifica se il ruolo ha il permesso richiesto
        if ($permission && !$role->hasPermissionTo($permission)) {
            abort(403, 'Non hai i permessi necessari per eseguire questa azione.');
        }

        return $next($request);
    }
}
