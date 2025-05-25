<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: ExportDataMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.export_data
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class ExportDataMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.export_data',
            'gdpr.export.index',
            'download',
            'export_personal_data'
        );
    }
}
