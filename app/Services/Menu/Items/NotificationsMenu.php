<?php
// app/Services/Menu/Items/NotificationsMenu.php
namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Notifications
 * 🎯 Purpose: Access to user notifications center
 *
 * @package App\Services\Menu\Items
 */
class NotificationsMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.notifications',
            'notifications.index',
            'bell',
            'view_notifications'
        );
    }
}
