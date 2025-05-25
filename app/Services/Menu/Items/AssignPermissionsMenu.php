<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Assign Permissions
 * 🎯 Purpose: Access to permission assignment
 */
class AssignPermissionsMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.assign_permissions',
            'admin.assign.permissions.form',
            'assign_permissions',
            'manage_roles'
        );
    }
}
