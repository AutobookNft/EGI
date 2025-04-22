<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;

/**
 * DTO per la richiesta di rifiuto di un wallet
 */
class WalletExpireResponse
{
    public function __construct(
        public readonly string $notification_id, // UUIDaccettare valori
        public readonly int $proposer_id,
        public readonly int $wallet_payload_id
    ) {}

    public static function fromRequest(
        CustomDatabaseNotification $notification,
    ): self {
        if (!$notification->model instanceof NotificationPayloadWallet) {
            throw new \InvalidArgumentException('Notifica non valida per wallet');
        }

        return new self(
            notification_id: $notification->id,
            proposer_id: $notification->model->proposer_id,
            wallet_payload_id: $notification->model->id
        );
    }
}
