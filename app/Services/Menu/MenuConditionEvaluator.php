<?php

namespace App\Services\Menu;

use App\Helpers\FegiAuth;
use Illuminate\Support\Facades\Log;

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

        // Se non è richiesto un permesso specifico, mostra la voce di menu
        if (empty($menuItem->permission)) {
            return true;
        }

        $user = FegiAuth::user();


        // Log::channel('upload')->debug('MenuConditionEvaluator: Checking permission for menu item', [
        //     'user_role' => $user ? $user->role : 'guest',
        //     'item' => $menuItem->name,
        //     'permission' => $menuItem->permission,
        //     'user_id' => FegiAuth::id(),
        //     'user_authenticated' => FegiAuth::check(),
        //     'user_can' => FegiAuth::can($menuItem->permission),
        // ]);

        // Controlla se l'utente autenticato ha il permesso richiesto
        return FegiAuth::check() && FegiAuth::can($menuItem->permission);
    }
}
