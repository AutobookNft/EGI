<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class NewCollectionMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('New Collection', 'collections.create', 'new_collection', 'create_collection');
    }
}
