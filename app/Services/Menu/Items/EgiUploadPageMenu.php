<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Upload EGI
 * 🎯 Purpose: Access to EGI upload interface
 */
class EgiUploadPageMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.upload_egi',
            'egi.upload.page',
            'egi',
            'manage_EGI'
        );
    }
}
