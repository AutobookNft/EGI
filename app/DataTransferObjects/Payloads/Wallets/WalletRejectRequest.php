<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;

/**
 * DTO per la richiesta di rifiuto di un wallet
 */
class WalletRejectRequest
{
    public function __construct(
        public readonly string $notification_id, // UUID
        public readonly string $reason,
        public readonly int $proposer_id,
        public readonly int $wallet_payload_id
    ) {}

    public static function fromRequest(
        CustomDatabaseNotification $notification,
        string $reason
    ): self {
        if (!$notification->model instanceof NotificationPayloadWallet) {
            throw new \InvalidArgumentException('Notifica non valida per wallet');
        }

        return new self(
            notification_id: $notification->id,
            reason: $reason,
            proposer_id: $notification->model->proposer_id,
            wallet_payload_id: $notification->model->id
        );
    }
}
