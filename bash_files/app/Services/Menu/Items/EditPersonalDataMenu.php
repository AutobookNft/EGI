<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: EditPersonalDataMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.edit_personal_data
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class EditPersonalDataMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.edit_personal_data',
            'profile.edit',
            'user-edit',
            'edit_personal_data'
        );
    }
}
