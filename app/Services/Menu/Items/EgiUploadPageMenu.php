<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class EgiUploadPageMenu extends MenuItem
{

    public function __construct()
    {
        parent::__construct('Upload EGI', 'egi.upload.page', 'egi', 'manage_egi');
    }

}
