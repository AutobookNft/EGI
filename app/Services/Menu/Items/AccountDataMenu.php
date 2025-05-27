<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Account Data
 * 🎯 Purpose: Access to account management
 * 🛡️ GDPR: Contains personal data management options
 */
class AccountDataMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.account_data',
            'profile.show',
            'user-cog',
            'manage_account'
        );
    }
}
