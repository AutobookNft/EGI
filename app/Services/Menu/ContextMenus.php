<?php

namespace App\Services\Menu;

use App\Services\Menu\Items\PermissionsRolesMenu;
use App\Services\Menu\Items\AssignRolesMenu;
use App\Services\Menu\Items\AssignPermissionsMenu;
use App\Services\Menu\Items\OpenCollectionMenu;
use App\Services\Menu\Items\NewCollectionMenu;

class ContextMenus
{
    public static function getMenusForContext(string $context): array
    {
        $menus = [];

        switch ($context) {
            case 'dashboard':
                $adminMenu = new MenuGroup('Admin Tools', '<i class="fas fa-tools"></i>', [
                    new PermissionsRolesMenu(),
                    new AssignRolesMenu(),
                    new AssignPermissionsMenu(),
                ]);
                $menus[] = $adminMenu;

                $collectionsMenu = new MenuGroup('Collections', '<i class="fas fa-folder-open"></i>', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                ]);
                $menus[] = $collectionsMenu;

                break;

            case 'collections':
                $collectionsMenu = new MenuGroup('Collections', '<i class="fas fa-folder-open"></i>', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                ]);
                $menus[] = $collectionsMenu;
                break;

            default:
                $defaultMenu = new MenuGroup('General', '<i class="fas fa-cogs"></i>', []);
                $menus[] = $defaultMenu;
                break;
        }

        return $menus;
    }
}
