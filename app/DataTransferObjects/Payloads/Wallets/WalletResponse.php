<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

/**
 * DTO per la risposta alle operazioni sui wallet
 */
class WalletResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $notification_id, // UUID
        public readonly string $status,
        public readonly ?string $message = null
    ) {}

    public static function success(
        string $notification_id,
        string $status
    ): self {
        return new self(
            success: true,
            notification_id: $notification_id,
            status: $status
        );
    }

    public static function error(
        string $notification_id,
        string $message
    ): self {
        return new self(
            success: false,
            notification_id: $notification_id,
            status: 'error',
            message: $message
        );
    }

    /**
     * Converte il DTO in un array per la risposta JSON
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'notification' => [
                'id' => $this->notification_id,
                'status' => $this->status,
                'message' => $this->message
            ]
        ];
    }
}
