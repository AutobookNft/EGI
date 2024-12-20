<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class AssignPermissionsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('Assign Permissions', 'admin.assign.permissions.form', 'assign_permissions', 'manage_roles');
    }
}
