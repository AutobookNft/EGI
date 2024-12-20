<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class PermissionsRolesMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('Permissions & Roles', 'admin.roles.index', 'permissions_roles', 'manage_roles');
    }
}
