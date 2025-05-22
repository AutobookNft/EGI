<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: BreachReportMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.breach_report
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class BreachReportMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.breach_report',
            'gdpr.breach.index',
            'alert-triangle',
            'view_breach_reports'
        );
    }
}
