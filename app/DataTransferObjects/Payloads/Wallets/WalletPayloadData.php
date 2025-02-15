<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

use App\Enums\NotificationStatus;
use App\Enums\PlatformRole;
use App\Models\NotificationPayloadWallet;
use App\Models\User;

/**
 * DTO che rappresenta i dati di un payload wallet
 */
class WalletPayloadData
{
    public function __construct(
        public readonly int $collection_id,
        public readonly int $proposer_id,
        public readonly int $receiver_id,
        public readonly string $wallet,
        public readonly string $platform_role,
        public readonly float $royalty_mint,
        public readonly float $royalty_rebind,
        public readonly string $status,
        public readonly string $type
    ) {}

    public static function fromModel(NotificationPayloadWallet $model): self
    {
        return new self(
            collection_id: $model->collection_id,
            proposer_id: $model->proposer_id,
            receiver_id: $model->receiver_id,
            wallet: $model->wallet,
            platform_role: $model->platform_role,
            royalty_mint: $model->royalty_mint,
            royalty_rebind: $model->royalty_rebind,
            status: $model->status,
            type: $model->type
        );
    }
}



