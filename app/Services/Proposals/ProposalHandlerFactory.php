<?php

namespace App\Services\Proposals;

use App\Contracts\ProposalHandlerInterface;
use Exception;

class ProposalHandlerFactory
{
    /**
     * Restituisce il gestore appropriato in base al contesto.
     *
     * @param string $context
     * @return ProposalHandlerInterface
     * @throws Exception
     */
    public function getHandler(string $context): ProposalHandlerInterface
    {
        $handlers = [
            'wallet' => \App\Services\Proposals\Handlers\WalletProposalHandler::class,
            'invitation' => \App\Services\Proposals\Handlers\InvitationProposalHandler::class,
            // Aggiungere altri handler qui
        ];

        if (!isset($handlers[$context])) {
            throw new Exception("Handler for context '{$context}' not found.");
        }

        return app($handlers[$context]);
    }
}
