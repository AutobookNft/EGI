<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: LimitProcessingMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.limit_processing
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class LimitProcessingMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.limit_processing',
            'gdpr.limit-processing',
            'user-minus-circle',
            'limit_data_processing'
        );
    }
}
