<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Collection Staff
 * 🎯 Purpose: Access to collection staff management
 */
class CollectionStaffMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.collection_staff',
            'collections.staff',
            'users',
            'manage_collection_staff'
        );
    }
}
