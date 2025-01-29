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
 * - REQUEST: Richiesta di invito.
 * - PROPOSED: Proposta di invito.
 *
 * Funzionalità principali:
 * - Conversione da stringhe del database in valori dell'enum.
 * - Tipizzazione forte per garantire la validità degli stati.
 * - Utilizzo:
 *    $status = InvitationStatus::PENDING;
 *    if ($status === InvitationStatus::PENDING) {
 *       // Esegui azioni specifiche per lo stato 'pending'.
 *    }
 *
 *    $invitation = NotificationPayloadInvitation::find(1);
 *    $invitation['view'] = InvitationStatus::PENDING->value;
 *    $invitation->save();
 *
 * @package App\Enums
 */
enum WalletStatus: string
{
    // In attesa
    case PENDING = 'pending';

    case CREATION = 'creation';
    // Accettato
    case ACCEPTED = 'accepted';
    case UPDATE = 'update';
    // Richiesta
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
            'creation' => self::CREATION,
            'accepted' => self::ACCEPTED,
            'update' => self::UPDATE,
            'rejected' => self::REJECTED,

            default => throw new \ValueError("Status '$value' non valido") // Lancia un'eccezione per valori non riconosciuti.
        };
    }
}
