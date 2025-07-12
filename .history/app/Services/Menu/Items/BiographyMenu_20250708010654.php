<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Biography Management
 * 🎯 Purpose: Access to user biography CRUD operations
 * 🧱 Core Logic: Links to biography management interface
 * 🛡️ Security: User can only manage own biographies
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography Integration)
 * @date 2025-01-07
 */
class BiographyMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            name: __('menu.biography.manage'),
            route: 'biography.manage',
            icon: 'book',
            permission: 'manage_own_biographies'
        );
    }
}
