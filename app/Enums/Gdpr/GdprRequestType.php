<?php

namespace App\Enums\Gdpr;

/**
 * @oracode Enum for GDPR Request Types
 * @oracode-dimension technical
 * @value-flow Centralizes request type definitions for consistency
 * @community-impact Ensures clear understanding of GDPR request categories
 * @transparency-level All request types are clearly defined and documented
 */
enum GdprRequestType: string
{
    case ERASURE = 'erasure';
    case ACCESS = 'access';
    case RECTIFICATION = 'rectification';
    case PORTABILITY = 'portability';
    case RESTRICTION = 'restriction';
    case OBJECTION = 'objection';

    /**
     * Get human-readable label for the request type
     */
    public function label(): string
    {
        return match($this) {
            self::ERASURE => __('gdpr.request_types.erasure'),
            self::ACCESS => __('gdpr.request_types.access'),
            self::RECTIFICATION => __('gdpr.request_types.rectification'),
            self::PORTABILITY => __('gdpr.request_types.portability'),
            self::RESTRICTION => __('gdpr.request_types.restriction'),
            self::OBJECTION => __('gdpr.request_types.objection'),
        };
    }

    /**
     * Get color class for UI representation
     */
    public function color(): string
    {
        return match($this) {
            self::ERASURE => 'red',
            self::ACCESS => 'blue',
            self::RECTIFICATION => 'yellow',
            self::PORTABILITY => 'green',
            self::RESTRICTION => 'orange',
            self::OBJECTION => 'purple',
        };
    }
}
