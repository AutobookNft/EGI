<?php

namespace App\Enums\Gdpr;

use Illuminate\Support\Traits\EnumTrait;

/**
 * @package App\Enums\Gdpr
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - Activity Category Enum)
 * @os2-pillars Explicit,Coherent,Simple,Secure
 *
 * Enum OS2.0-compliant per la categorizzazione delle attività della piattaforma.
 * Ogni categoria rappresenta un ambito semantico distinto, utile per audit, security e tracciabilità.
 */
enum GdprActivityCategory: string
{
    /** Login/logout activities */
    case AUTHENTICATION = 'authentication';

    /** Authentication-related activities */
    case AUTHENTICATION_LOGIN = 'authentication_login';

    /** Logout activities */
    case AUTHENTICATION_LOGOUT = 'authentication_logout';

    /** GDPR-related actions */
    case GDPR_ACTIONS = 'gdpr_actions';

    /** Data viewing/downloading */
    case DATA_ACCESS = 'data_access';

    /** General platform interaction */
    case PLATFORM_USAGE = 'platform_usage';

    /** Security-related activities */
    case SECURITY_EVENTS = 'security_events';

    /** Blockchain/NFT activities */
    case BLOCKCHAIN_ACTIVITY = 'blockchain_activity';

    case REGISTRATION = 'registration';

    /**
     * Ritorna la descrizione umana della categoria, OS2.0 style.
     */
    public function label(): string
    {
        return match($this) {
            self::AUTHENTICATION => 'User Authentication (Login/Logout)',
            self::GDPR_ACTIONS => 'GDPR Compliance and Privacy Actions',
            self::DATA_ACCESS => 'Data Access, Viewing or Downloading',
            self::PLATFORM_USAGE => 'General Platform Usage/Interaction',
            self::SECURITY_EVENTS => 'Security-related Events or Incidents',
            self::BLOCKCHAIN_ACTIVITY => 'Blockchain or NFT Related Activity',
            self::REGISTRATION => 'User Registration Activities',
            self::AUTHENTICATION_LOGIN => 'User Login Activities',
            self::AUTHENTICATION_LOGOUT => 'User Logout Activities',
        };
    }

    
}