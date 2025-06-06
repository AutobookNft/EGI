<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class MyProfileMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            'menu.my_profile',
            'user.profile.edit',
            'user-circle',
            'edit_own_profile_data'
        );
    }
}
