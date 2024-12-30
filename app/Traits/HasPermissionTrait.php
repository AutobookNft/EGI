<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

trait HasPermissionTrait
{
    /**
     * Verifica se l'utente autenticato ha il permesso specificato per una collezione.
     *
     * @param  $collection
     * @param  string  $permission
     * @return bool
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function hasPermission($collection, string $permission): bool
    {
        // Recupera l'utente autenticato
        $user = Auth::user();

        Log::channel('florenceegi')->info('HasPermissionTraits: User: ' . $user->id);

        // Leggi il ruolo dell'utente nella tabella collection_user
        $roleName = $collection->users()
            ->where('user_id', $user->id)
            ->pluck('role')
            ->first();

        if (!$roleName) {
            // Lancia un'eccezione se l'utente non è associato alla collezione
            throw new \Illuminate\Auth\Access\AuthorizationException('Non sei associato a questa collezione.');
        }

        // Verifica se il ruolo dell'utente ha il permesso richiesto
        $hasPermission = Role::where('name', $roleName)
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();

        if (!$hasPermission) {
            // Lancia un'eccezione se il permesso non è presente
            throw new \Illuminate\Auth\Access\AuthorizationException('Non hai i permessi necessari per eseguire questa azione.');
        }

        return true;
    }
}
