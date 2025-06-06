<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class MyOrganizationMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            'menu.my_organization',
            'user.organization.edit',
            'building-office-2',
            'edit_own_organization_data'
        );
    }
}
