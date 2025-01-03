<?php

namespace App\Services\Proposals\Handlers;

use App\Contracts\ProposalHandlerInterface;
use App\Models\WalletChangeApproval;
use App\Notifications\ProposalDeclinedNotification;
use Exception;

class WalletProposalHandler implements ProposalHandlerInterface
{
    /**
     * Gestisce il declino di una proposta di modifica del wallet.
     *
     * @param int $proposalId
     * @param string $reason
     * @return void
     * @throws Exception
     */
    public function decline(int $proposalId, string $reason): void
    {
        $proposal = WalletChangeApproval::findOrFail($proposalId);

        if ($proposal->status !== 'pending') {
            throw new Exception("The proposal is not in a pending state.");
        }

        // Aggiorna lo stato della proposta e aggiunge la motivazione
        $proposal->update([
            'status' => 'declined',
            'rejection_reason' => $reason,
        ]);

        // Invia una notifica al proponente
        $proposal->requestedBy->notify(
            new ProposalDeclinedNotification($proposal, $reason)
        );
    }
}
