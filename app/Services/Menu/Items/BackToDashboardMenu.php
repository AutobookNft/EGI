<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class BackToDashboardMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct('Back to Dashboard', 'dashboard', 'back', 'view_dashboard');
    }
}
