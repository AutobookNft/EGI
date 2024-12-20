<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class AssignRolesMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('Assign Roles', 'admin.assign.role.form', 'assign_roles', 'manage_roles');
    }
}
