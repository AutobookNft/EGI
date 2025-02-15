<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Wallets;

use App\Enums\NotificationStatus;
use App\Models\NotificationPayloadWallet;
use App\Models\User;

/**
 * DTO per la risposta a una notifica wallet
 */
class WalletNotificationResponse
{
    public function __construct(
        public readonly string $sender_name,
        public readonly int $model_id,
        public readonly string $model_type,
        public readonly string $message,
        public readonly string $view,
        public readonly string $prev_notification_id, // UUID
        public readonly string $status,
        public readonly ?string $reason = null
    ) {}

    public static function forAcceptance(
        User $sender,
        NotificationPayloadWallet $payload,
        string $prev_notification_id
    ): self {
        return new self(
            sender_name: "{$sender->name} {$sender->last_name}",
            model_id: $payload->id,
            model_type: $payload::class,
            message: __('collection.wallet.wallet_change_accepted'),
            view: 'wallets.' . NotificationStatus::ACCEPTED->value,
            prev_notification_id: $prev_notification_id,
            status: NotificationStatus::ACCEPTED->value
        );
    }

    public static function forRejection(
        User $sender,
        NotificationPayloadWallet $payload,
        string $prev_notification_id, // UUID
        string $reason
    ): self {
        return new self(
            sender_name: "{$sender->name} {$sender->last_name}",
            model_id: $payload->id,
            model_type: $payload::class,
            message: __('collection.wallet.wallet_change_rejected'),
            view: 'wallets.' . NotificationStatus::REJECTED->value,
            prev_notification_id: $prev_notification_id,
            status: NotificationStatus::REJECTED->value,
            reason: $reason
        );
    }
}
