<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: New Collection - OS1 Modal Enabled
 * 🎯 Purpose: Access to collection creation via instant modal
 *
 * @seo-purpose Quick collection creation for FlorenceEGI creators
 * @accessibility-trait Triggers accessible modal dialog
 * @modal-action open-create-collection-modal
 *
 * @version 2.0 - OS1 Modal Integration
 */
class NewCollectionMenu extends MenuItem
{
    /**
     * Constructor - OS1 Enhanced with Modal Action
     *
     * @oracular-purpose Validates modal integration maintains UX consistency
     */
    public function __construct()
    {
        parent::__construct(
            'menu.new_collection',
            '#', // Modal actions use # as route
            'new_collection',
            'create_collection',
            null, // No children
            'open-create-collection-modal' // Modal action trigger
        );
    }
}
