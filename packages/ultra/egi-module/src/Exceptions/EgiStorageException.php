<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Exceptions;

use Exception;
use Throwable;

/**
 * 📜 Oracode Exception: EgiStorageException
 *
 * Represents storage failures specific to EGI file upload, persistence, or retrieval operations.
 * Used when file operations encounter filesystem, database, or cloud storage issues.
 *
 * @package     Ultra\EgiModule\Exceptions
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Indicates that an EGI file operation related to storage has failed.
 *                  Provides storage operation details while maintaining semantic coupling with UEM error codes.
 *
 * @context     🧩 Used by handlers, services, and repositories within the EGI module when storage operations fail.
 *                 Designed to be caught by controllers and mapped to appropriate UEM error codes.
 *
 * @structure   🧱 Extends PHP's base Exception class.
 *                 Stores storage operation context for detailed error reporting.
 *                 Maps to UEM error code 'ERROR_DURING_FILE_UPLOAD'.
 *
 * @signal      🚦 Communicates that an EGI storage operation has failed.
 *                 Provides specific storage context details via the storage context array.
 *
 * @privacy     🛡️ May contain file paths or storage location details.
 *                 `@privacy-safe`: StorageContext should not contain file content or sensitive keys.
 *
 * @dependency  🤝 PHP's Exception class.
 *                 UEM for error code mapping (conceptual, not direct code dependency).
 *
 * @testable    🧪 Test construction with different storage contexts.
 *                 Test getStorageContext() returns the correct array.
 *
 * @rationale   💡 Specialized exception that provides semantic meaning specific to EGI storage failures.
 *                 Facilitates precise error handling and UEM integration while maintaining separation of concerns.
 */
class EgiStorageException extends Exception
{
    /**
     * 🧱 Storage operation context.
     * Contains details about the failed storage operation.
     *
     * @var array<string, mixed>
     */
    protected array $storageContext;

    /**
     * 🧱 Storage operation type.
     * Indicates what kind of operation failed (e.g., 'upload', 'persist', 'retrieve').
     *
     * @var string
     */
    protected string $operationType;

    /**
     * 🎯 Constructor: Creates a new EGI storage exception.
     *
     * @param string $message The exception message
     * @param string $operationType The type of storage operation that failed
     * @param array<string, mixed> $storageContext Context details about the failed operation
     * @param int $code The exception code (defaults to 0)
     * @param Throwable|null $previous The previous throwable used for exception chaining
     */
    public function __construct(
        string $message = "EGI file storage operation failed",
        string $operationType = "unknown",
        array $storageContext = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->operationType = $operationType;
        $this->storageContext = $this->sanitizeContext($storageContext);
    }

    /**
     * 📡 Get the storage context associated with this exception.
     *
     * @return array<string, mixed> The storage context array
     */
    public function getStorageContext(): array
    {
        return $this->storageContext;
    }

    /**
     * 📡 Get the operation type that failed.
     *
     * @return string The operation type ('upload', 'persist', 'retrieve', etc.)
     */
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * 🛡️ Sanitize the storage context to remove sensitive information.
     * @privacy-safe Ensures no PII or credentials are stored in the exception.
     *
     * @param array<string, mixed> $context The raw storage context
     * @return array<string, mixed> The sanitized storage context
     */
    protected function sanitizeContext(array $context): array
    {
        // Sensitive keys that should be redacted
        $sensitiveKeys = [
            'password', 'secret', 'key', 'token', 'credential', 'auth',
            'content', 'body', 'data', 'binary', 'file_content'
        ];

        $sanitized = [];
        foreach ($context as $key => $value) {
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
                $sanitized[$key] = $this->sanitizeContext($value); // Recursive sanitization
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
     * 📡 Get a descriptive string representation of the storage failure.
     *
     * @return string Formatted storage error description
     */
    public function getFormattedError(): string
    {
        return sprintf(
            "EGI storage %s operation failed: %s | Context: %s",
            $this->operationType,
            $this->getMessage(),
            !empty($this->storageContext) ? json_encode($this->storageContext, JSON_UNESCAPED_SLASHES) : "No context available"
        );
    }
}
