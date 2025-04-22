<?php

namespace App\Contracts;

interface InvitationNotificationDataInterface
{
    public function getCollectionId(): int;
    public function getProposerId(): ?int;
    public function getReceiverId(): int;
    public function getEmail(): ?string;
    public function getRole(): string;
    public function getStatus(): string;
    public function getMetadata(): ?array;
    public function toCollectionUser(): array;
    public function toPayloadInArray(): array;
}
