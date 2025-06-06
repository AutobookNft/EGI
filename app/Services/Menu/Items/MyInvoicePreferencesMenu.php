<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

class MyInvoicePreferencesMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            'menu.my_invoice_preferences',
            'user.invoice-preferences.edit',
            'receipt-tax',
            'manage_own_invoice_preferences'
        );
    }
}
