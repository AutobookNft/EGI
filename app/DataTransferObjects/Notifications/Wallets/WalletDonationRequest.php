<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Wallets;

use App\Enums\NotificationStatus;

class WalletDonationRequest
{
    public function __construct(
        public readonly int $collection_id,
        public readonly float $donate_royalty_mint,
        public readonly float $donate_royalty_rebind,
    ) {}

    public static function fromRequest(array $data, int $proposer_id): self
    {
        return new self(
            collection_id: (int) $data['collection_id'],
            donate_royalty_mint: (float) $data['royaltyMint'],
            donate_royalty_rebind: (float) $data['royaltyRebind'],
        );
    }

    public function toArray(): array
    {
        return [
            'collection_id' => $this->collection_id,
            'donate_royalty_mint' => $this->donate_royalty_mint,
            'donate_royalty_rebind' => $this->donate_royalty_rebind,
        ];
    }
}
