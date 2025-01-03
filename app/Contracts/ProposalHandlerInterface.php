<?php

namespace App\Contracts;

interface ProposalHandlerInterface
{
    /**
     * Gestisce il declino di una proposta.
     *
     * @param int $proposalId
     * @param string $reason
     * @return void
     */
    public function decline(int $proposalId, string $reason): void;
}
