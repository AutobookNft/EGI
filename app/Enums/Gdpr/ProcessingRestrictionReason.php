<?php

namespace App\Enums\Gdpr;

/**
 * @oracode Enum for processing restriction reasons
 * @oracode-dimension technical
 * @value-flow Legal basis for requesting processing restrictions
 * @transparency-level GDPR-compliant reason tracking
 */
enum ProcessingRestrictionReason: string
{
    case ACCURACY_DISPUTE = 'accuracy_dispute';
    case UNLAWFUL_PROCESSING = 'unlawful_processing';
    case LEGAL_CLAIMS = 'legal_claims';
    case PUBLIC_INTEREST = 'public_interest';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::ACCURACY_DISPUTE => __('gdpr.reasons.accuracy_dispute'),
            self::UNLAWFUL_PROCESSING => __('gdpr.reasons.unlawful_processing'),
            self::LEGAL_CLAIMS => __('gdpr.reasons.legal_claims'),
            self::PUBLIC_INTEREST => __('gdpr.reasons.public_interest'),
            self::OTHER => __('gdpr.reasons.other'),
        };
    }
}
