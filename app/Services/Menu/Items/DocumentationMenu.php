<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Documentation
 * 🎯 Purpose: Access to platform documentation
 */
class DocumentationMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.documentation',
            'documentation.index',
            'book',
            'view_documentation'
        );
    }
}
