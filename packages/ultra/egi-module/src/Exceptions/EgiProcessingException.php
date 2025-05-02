<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Exceptions;

use Exception;
use Throwable;

/**
 * 📜 Oracode Exception: EgiProcessingException
 *
 * Represents processing failures specific to EGI data manipulation, transformation, or business logic operations.
 * Used when operations beyond simple validation or storage fail, such as data extraction, transformation, 
 * or integration with other systems.
 *
 * @package     Ultra\EgiModule\Exceptions
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Indicates that an EGI processing operation has failed.
 *                  Provides processing details while maintaining semantic coupling with UEM error codes.
 *
 * @context     🧩 Used by handlers, services, and processors within the EGI module when processing operations fail.
 *                 Designed to be caught by controllers and mapped to appropriate UEM error codes.
 *
 * @structure   🧱 Extends PHP's base Exception class.
 *                 Stores processing stage and details for error reporting.
 *                 Maps to UEM error code 'ERROR_DURING_EGI_PROCESSING'.
 *
 * @signal      🚦 Communicates that an EGI processing operation has failed.
 *                 Provides specific processing stage and details via properties.
 *
 * @privacy     🛡️ May contain metadata about processing but should not contain file content.
 *                 `@privacy-safe`: ProcessingDetails should not contain PII or sensitive data.
 *
 * @dependency  🤝 PHP's Exception class.
 *                 UEM for error code mapping (conceptual, not direct code dependency).
 *
 * @testable    🧪 Test construction with different processing stages and details.
 *                 Test getter methods return the correct values.
 *
 * @rationale   💡 Specialized exception that provides semantic meaning specific to EGI processing failures.
 *                 Facilitates precise error handling and UEM integration while maintaining separation of concerns.
 */
class EgiProcessingException extends Exception
{
    /**
     * 🧱 Processing stage that failed.
     * Identifies where in the processing pipeline the failure occurred.
     *
     * @var string
     */
    protected string $processingStage;

    /**
     * 🧱 Processing details.
     * Contains specific information about the failed processing operation.
     *
     * @var array<string, mixed>
     */
    protected array $processingDetails;

    /**
     * 🧱 Whether the processing failure is recoverable.
     * Indicates if retry is possible or if the failure is terminal.
     *
     * @var bool
     */
    protected bool $recoverable;

    /**
     * 🎯 Constructor: Creates a new EGI processing exception.
     *
     * @param string $message The exception message
     * @param string $processingStage The pipeline stage where processing failed
     * @param array<string, mixed> $processingDetails Details about the failed operation
     * @param bool $recoverable Whether the error is potentially recoverable
     * @param int $code The exception code (defaults to 0)
     * @param Throwable|null $previous The previous throwable used for exception chaining
     */
    public function __construct(
        string $message = "EGI processing operation failed",
        string $processingStage = "unknown",
        array $processingDetails = [],
        bool $recoverable = false,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->processingStage = $processingStage;
        $this->processingDetails = $this->sanitizeDetails($processingDetails);
        $this->recoverable = $recoverable;
    }

    /**
     * 📡 Get the processing stage where the failure occurred.
     *
     * @return string The processing stage identifier
     */
    public function getProcessingStage(): string
    {
        return $this->processingStage;
    }

    /**
     * 📡 Get the details about the processing failure.
     *
     * @return array<string, mixed> The processing details array
     */
    public function getProcessingDetails(): array
    {
        return $this->processingDetails;
    }

    /**
     * 📡 Check if the processing failure is potentially recoverable.
     *
     * @return bool True if the operation might succeed on retry
     */
    public function isRecoverable(): bool
    {
        return $this->recoverable;
    }

    /**
     * 🛡️ Sanitize the processing details to remove sensitive information.
     * @privacy-safe Ensures no PII or sensitive data is stored in the exception.
     *
     * @param array<string, mixed> $details The raw processing details
     * @return array<string, mixed> The sanitized processing details
     */
    protected function sanitizeDetails(array $details): array
    {
        // Sensitive keys that should be redacted or sanitized
        $sensitiveKeys = [
            'password', 'secret', 'key', 'token', 'credential', 'auth',
            'personal', 'private', 'confidential', 'content', 'data'
        ];
        
        $sanitized = [];
        foreach ($details as $key => $value) {
            // Check if key contains any sensitive parts
            $keyLower = strtolower((string)$key);
            $shouldRedact = false;
            
            foreach ($sensitiveKeys as $sensitiveKey) {
                if (str_contains($keyLower, $sensitiveKey)) {
                    $shouldRedact = true;
                    break;
                }
            }
            
            if ($shouldRedact) {
                $sanitized[$key] = '[REDACTED]';
            } else if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeDetails($value); // Recursive sanitization
            } else if (is_scalar($value) || is_null($value)) {
                $sanitized[$key] = $value;
            } else {
                // For non-scalar, non-array values (objects, resources)
                $sanitized[$key] = '[' . (is_object($value) ? get_class($value) : gettype($value)) . ']';
            }
        }
        
        return $sanitized;
    }

    /**
     * 📡 Get a descriptive string representation of the processing failure.
     *
     * @return string Formatted processing error description
     */
    public function getFormattedError(): string
    {
        return sprintf(
            "EGI processing failed at stage '%s': %s | %s | %s",
            $this->processingStage,
            $this->getMessage(),
            $this->recoverable ? "Recoverable" : "Terminal",
            !empty($this->processingDetails) ? 
                "Details: " . json_encode($this->processingDetails, JSON_UNESCAPED_SLASHES) : 
                "No specific details available"
        );
    }

    /**
     * 📡 Get a list of potentially available recovery actions.
     * Based on the processing stage and recoverable flag.
     *
     * @return array<string> List of suggested recovery actions or empty array if none
     */
    public function getSuggestedRecoveryActions(): array
    {
        if (!$this->recoverable) {
            return [];
        }

        // Map common processing stages to suggested recovery actions
        $stageToActions = [
            'extraction' => ['retry_extraction', 'validate_file_structure'],
            'transformation' => ['retry_transformation', 'check_data_mapping'],
            'validation' => ['review_input_data', 'check_business_rules'],
            'integration' => ['retry_integration', 'check_connectivity'],
            'generation' => ['retry_generation', 'check_template'],
            'saving' => ['retry_saving', 'check_database_connection'],
        ];

        return $stageToActions[strtolower($this->processingStage)] ?? ['retry_processing'];
    }
}
