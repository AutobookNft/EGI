<?php

namespace App\Services\Menu;

use Illuminate\Support\Facades\Auth;

class MenuConditionEvaluator
{
    /**
     * Verifica se una voce di menu può essere visualizzata in base ai permessi dell'utente.
     *
     * @param MenuItem $menuItem
     * @return bool
     */
    public function shouldDisplay(MenuItem $menuItem): bool
    {

        // dump([
        //     'item' => $menuItem->name,
        //     'permission' => $menuItem->permission,
        //     'user' => Auth::check() ? Auth::user()->toArray() : null,
        //     'can' => Auth::check() && Auth::user()->can($menuItem->permission),
        // ]);

        // Se non è richiesto un permesso specifico, mostra la voce di menu
        if (empty($menuItem->permission)) {
            return true;
        }

        // Controlla se l'utente autenticato ha il permesso richiesto
        return Auth::check() && Auth::user()->can($menuItem->permission);
    }
}
