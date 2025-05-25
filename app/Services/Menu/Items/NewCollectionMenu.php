<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: New Collection
 * 🎯 Purpose: Access to collection creation
 */
class NewCollectionMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.new_collection',
            'collections.create',
            'new_collection',
            'create_collection'
        );
    }
}
