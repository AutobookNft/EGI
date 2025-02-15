<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Wallets;

use App\Enums\NotificationStatus;
use App\Models\NotificationPayloadWallet;
use App\Models\User;

/**
 * DTO per la creazione di una notifica wallet
 */
class WalletNotificationCreate
{
    public function __construct(
        public readonly string $sender_name,
        public readonly int $model_id,
        public readonly string $model_type,
        public readonly string $message,
        public readonly string $view,
        public readonly int $sender_id,
        public readonly int $notifiable_id,
        public readonly string $status = NotificationStatus::PENDING_CREATE->value
    ) {}

    public static function forCreation(
        User $sender,
        NotificationPayloadWallet $payload,
        string $message,
        string $view
    ): self {
        return new self(
            sender_name: "{$sender->name} {$sender->last_name}",
            model_id: $payload->id,
            model_type: $payload::class,
            message: $message,
            view: $view,
            sender_id: $sender->id,
            notifiable_id: $payload->receiver_id
        );
    }
}

