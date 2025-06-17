<?php
declare(strict_types=1);

namespace App\DataTransferObjects\Gdpr;

/**
 * @Oracode DTO: Consent Type Definition with Runtime Localization
 * 🎯 Purpose: Type-safe consent type with dynamic translation support
 * 🛡️ Privacy: GDPR-compliant consent type definitions
 * 🧱 Core Logic: Runtime localization via Laravel translation system
 */
class ConsentTypeDto
{
    public function __construct(
        public readonly string $key,
        public readonly string $category,
        public readonly string $legalBasis,
        public readonly bool $required,
        public readonly bool $defaultValue,
        public readonly bool $canWithdraw
    ) {}

    /**
     * Get localized name for this consent type
     */
    public function getName(): string
    {
        return __("gdpr.consent.{$this->key}.label");
    }

    /**
     * Get localized description for this consent type
     */
    public function getDescription(): string
    {
        return __("gdpr.consent.{$this->key}.description");
    }

    /**
     * Convert to array format (for backward compatibility)
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'category' => $this->category,
            'legal_basis' => $this->legalBasis,
            'required' => $this->required,
            'default_value' => $this->defaultValue,
            'can_withdraw' => $this->canWithdraw,
        ];
    }
}
