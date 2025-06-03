<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Open Collection
 * 🎯 Purpose: Access to collection opening
 */
class OpenCollectionMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.open_collection',
            'collections.open',
            'open_collection',
            'open_collection'
        );
    }
}
