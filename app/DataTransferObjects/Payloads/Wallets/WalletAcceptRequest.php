<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

use App\Models\{
    CustomDatabaseNotification,
    NotificationPayloadWallet
};
use Illuminate\Support\Facades\Log;

/**
 * DTO per la richiesta di accettazione di un wallet
 */
class WalletAcceptRequest
{
    public function __construct(
        public readonly string $notification_id, // UUID
        public readonly int $collection_id,
        public readonly int $wallet_payload_id,
        public readonly int $receiver_id
    ) {}

    public static function fromNotification(CustomDatabaseNotification $notification): self
    {

        Log::channel('florenceegi')->info('WalletAcceptRequest:fromNotification', [
            'notification' => $notification
        ]);

        if (!$notification->model instanceof NotificationPayloadWallet) {
            throw new \InvalidArgumentException('Notifica non valida per wallet');
        }

        return new self(
            notification_id: $notification->id,
            collection_id: $notification->model->collection_id,
            wallet_payload_id: $notification->model->id,
            receiver_id: $notification->model->receiver_id
        );
    }
}


