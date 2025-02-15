<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Wallets;

/**
 * DTO per la gestione degli errori nelle operazioni sui wallet
 */
class WalletError
{
    public function __construct(
        public readonly string $message,
        public readonly string $code,
        public readonly array $context = []
    ) {}

    public static function quotaInsufficient(float $available, float $required): self
    {
        return new self(
            message: __('collection.wallet.insufficient_quota'),
            code: 'QUOTA_INSUFFICIENT',
            context: [
                'available' => $available,
                'required' => $required
            ]
        );
    }

    public static function walletNotFound(string $wallet): self
    {
        return new self(
            message: __('collection.wallet.wallet_not_found'),
            code: 'WALLET_NOT_FOUND',
            context: ['wallet' => $wallet]
        );
    }
}
