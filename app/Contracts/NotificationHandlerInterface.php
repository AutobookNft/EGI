<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use App\Contracts\NotificationDataInterface;

/**
 * Interface NotificationHandlerInterface
 *
 * Interfaccia per i gestori di notifiche.
 * Definisce il contratto che tutti gli handler di notifiche devono implementare.
 *
 * @package App\Contracts
 */
interface NotificationHandlerInterface
{
    /**
     * Gestisce l'invio di una notifica a un utente.
     *
     * @param User                      $message_to   L'utente destinatario della notifica
     * @param NotificationDataInterface $notification I dati della notifica da inviare
     *
     * @throws \Exception Se si verifica un errore durante l'invio della notifica
     */
    public function handle(User $message_to, NotificationDataInterface $notification): void;
}
