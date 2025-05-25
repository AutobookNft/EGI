<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: ConsentMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.consent
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class ConsentMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'gdpr.menu.gdpr_center',
            'gdpr.consent',
            'shield-check',
            'manage_consents'
        );
    }
}
