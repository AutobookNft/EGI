<?php

namespace App\Enums;

use ValueError;

/**
 * Enum InvitationStatus
 *
 * Questa enumerazione rappresenta i possibili stati delle notifiche
 * Fornisce una gestione tipizzata per i valori di stato e un metodo
 * per la conversione dei valori dal database in istanze dell'enum.
 *
 * Stati disponibili:
 * - PENDING: Invito in attesa di una risposta.
 * - ACCEPTED: Invito accettato.
 * - REJECTED: Invito rifiutato.
 * - EXPIRED: Invito scaduto.
 * - REQUEST: Invio di una richiesta.
 * - CREATION: Fase di creazione
 * - PENDING_CREATE: Attesa di accettazione per un'entità ex nova
 * - PENDING_UPDATE: Attesa di accettazione di un aggiornamento
 *
 *
 * Funzionalità principali:
 * - Conversione da stringhe del database in valori dell'enum.
 * - Tipizzazione forte per garantire la validità degli stati.
 *
 * @package App\Enums
 */
enum NotificationStatus: string
{
    case DONE = 'done';
    case CREATION = 'creation';
    case PENDING = 'pending';
    case PENDING_CREATE = 'pending_create';
    case PENDING_UPDATE = 'pending_update';
    case REQUEST = 'request';
    case ACCEPTED = 'Accepted'; // Maiuscolo per compatibilità con la UI
    case UPDATE = 'update';
    case REJECTED = 'Rejected'; // Maiuscolo per compatibilità con la UI
    case ARCHIVED = 'Archived'; // Maiuscolo per compatibilità con la UI
    case EXPIRED = 'expired';

    /**
     * Converte un valore stringa del database in un'istanza dell'enum.
     *
     * @param string $value Il valore dello stato proveniente dal database.
     * @return self L'istanza dell'enum corrispondente al valore.
     * @throws \ValueError Se il valore non è valido o non mappato.
     */
    public static function fromDatabase(string $value): self
    {

        // Usa il costrutto match per i restanti stati
        return match($value) {
            'done' => self::DONE,
            'creation' => self::CREATION,
            'Accepted' => self::ACCEPTED,
            'update' => self::UPDATE,
            'Rejected' => self::REJECTED,
            'request' => self::REQUEST,
            'Archived' => self::ARCHIVED,
            'expired' => self::EXPIRED,
            'pending_create' => self::PENDING_CREATE,
            'pending_update' => self::PENDING_UPDATE,
            'pending' => self::PENDING,
            default => throw new ValueError("Status '$value' non valido")
        };
    }

}
