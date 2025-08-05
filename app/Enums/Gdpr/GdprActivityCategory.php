<?php

namespace App\Enums\Gdpr;

use Illuminate\Support\Traits\EnumTrait;

/**
 * @package App\Enums\Gdpr
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI MVP - Activity Category Enum + Biography Support)
 * @os2-pillars Explicit,Coherent,Simple,Secure
 *
 * Enum OS2.0-compliant per la categorizzazione delle attività della piattaforma.
 * Ogni categoria rappresenta un ambito semantico distinto, utile per audit, security e tracciabilità.
 * v1.1: Aggiunto supporto completo per sistema Biography e gestione contenuti.
 */
enum GdprActivityCategory: string
{
    /** Login/logout activities */
    case AUTHENTICATION = 'authentication';

    /** Authentication-related activities */
    case AUTHENTICATION_LOGIN = 'authentication_login';

    /** Logout activities */
    case AUTHENTICATION_LOGOUT = 'authentication_logout';

    /** User Registration Activities */
    case REGISTRATION = 'registration';

    /** GDPR-related actions */
    case GDPR_ACTIONS = 'gdpr_actions';

    /** Data viewing/downloading */
    case DATA_ACCESS = 'data_access';

    /** Data deletion and erasure */
    case DATA_DELETION = 'data_deletion';

    /** Content creation activities (biographies, posts, etc.) */
    case CONTENT_CREATION = 'content_creation';

    /** Content modification and updates */
    case CONTENT_MODIFICATION = 'content_modification';

    /** General platform interaction */
    case PLATFORM_USAGE = 'platform_usage';

    /** System interactions and UI operations */
    case SYSTEM_INTERACTION = 'system_interaction';

    /** Security-related activities */
    case SECURITY_EVENTS = 'security_events';

    /** Blockchain/NFT activities */
    case BLOCKCHAIN_ACTIVITY = 'blockchain_activity';

    /** File and media operations */
    case MEDIA_MANAGEMENT = 'media_management';

    /** Privacy and consent operations */
    case PRIVACY_MANAGEMENT = 'privacy_management';

    /** Personal data updates */   
    case PERSONAL_DATA_UPDATE = 'personal_data_update';

    /**
     * Ritorna la descrizione umana della categoria, OS2.0 style.
     */
    public function label(): string
    {
        return match($this) {
            self::AUTHENTICATION => 'User Authentication (Login/Logout)',
            self::AUTHENTICATION_LOGIN => 'User Login Activities',
            self::AUTHENTICATION_LOGOUT => 'User Logout Activities',
            self::REGISTRATION => 'User Registration Activities',
            self::GDPR_ACTIONS => 'GDPR Compliance and Privacy Actions',
            self::DATA_ACCESS => 'Data Access, Viewing or Downloading',
            self::DATA_DELETION => 'Data Deletion and Erasure Operations',
            self::CONTENT_CREATION => 'Content Creation (Biographies, Posts, Articles)',
            self::CONTENT_MODIFICATION => 'Content Modification and Updates',
            self::PLATFORM_USAGE => 'General Platform Usage/Interaction',
            self::SYSTEM_INTERACTION => 'System Interactions and UI Operations',
            self::SECURITY_EVENTS => 'Security-related Events or Incidents',
            self::BLOCKCHAIN_ACTIVITY => 'Blockchain or NFT Related Activity',
            self::MEDIA_MANAGEMENT => 'File Upload, Media and Asset Management',
            self::PRIVACY_MANAGEMENT => 'Privacy Settings and Consent Management',
            self::PERSONAL_DATA_UPDATE => 'Personal Data Updates and Modifications',    
        };
    }

    /**
     * @Oracode Method: Get Privacy Level for Category
     * 🎯 Purpose: Return appropriate privacy level for audit retention
     * 📊 Logic: Higher sensitivity = longer retention + higher security
     */
    public function privacyLevel(): string
    {
        return match($this) {
            self::AUTHENTICATION,
            self::AUTHENTICATION_LOGIN,
            self::AUTHENTICATION_LOGOUT,
            self::SECURITY_EVENTS => 'high',

            self::GDPR_ACTIONS,
            self::DATA_DELETION,
            self::PRIVACY_MANAGEMENT => 'critical',

            self::CONTENT_CREATION,
            self::CONTENT_MODIFICATION,
            self::DATA_ACCESS => 'standard',

            self::REGISTRATION,
            self::BLOCKCHAIN_ACTIVITY,
            self::MEDIA_MANAGEMENT => 'standard',

            self::PLATFORM_USAGE,
            self::SYSTEM_INTERACTION => 'standard'
        };
    }

    /**
     * @Oracode Method: Get Retention Period in Days
     * 🎯 Purpose: Return retention period based on category sensitivity
     * 📊 Logic: Critical = 7 years, High = 3 years, Standard = 2 years
     */
    public function retentionDays(): int
    {
        return match($this->privacyLevel()) {
            'critical' => 2555, // 7 years (GDPR compliance)
            'high' => 1095,     // 3 years (security events)
            'standard' => 730,  // 2 years (general activities)
            default => 365      // 1 year fallback
        };
    }

    /**
     * @Oracode Method: Check if Category Requires GDPR Audit
     * 🎯 Purpose: Determine if activity needs full GDPR audit logging
     * 🛡️ GDPR: Critical categories get full audit, others get user activity only
     */
    public function requiresGdprAudit(): bool
    {
        return match($this) {
            self::GDPR_ACTIONS,
            self::DATA_DELETION,
            self::CONTENT_CREATION,
            self::CONTENT_MODIFICATION,
            self::PRIVACY_MANAGEMENT,
            self::MEDIA_MANAGEMENT => true,
            default => false
        };
    }
}