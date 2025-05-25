<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Assign Roles
 * 🎯 Purpose: Access to role assignment
 */
class AssignRolesMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.assign_roles',
            'admin.assign.role.form',
            'assign_roles',
            'manage_roles'
        );
    }
}
