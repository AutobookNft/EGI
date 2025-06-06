<?php

namespace App\Services\Fiscal;

/**
 * @Oracode Class: Fiscal Validation Result Container
 * 🎯 Purpose: Standardized validation result with detailed feedback
 * 🧱 Core Logic: Immutable result object with success/failure and context
 * 🛡️ Privacy: Sanitized error messages, no sensitive data exposure
 * 🌍 Scale: Supports complex validation scenarios with multiple error types
 *
 * @package App\Services\Fiscal
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Fiscal Ready)
 */
class ValidationResult
{
    /**
     * Whether the validation was successful
     * @var bool
     */
    public readonly bool $isValid;

    /**
     * Error code for failed validations (e.g., 'INVALID_FORMAT', 'CHECKSUM_FAILED')
     * @var string|null
     */
    public readonly ?string $errorCode;

    /**
     * Human-readable error message for user display
     * @var string|null
     */
    public readonly ?string $errorMessage;

    /**
     * Additional context information about the validation
     * @var array<string, mixed>
     */
    public readonly array $context;

    /**
     * Properly formatted value if validation succeeded
     * @var string|null
     */
    public readonly ?string $formattedValue;

    /**
     * @Oracode Constructor: Create Validation Result Instance
     * 🎯 Purpose: Initialize immutable validation result with all details
     * 📥 Input: Validation status, optional error details and context
     * 🧱 Core Logic: Immutable object with readonly properties
     *
     * @param bool $isValid Whether the validation passed
     * @param string|null $errorCode Error code for failed validations
     * @param string|null $errorMessage Human-readable error message
     * @param array<string, mixed> $context Additional validation context
     * @param string|null $formattedValue Properly formatted value
     */
    public function __construct(
        bool $isValid,
        ?string $errorCode = null,
        ?string $errorMessage = null,
        array $context = [],
        ?string $formattedValue = null
    ) {
        $this->isValid = $isValid;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->context = $context;
        $this->formattedValue = $formattedValue;
    }

    /**
     * @Oracode Method: Create Successful Validation Result
     * 🎯 Purpose: Factory method for valid fiscal number validation
     * 📥 Input: Optional formatted value and validation context
     * 📤 Output: ValidationResult indicating success
     *
     * @param string|null $formattedValue Properly formatted fiscal number
     * @param array<string, mixed> $context Additional validation context
     * @return self ValidationResult instance indicating success
     */
    public static function valid(?string $formattedValue = null, array $context = []): self
    {
        return new self(
            isValid: true,
            formattedValue: $formattedValue,
            context: $context
        );
    }

    /**
     * @Oracode Method: Create Failed Validation Result
     * 🎯 Purpose: Factory method for invalid fiscal number with error details
     * 📥 Input: Error code, localized message, optional context
     * 📤 Output: ValidationResult with failure details for user feedback
     *
     * @param string $errorCode Machine-readable error code (e.g., 'INVALID_FORMAT')
     * @param string $errorMessage Human-readable localized error message
     * @param array<string, mixed> $context Additional error context information
     * @return self ValidationResult instance indicating failure
     */
    public static function invalid(?string $errorCode=null, string $errorMessage, array $context = []): self
    {
        return new self(
            isValid: false,
            errorCode: $errorCode,
            errorMessage: $errorMessage,
            context: $context
        );
    }

    /**
     * @Oracode Method: Check if Validation Failed
     * 🎯 Purpose: Convenience method for negative validation checks
     * 📤 Output: Boolean indicating validation failure
     *
     * @return bool True if validation failed, false if succeeded
     */
    public function failed(): bool
    {
        return !$this->isValid;
    }

    /**
     * @Oracode Method: Get Laravel Validation Error Array
     * 🎯 Purpose: Convert result to Laravel validation error format
     * 📤 Output: Array suitable for Laravel validator errors
     * 🔧 Integration: Direct integration with Form Request validation
     *
     * @return array<string, array<string>> Laravel validation error format
     */
    public function toValidationError(): array
    {
        if ($this->isValid) {
            return [];
        }

        return [$this->errorCode => [$this->errorMessage]];
    }
}
