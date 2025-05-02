<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Exceptions;

use Exception;
use Throwable;

/**
 * 📜 Oracode Exception: EgiValidationException
 *
 * Represents validation failures specific to EGI files and related data.
 * Used when an EGI file fails format, structure, content, or business rule validation.
 *
 * @package     Ultra\EgiModule\Exceptions
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Indicates that an EGI file or related data has failed validation checks. 
 *                  Provides specific validation details while maintaining semantic coupling with UEM error codes.
 *
 * @context     🧩 Used by handlers, services, and validators within the EGI module when validation rules are violated.
 *                 Designed to be caught by controllers and mapped to appropriate UEM error codes.
 *
 * @structure   🧱 Extends PHP's base Exception class.
 *                 Stores validation failures as array for detailed error reporting.
 *                 Maps to UEM error code 'INVALID_EGI_FILE'.
 *
 * @signal      🚦 Communicates that an EGI validation has failed.
 *                 Provides specific validation details via the validation errors array.
 *
 * @privacy     🛡️ May contain metadata about files but should not contain actual file content.
 *                 `@privacy-safe`: ValidationErrors should not contain PII.
 *
 * @dependency  🤝 PHP's Exception class.
 *                 UEM for error code mapping (conceptual, not direct code dependency).
 *
 * @testable    🧪 Test construction with different validation errors.
 *                 Test getValidationErrors() returns the correct array.
 *
 * @rationale   💡 Specialized exception that provides semantic meaning specific to EGI validation failures.
 *                 Facilitates precise error handling and UEM integration while maintaining separation of concerns.
 */
class EgiValidationException extends Exception
{
    /**
     * 🧱 Array of validation errors.
     * Contains field names and their associated error messages.
     *
     * @var array<string, string|array<string>>
     */
    protected array $validationErrors;

    /**
     * 🎯 Constructor: Creates a new EGI validation exception.
     *
     * @param string $message The exception message
     * @param array<string, string|array<string>> $validationErrors Specific validation errors (field => message)
     * @param int $code The exception code (defaults to 0)
     * @param Throwable|null $previous The previous throwable used for exception chaining
     */
    public function __construct(
        string $message = "EGI file validation failed",
        array $validationErrors = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validationErrors = $validationErrors;
    }

    /**
     * 📡 Get the validation errors associated with this exception.
     *
     * @return array<string, string|array<string>> The validation errors array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * 📡 Check if a specific field has validation errors.
     *
     * @param string $field The field name to check
     * @return bool True if the field has validation errors
     */
    public function hasErrorForField(string $field): bool
    {
        return isset($this->validationErrors[$field]);
    }

    /**
     * 📡 Get validation errors for a specific field.
     *
     * @param string $field The field name
     * @return string|array<string>|null The validation error(s) for the field or null if none exist
     */
    public function getErrorForField(string $field): string|array|null
    {
        return $this->validationErrors[$field] ?? null;
    }

    /**
     * 📡 Get a descriptive string representation of all validation errors.
     *
     * @return string Formatted validation errors
     */
    public function getFormattedErrors(): string
    {
        if (empty($this->validationErrors)) {
            return "No specific validation errors";
        }

        $formatted = [];
        foreach ($this->validationErrors as $field => $errors) {
            if (is_array($errors)) {
                $formatted[] = $field . ": " . implode(", ", $errors);
            } else {
                $formatted[] = $field . ": " . $errors;
            }
        }

        return implode(" | ", $formatted);
    }
}
