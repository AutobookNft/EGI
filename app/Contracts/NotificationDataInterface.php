<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface NotificationDataInterface
 *
 * Definisce il contratto per i DTO delle notifiche
 */
interface NotificationDataInterface
{
    public function getSenderName(): string;
    public function getMessage(): string;
    public function getView(): string;
    public function getPrevId(): ?string;
    public function getStatus(): string;
    public function getSenderEmail(): ?string;
    public function getCollectionName(): ?string;
    public function getModelType(): string;
    public function getModelId(): int;
    public function getSenderId(): ?int;



}
