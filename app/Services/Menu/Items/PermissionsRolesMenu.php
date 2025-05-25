<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Permissions & Roles
 * 🎯 Purpose: Access to permission and role management
 */
class PermissionsRolesMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.permissions_roles',
            'admin.roles.index',
            'permissions_roles',
            'manage_roles'
        );
    }
}
