<?php

namespace App\Enums;

/**
 *
 *
 * @package App\Enums
 */
enum PlatformRole: string
{

    case EPP = 'EPP';
    case NATAN = 'Natan';
    case CREATOR = 'Creator';
    case STAFF_MEMBER = 'staff_member';

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
            'EPP' => self::EPP,
            'Natan' => self::NATAN,
            'Creator' => self::CREATOR,
            'staff_member' => self::STAFF_MEMBER,


            default => throw new \ValueError("Platform role '$value' non valido") // Lancia un'eccezione per valori non riconosciuti.
        };
    }
}
