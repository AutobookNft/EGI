<?php

namespace App\Traits;

use App\Models\Collection;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

trait HasCreateDefaultCollectionWallets
{
    /**
     * Genera i wallet di default per una collection.
     *
     * @param  Collection  $collection
     * @param  string  $wallet_creator
     */
    public function generateDefaultWallets(Collection $collection, string $wallet_creator, $creator_id): void
    {
        $natan_wallet_address = config('app.natan_wallet_address');
        $natan_royalty_mint = config('app.natan_royalty_mint');
        $natan_royalty_rebind = config('app.natan_royalty_rebind');
        $mediator_royalty_mint = config('app.mediator_royalty_mint');
        $mediator_royalty_rebind = config('app.mediator_royalty_rebind');
        $epp_wallet_address = config('app.epp_wallet_address');
        $epp_royalty_mint = config('app.epp_royalty_mint');
        $epp_royalty_rebind = config('app.epp_royalty_rebind');
        $creator_royalty_mint = config('app.creator_royalty_mint');
        $creator_royalty_rebind = config('app.creator_royalty_rebind');
        $natan_id = config('app.natan_id');
        $epp_id = config('app.epp_id');

        DB::transaction(function () use (
            $collection,
            $natan_wallet_address,
            $natan_royalty_mint,
            $natan_royalty_rebind,
            $mediator_royalty_mint,
            $mediator_royalty_rebind,
            $epp_wallet_address,
            $epp_royalty_mint,
            $epp_royalty_rebind,
            $wallet_creator,
            $creator_royalty_mint,
            $creator_royalty_rebind,
            $natan_id,
            $epp_id,
            $creator_id
        ) {
            // Wallet per natan
            $this->createWallet('natan', $natan_wallet_address, $natan_royalty_mint, $natan_royalty_rebind, $collection, $natan_id);

            // Wallet per il Mediator (di default uguale a natan)
            // PER IL MOMENTO NON GESTITO
            // $this->createWallet('Mediator', $natan_wallet_address, $mediator_royalty_mint, $mediator_royalty_rebind, $collection);

            // Wallet per EPP
            $this->createWallet('EPP', $epp_wallet_address, $epp_royalty_mint, $epp_royalty_rebind, $collection, $epp_id);

            // Wallet per il Creator
            $this->createWallet('Creator', $wallet_creator, $creator_royalty_mint, $creator_royalty_rebind, $collection, $creator_id);
        });
    }

    /**
     * Crea un wallet per una collection.
     *
     * @param  string  $role
     * @param  string  $address
     * @param  string  $royalty_mint
     * @param  string  $royalty_rebind
     * @param  Collection  $collection
     */
    protected function createWallet(string $role, string $address, string $royalty_mint, string $royalty_rebind, Collection $collection, $id): void
    {
        Wallet::create([
            'collection_id' => $collection->id,
            'user_id' => $id,
            'platform_role' => $role,
            'wallet' => $address,
            'royalty_mint' => $royalty_mint,
            'royalty_rebind' => $royalty_rebind,
        ]);
    }
}
