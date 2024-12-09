<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\Team_wallet;
use App\Models\TeamWallet;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasCreateWallet
 */
trait HasCreateDefaultTeamWallets
{
    /**
     * Create a default wallet for the team.
     *
     * @param  string  $wallet_creator;
     */
    public function generateAssetWallet(int $team_id, string $wallet_creator): void
    {

        $wallet_frangette = config('app.frangette_wallet_address');
        $frangette_royalty_mint = config('app.frangette_royalty_mint');
        $frangette_royalty_rebind = config('app.frangette_royalty_rebind');
        $mediator_royalty_mint = config('app.mediator_royalty_mint');
        $mediator_royalty_rebind = config('app.mediator_royalty_rebind');
        $creator_royalty_mint = config('app.creator_royalty_mint');
        $creator_royalty_rebind = config('app.creator_royalty_rebind');

        DB::transaction(function () use ($team_id, $wallet_frangette, $frangette_royalty_mint, $frangette_royalty_rebind, $mediator_royalty_mint, $mediator_royalty_rebind, $wallet_creator, $creator_royalty_mint, $creator_royalty_rebind) {
            // Creazione del wallet per Frangette
            $this->createWallet('Frangette', $wallet_frangette, $frangette_royalty_mint, $frangette_royalty_rebind, $team_id);

            // Creazione del wallet per il mediator. Di default è lo stesso di Frangette
            $this->createWallet('Mediator', $wallet_frangette, $mediator_royalty_mint, $mediator_royalty_rebind, $team_id);

            // Creazione del wallet per Creator
            $this->createWallet('Creator', $wallet_creator, $creator_royalty_mint, $creator_royalty_rebind, $team_id);
        });

    }

    /**
     * Create a default wallet.
     */
    protected function createWallet(string $role, string $address, string $rm, string $rr, int $team_id): void
    {

        $newWallet = new TeamWallet;
        $newWallet->team_id = $team_id;
        $newWallet->user_role = $role;
        $newWallet->address = $address;
        $newWallet->royalty_mint = $rm;
        $newWallet->royalty_rebind = $rr;
        $newWallet->status = true;
        $newWallet->save();

    }
}
