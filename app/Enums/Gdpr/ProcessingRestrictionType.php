<?php

namespace App\Enums\Gdpr;

/**
 * @oracode Enum for processing restriction types
 * @oracode-dimension technical
 * @value-flow Categorizes different types of processing limitations
 * @transparency-level Restriction types are clearly defined
 */
enum ProcessingRestrictionType: string
{
    case MARKETING = 'marketing';
    case PROFILING = 'profiling';
    case ANALYTICS = 'analytics';
    case THIRD_PARTY = 'third_party';
    case AUTOMATED_DECISIONS = 'automated_decisions';
    case DATA_SHARING = 'data_sharing';
    case ALL = 'all';

    public function label(): string
    {
        return match($this) {
            self::MARKETING => __('gdpr.restriction_types.marketing'),
            self::PROFILING => __('gdpr.restriction_types.profiling'),
            self::ANALYTICS => __('gdpr.restriction_types.analytics'),
            self::THIRD_PARTY => __('gdpr.restriction_types.third_party'),
            self::AUTOMATED_DECISIONS => __('gdpr.restriction_types.automated_decisions'),
            self::DATA_SHARING => __('gdpr.restriction_types.data_sharing'),
            self::ALL => __('gdpr.restriction_types.all'),
        };
    }

    public function description(): string
    {
        return match($this) {
            self::MARKETING => __('gdpr.restriction_descriptions.marketing'),
            self::PROFILING => __('gdpr.restriction_descriptions.profiling'),
            self::ANALYTICS => __('gdpr.restriction_descriptions.analytics'),
            self::THIRD_PARTY => __('gdpr.restriction_descriptions.third_party'),
            self::AUTOMATED_DECISIONS => __('gdpr.restriction_descriptions.automated_decisions'),
            self::DATA_SHARING => __('gdpr.restriction_descriptions.data_sharing'),
            self::ALL => __('gdpr.restriction_descriptions.all'),
        };
    }
}
