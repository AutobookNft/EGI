<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use Spatie\Permission\Models\Role;

class CheckTeamPermission
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

        // Verifica se la rotta è per la creazione di una nuova collection
        if ($request->route()->getName() === 'collections.create') {
            // Se l'utente ha il permesso di creare una collection, permetti l'accesso
            if ($user->can('create_collection')) {
                return $next($request);
            }

            // Se l'utente non ha il permesso, blocca l'accesso
            abort(403, 'Non hai il permesso di creare una collection.');
        }

        // Verifica se la rotta è '/collections/open'
        if ($request->route()->getName() === 'collections.open') {
            // Trova tutte le collection attive dei team a cui l'utente è associato
            $collections = Collection::whereHas('team', function ($query) use ($user) {
                $query->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                });
            })->get();

            // Se esiste una sola collection, usala per il controllo
            if ($collections->count() === 1) {
                $collection = $collections->first();
            } else {
                // Se non c'è una sola collection, blocca l'accesso
                return $next($request);
            }
        } else {
            // Recupera l'ID della collection dalla richiesta per le altre rotte
            $collectionId = $request->route('id') ?? $request->route('collection');
            $collection = Collection::find($collectionId);

            // Se la collection non esiste, restituisci un errore 404
            if (!$collection) {
                abort(404, 'Collection non trovata.');
            }

        }

        // Recupera il team associato alla collection
        $team = $collection->team;

        // Se il team non esiste, restituisci un errore 404
        if (!$team) {
            abort(404, 'Team non trovato.');
        }

        // Recupera il ruolo dell'utente nel team dalla tabella pivot team_user
        $teamMembership = $user->teams()->where('team_id', $team->id)->first();

        // Verifica se l'utente è membro del team
        if (!$user->isMemberOfTeam($team)) {
            abort(403, 'Non sei un membro del team associato a questa collection.');
        }

        if (!$teamMembership) {
            abort(403, 'Non sei un membro del team associato a questa collection.');
        }

        $roleName = $teamMembership->pivot->role;

        // Verifica se il ruolo esiste in Spatie
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            abort(403, 'Ruolo non valido.');
        }

        if (!$role) {
            abort(403, 'Ruolo non valido.');
        }

        // Verifica se il ruolo ha il permesso richiesto
        if (!$role->hasPermissionTo($permission)) {
            abort(403, 'Non hai i permessi necessari per eseguire questa azione.');
        }

        return $next($request);
    }
}
