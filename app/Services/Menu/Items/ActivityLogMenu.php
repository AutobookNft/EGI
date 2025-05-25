<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: ActivityLogMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.activity_log
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class ActivityLogMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.activity_log',
            'gdpr.activity-log',
            'clock',
            'view_activity_log'
        );
    }
}
