<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;
use Illuminate\Support\Facades\Session;

/**
 * @Oracode Menu Item: Term of Service (Corrected & Final)
 * 🎯 Purpose: GDPR menu item that correctly passes route name and params to the evolved parent constructor.
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS2.0) for Fabio Cherici
 * @version 1.3.0 (FlorenceEGI MVP - Final Alignment)
 * @deadline 2025-06-30
 */
class TermOfServiceMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $validUserTypes = [
            'collector', 'creator', 'patron', 'epp', 'company', 'trader_pro'
        ];

        $sessionUserType = Session::get('user_type');

        $finalUserType = in_array($sessionUserType, $validUserTypes)
            ? $sessionUserType
            : 'creator';

        // 🎯 Ora questa chiamata è corretta, perché il costruttore di MenuItem
        // è stato potenziato per accettare il quinto parametro (routeParams).
        parent::__construct(
            'menu.terms_of_service',
            'legal.terms', // Nome della rotta
            'file-text',   // Icona
            'view_privacy_policy', // Permesso
            routeParams: [
                'userType' => $finalUserType,
                'redirect_url' => request()->fullUrl() // Ecco l'iniezione del contesto
            ]
        );
    }
}
