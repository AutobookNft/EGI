<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: PrivacyPolicyMenu
 * 🎯 Purpose: GDPR compliance menu item for menu.privacy_policy
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class PrivacyPolicyMenu extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            'menu.privacy_policy',
            'legal.privacy',
            'file-text',
            'view_privacy_policy'
        );
    }
}
