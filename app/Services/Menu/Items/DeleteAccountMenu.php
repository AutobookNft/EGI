<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: DeleteAccountMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.delete_account
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class DeleteAccountMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.delete_account',
            'gdpr.delete-account',
            'user-x',
            'delete_account'
        );
    }
}
