<?php

namespace App\Enums\PaymentDistribution;

/**
 * @Oracode Enum: Payment Distribution Status
 * 🎯 Purpose: Type-safe status tracking for payment distributions
 * 🛡️ Privacy: Status tracking with audit trail capability
 * 🧱 Core Logic: Distribution lifecycle management
 *
 * @package App\Enums\PaymentDistribution
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 */
enum DistributionStatusEnum: string {
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case CONFIRMED = 'confirmed';
    case FAILED = 'failed';

    /**
     * Get the display name for the status
     * @return string
     */
    public function getDisplayName(): string {
        return match ($this) {
            self::PENDING => __('payment_distribution.status.pending'),
            self::PROCESSED => __('payment_distribution.status.processed'),
            self::CONFIRMED => __('payment_distribution.status.confirmed'),
            self::FAILED => __('payment_distribution.status.failed'),
        };
    }

    /**
     * Get the description for the status
     * @return string
     */
    public function getDescription(): string {
        return match ($this) {
            self::PENDING => __('payment_distribution.status_desc.pending'),
            self::PROCESSED => __('payment_distribution.status_desc.processed'),
            self::CONFIRMED => __('payment_distribution.status_desc.confirmed'),
            self::FAILED => __('payment_distribution.status_desc.failed'),
        };
    }

    /**
     * Get the color class for UI display
     * @return string
     */
    public function getColorClass(): string {
        return match ($this) {
            self::PENDING => 'text-yellow-600 bg-yellow-100',
            self::PROCESSED => 'text-blue-600 bg-blue-100',
            self::CONFIRMED => 'text-green-600 bg-green-100',
            self::FAILED => 'text-red-600 bg-red-100',
        };
    }

    /**
     * Get all statuses as array for selects
     * @return array<string, string>
     */
    public static function getOptions(): array {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName();
        }
        return $options;
    }

    /**
     * Get statuses that indicate success
     * @return array<DistributionStatusEnum>
     */
    public static function getSuccessStatuses(): array {
        return [self::PROCESSED, self::CONFIRMED];
    }

    /**
     * Check if this status indicates success
     * @return bool
     */
    public function isSuccess(): bool {
        return in_array($this, self::getSuccessStatuses());
    }

    /**
     * Get statuses that indicate pending/in-progress
     * @return array<DistributionStatusEnum>
     */
    public static function getPendingStatuses(): array {
        return [self::PENDING];
    }

    /**
     * Check if this status indicates pending
     * @return bool
     */
    public function isPending(): bool {
        return $this === self::PENDING;
    }

    /**
     * Get statuses that indicate failure
     * @return array<DistributionStatusEnum>
     */
    public static function getFailureStatuses(): array {
        return [self::FAILED];
    }

    /**
     * Check if this status indicates failure
     * @return bool
     */
    public function isFailure(): bool {
        return $this === self::FAILED;
    }

    /**
     * Check if this status is final (no further processing)
     * @return bool
     */
    public function isFinal(): bool {
        return in_array($this, [self::CONFIRMED, self::FAILED]);
    }
}
