<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: Wallet
 * 🎯 Purpose: Access to wallet overview
 */
class WalletMenu extends MenuItem
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'menu.wallet',
            'wallet.index',
            'wallet',
            'view_wallet'
        );
    }
}
