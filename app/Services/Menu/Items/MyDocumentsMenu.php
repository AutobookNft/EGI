<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class MyDocumentsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            'menu.my_documents',
            'user.documents.index',
            'document-text',
            'manage_own_documents'
        );
    }
}
