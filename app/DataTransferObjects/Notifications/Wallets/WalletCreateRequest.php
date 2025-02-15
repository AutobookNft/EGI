<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Wallets;

use App\Enums\NotificationStatus;

class WalletCreateRequest
{
    public function __construct(
        public readonly string $collection_id, // UUID
        public readonly int $receiver_id,
        public readonly int $proposer_id,
        public readonly string $wallet,
        public readonly float $royalty_mint,
        public readonly float $royalty_rebind,
        public readonly ?float $old_royalty_mint = null,
        public readonly ?float $old_royalty_rebind = null,

    ) {}

    public static function fromRequest(array $data, int $proposer_id): self
    {
        return new self(
            collection_id: $data['collection_id'],
            receiver_id: (int) $data['receiver_id'],
            proposer_id: (int) $proposer_id,
            wallet: $data['wallet'],
            royalty_mint: (float) $data['royaltyMint'],
            royalty_rebind: (float) $data['royaltyRebind'],
            old_royalty_mint: $data['old_royalty_mint'] ?? null,
            old_royalty_rebind: $data['old_royalty_rebind'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'collection_id' => $this->collection_id,
            'receiver_id' => $this->receiver_id,
            'proposer_id' => $this->proposer_id,
            'wallet' => $this->wallet,
            'royalty_mint' => $this->royalty_mint,
            'royalty_rebind' => $this->royalty_rebind,
            'old_royalty_mint' => $this->old_royalty_mint,
            'old_royalty_rebind' => $this->old_royalty_rebind,
        ];
    }
}
