<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: BackToDashboardMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.back_to_dashboard
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class BackToDashboardMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.back_to_dashboard',
            'dashboard',
            'arrow-left',
            'access_dashboard'
        );
    }
}
