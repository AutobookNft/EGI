<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: View Public Biography
 * 🎯 Purpose: View user's public biography
 * 🧱 Core Logic: Links to public biography display
 * 🛡️ Security: User can view own biography
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography Integration)
 * @date 2025-01-07
 */
class ViewBiographyMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            name: __('menu.biography.view'),
            route: 'biography.view',
            icon: 'visibility',
            permission: 'view_own_biography'
        );
    }
}
