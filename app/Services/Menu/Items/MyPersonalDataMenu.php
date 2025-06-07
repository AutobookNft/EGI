<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class MyPersonalDataMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            'menu.my_personal_data',
            'user.domains.personal-data',
            'shield-check',
            'edit_own_personal_data'
        );
    }
}
