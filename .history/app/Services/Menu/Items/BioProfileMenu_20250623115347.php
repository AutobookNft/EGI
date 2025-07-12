<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Bio Profile
 * 🎯 Purpose: Access to user profile and bio management
 * 🛡️ GDPR: Contains personal data management options
 */
class BioProfileMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.bio_profile',
            'personal-data.bio',
            'user-edit',
            'manage_profile'
        );
    }
}
