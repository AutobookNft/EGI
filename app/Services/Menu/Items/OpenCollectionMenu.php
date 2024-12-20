<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class OpenCollectionMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('Open Collection', 'collections.open', 'open', 'view_collection');
    }
}
