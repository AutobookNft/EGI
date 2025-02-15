<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

use App\Models\NotificationPayloadWallet;

/**
 * DTO per la validazione delle quote wallet
 */
class WalletQuotaValidation
{
    public function __construct(
        public readonly int $collection_id, // UUID
        public readonly int $proposer_id,
        public readonly int $receiver_id,
        public readonly float $required_mint_quota,
        public readonly float $required_rebind_quota,
        public readonly string $wallet,
        public readonly string $status,
        public readonly string $type,
        public readonly ?int $metadata = null,
        public readonly ?float $old_royalty_mint = null,
        public readonly ?float $old_royalty_rebind = null,

    ) {}

    public static function fromPayload(NotificationPayloadWallet $payload): self
    {
        return new self(
            collection_id: $payload->collection_id,
            proposer_id: $payload->proposer_id,
            receiver_id: $payload->receiver_id,
            required_mint_quota: $payload->royalty_mint,
            required_rebind_quota: $payload->royalty_rebind,
            wallet: $payload->wallet,
            status: $payload->status,
            type: $payload->type,
            metadata: $payload->metadata,
            old_royalty_mint: $payload->old_royalty_mint,
            old_royalty_rebind: $payload->old_royalty_rebind,
        );
    }
}
