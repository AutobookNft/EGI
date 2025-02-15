<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Wallets;

use App\Contracts\NotificationDataInterface;

class WalletNotificationData implements NotificationDataInterface
{
    public function __construct(
        private readonly string $model_type,
        private readonly int $model_id,
        private readonly string $view,
        private readonly ?string $prev_id = null, // UUID
        private readonly int $sender_id,
        private readonly string $message,
        private readonly ?string $reason = '', // Motivo del rifiuto
        private readonly string $sender_name,     // Nome di chi invia
        private readonly string $sender_email,    // Email di chi invia
        private readonly ?string $collection_name,
        private readonly string $status,
        private readonly ?float $old_royalty_mint = null,
        private readonly ?float $old_royalty_rebind = null,

    ) {}

    // Implementazione dell'interfaccia
    public function getSenderName(): string
    {
        return $this->sender_name;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function getView(): string
    {
        return $this->view;
    }
    public function getPrevId(): ?string
    {
        return $this->prev_id;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getSenderEmail(): ?string
    {
        return $this->sender_email;
    }
    public function getCollectionName(): ?string
    {
        return $this->collection_name;
    }
    public function getModelType(): string
    {
        return $this->model_type;
    }
    public function getModelId(): int
    {
        return $this->model_id;
    }
    public function getSenderId(): ?int
    {
        return $this->sender_id;
    }
    public function getOldRoyaltyMint(): ?float
    {
        return $this->old_royalty_mint;
    }
    public function getOldRoyaltyRebind(): ?float
    {
        return $this->old_royalty_rebind;
    }


    // Factory method per l'accettazione
    public static function forAcceptance(
        string $model_type,
        int $model_id,
        string $view,
        string $prev_id, // UUID
        int $sender_id,
        string $message,
        string $sender,
        ?string $sender_email,
        ?string $collection_name,
        string $status,
        ?float $Old_royalty_mint,
        ?float $Old_royalty_rebind

    ): self {
        return new self(
            model_type: $model_type,
            model_id: $model_id,
            view: $view,
            prev_id: $prev_id,
            sender_id: $sender_id,
            message: $message,
            reason: null,
            sender_name: $sender,
            sender_email: $sender_email,
            collection_name: $collection_name,
            status: $status,
            old_royalty_mint: $Old_royalty_mint,
            old_royalty_rebind: $Old_royalty_rebind
        );
    }

    // Factory method per il rifiuto
    public static function forRejection(
        string $model_type,
        int $model_id,
        string $view,
        string $prev_id,
        int $sender_id,
        string $message,
        string $reason,
        string $sender,
        ?string $sender_email,
        ?string $collection_name,
        string $status
    ): self {
        return new self(
            model_type: $model_type,
            model_id: $model_id,
            view: $view,
            prev_id: $prev_id,
            sender_id: $sender_id,
            message: $message,
            reason: $reason,
            sender_name: $sender,
            sender_email: $sender_email,
            collection_name: $collection_name,
            status: $status
        );
    }

    // Metodi per accedere ai dati per la notifica
    public function toNotificationData(): array
    {
        return [
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'view' => $this->view,
            'prev_id' => $this->prev_id,
            'sender_id' => $this->sender_id,
            'data' => [
                'message' => $this->message,
                'reason' => $this->reason,
                'sender' => $this->sender_name,
                'email' => $this->sender_email,   // Email del sender
                'collection_name' => $this->collection_name,
            ],
            'outcome' => $this->status,
        ];
    }

}
