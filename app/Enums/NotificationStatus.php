<?php

namespace App\Enums;

/**
 * Enum InvitationStatus
 *
 * Questa enumerazione rappresenta i possibili stati di un invito.
 * Fornisce una gestione tipizzata per i valori di stato e un metodo
 * per la conversione dei valori dal database in istanze dell'enum.
 *
 * Stati disponibili:
 * - PENDING: Invito in attesa di una risposta.
 * - ACCEPTED: Invito accettato.
 * - REJECTED: Invito rifiutato.
 *
 * Funzionalità principali:
 * - Conversione da stringhe del database in valori dell'enum.
 * - Tipizzazione forte per garantire la validità degli stati.
 *
 * @package App\Enums
 */
enum NotificationStatus: string
{

    // Notifica inviata.
    case PROPOSED = 'proposed';

    // risposta con accettazione
    case ACCEPTED = 'accepted';

    // risposta con rifiuto
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
            'accepted' => self::ACCEPTED,
            'rejected' => self::REJECTED,
            'proposed' => self::PROPOSED,
            default => throw new \ValueError("Status '$value' non valido") // Lancia un'eccezione per valori non riconosciuti.
        };
    }
}
