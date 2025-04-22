<?php

namespace Ultra\UploadManager\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;      // Standard Log Facade
use Illuminate\Support\Facades\Config;   // Standard Config Facade
use Throwable;
use Exception;      // Standard Exception
use LogicException; // For internal logic/config errors

/**
 * 📜 Oracode Helper Class: EGI Utilities
 * Provides specialized utility methods for EGI-related operations within the UUM package.
 *
 * @package     Ultra\UploadManager\Helpers
 * @version     1.1.0 // Added configuration reading for locking.
 * @author      Padmin D. Curtis (Implementation based on Fabio Cherici's requirement)
 * @copyright   2024 Fabio Cherici
 * @license     MIT // Or your package license
 *
 * @purpose     Offers reusable functions specific to EGI handling, like concurrency-safe
 *              position number generation, promoting cleaner code in handlers.
 *
 * @context     Used statically by components like EgiUploadHandler. Relies on Laravel Facades
 *              (DB, Log, Config) and application configuration (`config/egi.php`).
 *
 * @signal      Methods return calculated values or throw standard Exceptions on failure.
 * @signal      Logs operations and errors via the standard `Log` facade.
 *
 * @dependency  Laravel Framework (DB, Log, Config). Configuration file `config/egi.php`.
 *              Database table `egi` with relevant columns.
 *
 * @testing     Requires mocking `DB`, `Log`, `Config` facades. Test edge cases like empty
 *              collections, concurrent access (if possible in test environment), and DB errors.
 */
class EgiHelper
{
    /**
     * 🎯 Generates the next sequential position number for an EGI within a collection.
     * Handles potential concurrency using pessimistic locking based on configuration.
     *
     * @param int $collectionId The ID of the collection.
     * @param string $logChannel Optional log channel override.
     * @return int The next available position number (starting from 1).
     *
     * @throws \LogicException If the required configuration `egi.position.use_locking` is missing or invalid.
     * @throws \Exception If a database error occurs during the query.
     *
     * @configReads egi.position.use_locking (boolean) - Determines if pessimistic locking is used.
     * @transaction Requires being called within an active DB transaction if locking is enabled.
     */
    public static function generatePositionNumber(int $collectionId, string $logChannel = 'stack'): int
    {
        $logContext = ['helper' => static::class, 'method' => 'generatePositionNumber', 'collection_id' => $collectionId];
        Log::channel($logChannel)->debug('[EgiHelper] Generating next position number.', $logContext);

        // --- Read and Validate Configuration ---
        $useLocking = Config::get('egi.position.use_locking');
        if (!is_bool($useLocking)) {
            // Log critical config error and throw LogicException
            Log::channel($logChannel)->error("[EgiHelper] Invalid configuration: 'egi.position.use_locking' must be a boolean.", array_merge($logContext, ['retrieved_value' => $useLocking]));
            throw new LogicException("Invalid configuration for 'egi.position.use_locking'. Expected boolean.");
        }
        $logContext['locking_enabled'] = $useLocking;
        // --- End Configuration Handling ---

        try {
            // Build the query for the 'egi' table targeting the specific collection
            $query = DB::table('egi')->where('collection_id', $collectionId);

            // Apply pessimistic lock ONLY if configured AND if inside a transaction
            if ($useLocking) {
                // Check if currently inside a transaction before applying lock
                if (DB::connection()->transactionLevel() > 0) {
                    $query->lockForUpdate(); // Apply lock (e.g., SELECT ... FOR UPDATE)
                    Log::channel($logChannel)->debug('[EgiHelper] Pessimistic database lock applied.', $logContext);
                } else {
                    // Log a warning if locking is configured but no transaction is active
                    Log::channel($logChannel)->warning('[EgiHelper] Locking configured but no active DB transaction detected. Lock skipped.', $logContext);
                }
            }

            // --- Execute Query ---
            // Get the maximum current 'position' value for the locked rows (or all rows if not locked)
            // Using max() aggregate function is generally safe and efficient. It returns NULL if no matching rows exist.
            $maxPosition = $query->max('position');
            // --- End Query ---

            // Calculate the next position number
            // If maxPosition is NULL (no EGIs in collection yet), start at 1. Otherwise, increment.
            $nextPosition = ($maxPosition === null) ? 1 : ((int)$maxPosition + 1);

            Log::channel($logChannel)->info('[EgiHelper] Next position number generated.', array_merge($logContext, ['max_position' => $maxPosition ?? 'none', 'next_position' => $nextPosition]));

            return $nextPosition;

        } catch (Throwable $e) {
            // Log any database query errors
            Log::channel($logChannel)->error('[EgiHelper] Database error during position number generation.', array_merge($logContext, [
                 'error' => $e->getMessage(),
                 'exception_class' => get_class($e),
            ]));
            // Re-throw a standard Exception to ensure transaction rollback in the calling handler
            throw new Exception("Database error occurred while generating position number for collection {$collectionId}: " . $e->getMessage(), 500, $e);
        }
    }
}
