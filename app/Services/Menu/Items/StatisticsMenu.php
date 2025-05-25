<?php
// app/Services/Menu/Items/StatisticsMenu.php
namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Statistics
 * 🎯 Purpose: Provides access to platform statistics
 *
 * @package App\Services\Menu\Items
 */
class StatisticsMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.statistics', // Chiave di traduzione
            'statistics.index',
            'chart-bar', // Nome dell'icona nell'IconRepository
            'view_statistics'
        );
    }
}
