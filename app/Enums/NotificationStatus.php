<?php

namespace App\Enums;

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

    case CREATION = 'creation';
    case PENDING = 'pending';
    case PENDING_CREATE = 'pending_create';
    case PENDING_UPDATE = 'pending_update';
    case REQUEST = 'request';
    case ACCEPTED = 'accepted';
    case UPDATE = 'update';
    case REJECTED = 'rejected';

    /**
     * Converte un valore stringa del database in un'istanza dell'enum.
     *
     * @param string $value Il valore dello stato proveniente dal database.
     * @return self L'istanza dell'enum corrispondente al valore.
     * @throws \ValueError Se il valore non è valido o non mappato.
     */
    public static function fromDatabase(string $value): self
    {
        // Usa il costrutto match per mappare i valori stringa ai casi dell'enum.
        return match($value) {
            'pending' => self::PENDING,
            'pending_create' => self::PENDING_CREATE,
            'pending_update' => self::PENDING_UPDATE,
            'creation' => self::CREATION,
            'accepted' => self::ACCEPTED,
            'update' => self::UPDATE,
            'rejected' => self::REJECTED,
            'request' => self::REQUEST,
            default => throw new \ValueError("Status '$value' non valido") // Lancia un'eccezione per valori non riconosciuti.
        };
    }
}
