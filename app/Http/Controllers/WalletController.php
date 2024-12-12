<?php

namespace App\Http\Controllers;

use App\Models\TeamWallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function edit(TeamWallet $wallet)
{
    $this->authorize('update', $wallet);
    return view('wallets.edit', compact('wallet'));
}
}
