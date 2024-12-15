<?php

namespace App\Traits;

use Spatie\Permission\Traits\HasRoles;

trait HasTeamRoles
{
    use HasRoles;

    /**
     * Assegna un ruolo all'utente in un contesto di team specifico.
     */
    public function assignRoleToTeam(string $role, $team)
    {
        $this->assignRole($role, $team);
    }

    /**
     * Verifica se l'utente ha un ruolo specifico in un team.
     */
    public function hasRoleInTeam(string $role, $team): bool
    {
        return $this->hasRole($role, $team);
    }

    /**
     * Relazione con i ruoli assegnati in un contesto di team.
     */
    public function roles()
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            'model_id',
            'role_id'
        )->withPivot('team_id');
    }
}
