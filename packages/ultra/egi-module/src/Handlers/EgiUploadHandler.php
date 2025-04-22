<?php

// Namespace corretto come richiesto
namespace Ultra\UploadManager\Handlers;

// Laravel & PHP Dependencies
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;      // Standard Log Facade
use Illuminate\Support\Facades\Storage;  // Standard Storage Facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;     // Only if needed for other hashing
use Illuminate\Support\Facades\Config;   // Standard Config Facade
use Illuminate\Support\Str;
use Throwable;
use Exception;      // Standard Exception
use LogicException; // For setup/config errors

// Application Specific Dependencies (Assume available in the consuming app)
use App\Models\Collection;
use App\Models\Egi;
use App\Models\Wallet;
use App\Models\User;

// UUM Package Dependencies
use Ultra\UploadManager\Helpers\EgiHelper; // Use Helper from this package
use Ultra\UploadManager\Traits\HasValidation; // Use Trait from this package
use Ultra\UploadManager\Traits\HasUtilitys; // Use Trait from this package

/**
 * 📜 Oracode Handler Class: EGI Image Upload
 * Handles the backend logic for uploading EGI images, including default collection management.
 * Designed to be part of the UltraUploadManager package.
 * Adheres to Oracode v1.5 documentation standards.
 *
 * @package     Ultra\UploadManager\Handlers
 * @version     1.2.0 // Corrected config reading, adjusted wallet creation, cache key.
 * @author      Fabio Cherici (Logic) & Padmin D. Curtis (Implementation)
 * @copyright   2024 Fabio Cherici
 * @license     MIT // Or your package license
 *
 * @purpose     Manages the EGI image upload process: validation, default collection/wallet handling,
 *              EGI record creation, multi-disk storage, cache invalidation, and response generation.
 *              References `config/egi.php` for specific settings. Explicitly avoids UCM/ULM for MVP.
 *
 * @context     Instantiated by a controller within the consuming application. Uses standard Laravel
 *              Facades (Log, Storage, Auth, Cache, DB, Config), application Models (User, Collection,
 *              Egi, Wallet), and package Traits/Helpers. Assumes `config/egi.php` is published
 *              and correctly configured in the consuming application.
 *
 * @state       Modifies database (`collections`, `egi`, `wallets`, `collection_user`) within a transaction.
 *              Modifies file storage on configured disks. Reads `config/egi.php`.
 *
 * @feature     - Automatic 'single_egi_default' collection creation.
 * @feature     - Default wallet association based on `config('egi.default_wallets')`.
 * @feature     - Multi-disk storage based on `config('egi.storage.disks')`.
 * @feature     - Critical disk failure handling based on `config('egi.storage.critical_disks')`.
 * @feature     - Uses standard Laravel Log, Config, Storage. No direct UCM/ULM dependency.
 * @feature     - DB transactions for atomicity. Structured JSON responses.
 * @feature     - Sequential position generation using `EgiHelper::generatePositionNumber`.
 * @feature     - Reads metadata (title, description, price, date, position, publish) from request.
 *
 * @signal      Returns `Illuminate\Http\JsonResponse` (HTTP 200 success, 4xx/5xx error).
 * @signal      Logs operations and errors via standard `Log` facade (channel defined by `$logChannel`).
 *
 * @privacy     Handles user ID, uploaded images, metadata. Encrypts original filename. Logs user IDs.
 *              Requires secure configuration and log management in the consuming application. Stores
 *              files on configured disks (compliance responsibility of consuming app config).
 *
 * @dependency  Laravel Framework. Models: `App\Models\*`. Config: `config/egi.php`.
 * @dependency  Package Traits: `HasValidation`, `HasUtilitys`. Package Helper: `EgiHelper`.
 *
 * @testing     Requires Feature tests simulating uploads. Mock Models, Storage, Cache, Config, EgiHelper.
 *              Verify DB changes, file storage, responses, rollbacks. Test config/ENV variations.
 *
 * @rationale   Encapsulates EGI-specific upload logic within the UUM package namespace.
 *              Uses standard Laravel features for MVP simplicity as requested.
 */
class EgiUploadHandler
{
    use HasValidation; // Provides validateFile()
    use HasUtilitys;   // Provides my_advanced_crypt(), formatSizeInMegabytes()

    /**
     * Log channel for this handler.
     * @var string
     */
    protected string $logChannel = 'upload'; // Default channel

    /**
     * Default type identifier for automatically created collections.
     * @var string
     */
    protected string $defaultCollectionType = 'single_egi_default';

    /**
     * 🎯 Handles the upload and processing of a single EGI image.
     * Orchestrates validation, DB operations (collection, EGI, wallets), and file storage.
     *
     * @param Request $request The incoming HTTP request containing the file and metadata.
     * @return JsonResponse A JSON response indicating success or failure.
     *
     * @throws Throwable Can re-throw exceptions for global handler or caller.
     */
    public function handleEgiUpload(Request $request): JsonResponse
    {
        $file = null;
        $originalName = 'unknown';
        $logContext = ['handler' => static::class, 'operation_id' => Str::uuid()->toString()]; // Add unique ID per request
        $creatorUser = null;
        $egiId = null; // Define here for potential use in final catch block

        try {
            // --- 0. Get Authenticated User ---
            $creatorUser = Auth::user();
            if (!$creatorUser instanceof User) {
                Log::channel($this->logChannel)->error('[EgiUploadHandler] Authentication Required.', $logContext);
                return response()->json([
                    'userMessage' => trans('uploadmanager::uploadmanager.js.auth_required') ?: 'Authentication required.',
                    'error_code' => 'AUTH_REQUIRED',
                ], 401);
            }
            $creatorUserId = $creatorUser->id;
            $logContext['user_id'] = $creatorUserId;
            Log::channel($this->logChannel)->info('[EgiUploadHandler] Upload request initiated.', $logContext);

            // --- Wrap core logic in DB Transaction ---
            $result = DB::transaction(function () use ($request, $creatorUser, &$file, &$originalName, &$logContext, &$egiId) {
                $creatorUserId = $creatorUser->id;

                // --- 1. Get and Validate File ---
                if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                    $errorCode = $request->hasFile('file') ? $request->file('file')->getError() : UPLOAD_ERR_NO_FILE;
                    throw new Exception("Invalid or missing 'file' input. Upload error code: {$errorCode}", 400);
                }
                /** @var UploadedFile $file */
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $logContext['original_filename'] = $originalName;
                Log::channel($this->logChannel)->debug('[EgiUploadHandler] Processing file.', $logContext);

                // --- 2. Core File Validation ---
                // Assumes config('AllowedFileType.collection.*') provides image rules.
                $this->validateFile($file); // Throws Exception on failure
                Log::channel($this->logChannel)->info('[EgiUploadHandler] Base file validation passed.', $logContext);

                // --- 3. Find or Create Default Collection ---
                $collection = $this->findOrCreateDefaultCollection($creatorUser, $logContext); // Throws Exception on critical failure
                $collectionId = $collection->id;
                $logContext['collection_id'] = $collectionId;

                // --- 4. Prepare EGI Data ---
                $tempPath = $file->getRealPath();
                if ($tempPath === false) { throw new Exception("Could not get real path for temporary file: {$originalName}"); }

                $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
                if (empty($extension)) { throw new Exception("Could not determine file extension for: {$originalName}"); }
                $extension = strtolower($extension); // Ensure lowercase

                $mimeType = $file->getMimeType() ?? 'application/octet-stream';
                $filetype = 'image'; // MVP Q1 constraint

                $crypt_filename = $this->my_advanced_crypt($originalName, 'e');
                if ($crypt_filename === false) { throw new Exception("Failed to encrypt filename for: {$originalName}"); }

                // Generate position using the dedicated Helper
                $num = EgiHelper::generatePositionNumber($collectionId, $this->logChannel); // Throws Exception on DB error

                $default_generated_name = '#' . str_pad($num, 4, '0', STR_PAD_LEFT) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

                // Get data from request (ensure names match form)
                $egiTitle = $request->input('egi-title', $default_generated_name);
                $egiDescription = $request->input('egi-description'); // Can be null
                // Read floor price carefully from request or config
                $egiFloorPriceInput = $request->input('egi-floor-price');
                $egiFloorPrice = ($egiFloorPriceInput !== null && is_numeric($egiFloorPriceInput))
                                 ? (float) $egiFloorPriceInput
                                 : ($collection->floor_price ?: Config::get('egi.default_floor_price', 0));

                $egiCreationDate = $request->input('egi-date'); // Expects YYYY-MM-DD format from input[type=date]
                $egiPosition = $request->input('egi-position', $num);
                $isPublished = $request->boolean('egi-publish'); // Handles 'on' or '1'

                $upload_id = $request->input('upload_id', Str::uuid()->toString()); // Use UUID for upload batch ID
                $logContext['upload_id'] = $upload_id;

                // --- 5. Create EGI Database Record ---
                $egi = new Egi();
                $egi->collection_id = $collectionId;
                $egi->user_id = $creatorUserId;
                $egi->owner_id = $creatorUserId;
                $egi->creator = $creatorUser->wallet ?? 'N/A'; // Check if user.wallet exists and is correct string
                $egi->owner_wallet = $creatorUser->wallet ?? 'N/A'; // Check if user.wallet exists
                $egi->upload_id = $upload_id;
                $egi->title = Str::limit($egiTitle, 60); // Limit title based on DB schema
                $egi->description = $egiDescription;
                $egi->extension = $extension;
                $egi->media = false;
                $egi->type = $filetype;
                $egi->bind = 0;
                $egi->paired = 0;
                $egi->price = $egiFloorPrice;
                $egi->floorDropPrice = $egiFloorPrice;
                $egi->position = (int) $egiPosition;
                $egi->creation_date = $egiCreationDate; // Assign directly (can be null or date string)
                $egi->size = $this->formatSizeInMegabytes($file->getSize()); // From HasUtilitys
                $dimensions = @getimagesize($tempPath);
                $egi->dimension = ($dimensions !== false) ? 'w:' . $dimensions[0] . ' x h:' . $dimensions[1] : 'N/A'; // Use N/A for consistency
                $egi->is_published = $isPublished;
                $egi->status = $isPublished ? 'published' : 'draft';
                $egi->file_crypt = $crypt_filename;
                $egi->file_hash = hash_file('md5', $tempPath);
                $egi->file_mime = $mimeType;
                // key_file set after save

                $egi->save(); // Save to get ID

                $egi->key_file = $egi->id; // Set key_file = id
                $egi->save(); // Save again

                $egiId = $egi->id; // Assign ID for potential use outside transaction
                $logContext['egi_id'] = $egiId;
                Log::channel($this->logChannel)->info('[EgiUploadHandler] EGI record created/updated.', $logContext);

                // --- 6. Store File to Configured Disks ---
                $primaryDisk = Config::get('egi.storage.disks.0', 'do');
                $root = Config::get("filesystems.disks.{$primaryDisk}.root", 'users_files');
                $base_path = rtrim($root, '/') . '/' . $creatorUserId . '/' . $collectionId . '/';
                $final_path_key = $base_path . $egi->key_file; // Use EGI ID as filename key

                // saveToMultipleDisks throws Exception on critical failure, rolling back transaction
                $savedUrls = $this->saveToMultipleDisks($final_path_key, $tempPath, $logContext);

                // --- 7. Invalidate Cache ---
                $cacheKey = 'collection_items-' . $collectionId; // Invalidate collection list
                Cache::forget($cacheKey);
                Log::channel($this->logChannel)->info('[EgiUploadHandler] Collection items cache invalidated.', array_merge($logContext, ['cache_key' => $cacheKey]));
                // Optionally invalidate specific EGI cache: Cache::forget('egi_details-' . $egiId);

                // --- 8. Prepare Success Data ---
                $successUserMessage = trans('uploadmanager::uploadmanager.file_saved_successfully', ['fileCaricato' => $originalName])
                                    ?: "File '{$originalName}' (EGI ID: {$egiId}) processed successfully.";

                return [ // Return data for JSON response
                    'success' => true,
                    'userMessage' => $successUserMessage,
                    'egiData' => [
                        'id' => $egiId,
                        'collection_id' => $collectionId,
                        'title' => $egi->title,
                        'urls' => $savedUrls, // URLs/paths from successful saves
                        'is_published' => $isPublished,
                        'position' => $egi->position,
                    ]
                ];
            }); // --- End DB Transaction ---

            // Build and return success response from transaction result
            return response()->json($result, 200);

        } catch (Throwable $e) {
            // Log detailed error
            $exceptionClass = get_class($e);
            $exceptionMessage = $e->getMessage();
            Log::channel($this->logChannel)->error("[EgiUploadHandler] Error processing EGI upload (EGI ID: {$egiId}).", array_merge($logContext, [
                'exception_class' => $exceptionClass,
                'exception_message' => $exceptionMessage,
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                // Only include trace in non-production environments for brevity
                'exception_trace' => app()->environment('production') ? null : $e->getTraceAsString(),
            ]));

            // Determine HTTP status code
            $statusCode = ($e instanceof Exception && is_int($e->getCode()) && $e->getCode() >= 400 && $e->getCode() < 600)
                        ? $e->getCode()
                        : 500;

            // Return generic error response using trans()
            $errorUserMessage = trans('uploadmanager::uploadmanager.js.error_during_upload') ?: 'An unexpected error occurred.';

            return response()->json([
                 'userMessage' => $errorUserMessage,
                 'error_code' => 'EGI_UPLOAD_FAILED', // Add a general error code
                 'error_details' => $exceptionMessage // Provide technical details for debugging
                ], $statusCode);
        }
    }

    /**
     * Finds the default collection for a user or creates it if it doesn't exist.
     * Also handles default wallet/membership creation for new collections.
     *
     * @param User $creatorUser The user.
     * @param array $logContext Base context for logging.
     * @return Collection The found or newly created default collection.
     * @throws Exception If collection/wallet creation fails critically.
     * @throws LogicException If essential configuration is missing/invalid.
     */
    protected function findOrCreateDefaultCollection(User $creatorUser, array $logContext): Collection
    {
        // --- Input Validation ---
        if (!$creatorUser instanceof User || !$creatorUser->id) {
             throw new LogicException("Invalid User object provided to findOrCreateDefaultCollection.");
        }

        // --- Find Existing ---
        $collection = Collection::where('creator_id', $creatorUser->id)
                                ->where('type', $this->defaultCollectionType)
                                ->first();

        if ($collection instanceof Collection) {
            Log::channel($this->logChannel)->info('[EgiUploadHandler] Found existing default collection.', array_merge($logContext, ['collection_id' => $collection->id]));
            return $collection; // Return existing collection
        }

        // --- Create New Collection ---
        Log::channel($this->logChannel)->info('[EgiUploadHandler] Default collection not found, creating new.', $logContext);
        try {
            // Read configuration with validation
            $defaultFloorPrice = Config::get('egi.default_floor_price');
            $ownerRole = Config::get('permission.default_roles.collection_owner'); // From Spatie config

            if (!is_numeric($defaultFloorPrice)) {
                throw new LogicException("Invalid configuration: 'egi.default_floor_price' must be numeric.");
            }
            if (empty($ownerRole) || !is_string($ownerRole)) {
                 throw new LogicException("Invalid configuration: 'permission.default_roles.collection_owner' must be a non-empty string.");
            }

            // Create the collection record
            $collection = Collection::create([
                'creator_id'        => $creatorUser->id,
                'owner_id'          => $creatorUser->id,
                'collection_name'   => $creatorUser->name . "'s Default EGIs", // More specific name
                'type'              => $this->defaultCollectionType,
                'status'            => 'draft',
                'floor_price'       => (float)$defaultFloorPrice,
            ]);
            $logContext['collection_id'] = $collection->id; // Update context for subsequent logs
            Log::channel($this->logChannel)->info('[EgiUploadHandler] Default collection record created.', $logContext);

            // Attach creator as owner via pivot table
            $collection->users()->attach($creatorUser->id, [
                'role'      => $ownerRole,
                'is_owner'  => true,
                'status'    => 'accepted',
                'joined_at' => now()
            ]);
            Log::channel($this->logChannel)->info('[EgiUploadHandler] Creator linked to collection as owner.', array_merge($logContext, ['role' => $ownerRole]));

            // Create default wallets (method handles its own logging/errors)
            $this->createDefaultWalletsForCollection($collection, $creatorUser->id, $logContext);

            return $collection; // Return the newly created collection

        } catch (Throwable $e) {
             // Log critical failure during creation
             Log::channel($this->logChannel)->error('[EgiUploadHandler] CRITICAL: Failed to create default collection structure.', array_merge($logContext, ['error' => $e->getMessage(), 'exception_class' => get_class($e)]));
             // Re-throw standard Exception to ensure transaction rollback
             throw new Exception("Failed to initialize default collection: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Creates and associates default wallets (Creator, EPP, Natan) for a new collection.
     * Reads configuration values directly from `config/egi.php`.
     * Logs warnings for configuration issues or DB errors but does not throw exceptions
     * to avoid blocking the entire upload for a non-critical wallet setup issue.
     *
     * @param Collection $collection The newly created collection.
     * @param int $creatorUserId The ID of the user who created the collection.
     * @param array $logContext Base context for logging.
     * @return void
     *
     * @configReads egi.default_wallets.* - Reads the pre-processed wallet configurations.
     * @configReads egi.default_ids.* - Reads the EPP/Natan user IDs.
     */
    protected function createDefaultWalletsForCollection(Collection $collection, int $creatorUserId, array $logContext): void
    {
        Log::channel($this->logChannel)->info('[EgiUploadHandler] Attempting to create default wallets.', $logContext);

        // --- Get Wallet Configuration ---
        $defaultWalletSetup = Config::get('egi.default_wallets');
        if (empty($defaultWalletSetup) || !is_array($defaultWalletSetup)) {
             Log::channel($this->logChannel)->warning("[EgiUploadHandler] 'egi.default_wallets' configuration is missing or invalid. Skipping default wallet creation.", $logContext);
             return; // Exit gracefully if config is bad
        }
        // --- End Config Reading ---

        foreach ($defaultWalletSetup as $role => $config) {
            $walletLogContext = array_merge($logContext, ['wallet_role' => $role]); // Add role to context

            // --- Determine User ID ---
            $userId = null;
            if ($role === 'Creator') {
                $userId = $creatorUserId;
            } elseif (isset($config['user_id_config_key']) && is_string($config['user_id_config_key'])) {
                $userId = Config::get($config['user_id_config_key']);
                $walletLogContext['user_id_source'] = $config['user_id_config_key'];
            } else {
                 Log::channel($this->logChannel)->warning("[EgiUploadHandler] Missing 'user_id_config_key' for non-Creator role.", $walletLogContext);
            }
            // --- End User ID ---

            // --- Determine Royalties ---
            $mintRoyalty = $config['mint_royalty'] ?? null;
            $rebindRoyalty = $config['rebind_royalty'] ?? null;
            // --- End Royalties ---

            // --- Determine Anonymity ---
            $isAnonymous = $config['is_anonymous'] ?? true;
            // --- End Anonymity ---

            // --- Validate Values ---
            if (empty($userId) || !is_numeric($userId)) {
                Log::channel($this->logChannel)->warning("[EgiUploadHandler] Skipping wallet: Invalid/Missing User ID.", array_merge($walletLogContext, ['retrieved_id' => $userId]));
                continue; // Skip this wallet
            }
            if ($mintRoyalty === null || !is_numeric($mintRoyalty)) {
                Log::channel($this->logChannel)->warning("[EgiUploadHandler] Skipping wallet: Invalid/Missing Mint Royalty.", array_merge($walletLogContext, ['retrieved_val' => $mintRoyalty]));
                continue;
            }
             if ($rebindRoyalty === null || !is_numeric($rebindRoyalty)) {
                Log::channel($this->logChannel)->warning("[EgiUploadHandler] Skipping wallet: Invalid/Missing Rebind Royalty.", array_merge($walletLogContext, ['retrieved_val' => $rebindRoyalty]));
                continue;
            }
            // Check User Exists (only if not the Creator)
            if ($role !== 'Creator' && !User::where('id', $userId)->exists()) {
                Log::channel($this->logChannel)->warning("[EgiUploadHandler] Skipping wallet: Configured User ID '{$userId}' not found.", $walletLogContext);
                continue;
            }
            // --- End Validation ---

            // --- Create Wallet Record ---
            try {
                Wallet::create([
                    'collection_id' => $collection->id,
                    'user_id'       => (int)$userId,
                    'platform_role' => $role, // Use the key as the role
                    'royalty_mint'  => (float)$mintRoyalty,
                    'royalty_rebind'=> (float)$rebindRoyalty,
                    'is_anonymous'  => (bool)$isAnonymous,
                ]);
                 Log::channel($this->logChannel)->info("[EgiUploadHandler] Default wallet created successfully.", array_merge($walletLogContext, ['user_id' => $userId]));
            } catch (Throwable $e) {
                 // Log DB error but don't let it fail the whole upload
                 Log::channel($this->logChannel)->error("[EgiUploadHandler] Failed to create default wallet record in DB.", array_merge($walletLogContext, ['user_id' => $userId, 'error' => $e->getMessage()]));
            }
            // --- End Create ---
        }
        Log::channel($this->logChannel)->info('[EgiUploadHandler] Default wallets creation attempt finished.', $logContext);
    }

     /**
      * Saves a file to multiple configured storage disks. Handles critical failures.
      *
      * @param string $pathKey The base storage key (e.g., "root/user/coll/egi_id").
      * @param string $tempPath The path to the temporary source file.
      * @param array $logContext Base context for logging.
      * @return array Associative array of [disk => urlOrPathKey] for successful saves.
      * @throws Exception If saving to a critical disk fails.
      * @throws LogicException If storage configuration is missing/invalid.
      */
     protected function saveToMultipleDisks(string $pathKey, string $tempPath, array $logContext): array
     {
         // --- Read and Validate Configuration ---
         $storageDisks = Config::get('egi.storage.disks');
         $criticalDisks = Config::get('egi.storage.critical_disks', []); // Default empty if missing

         if (empty($storageDisks) || !is_array($storageDisks)) {
              Log::channel($this->logChannel)->error('[EgiUploadHandler] Invalid or missing storage disk configuration.', array_merge($logContext, ['config_key' => 'egi.storage.disks']));
              throw new LogicException("Config 'egi.storage.disks' is missing or invalid.");
         }
         if (!is_array($criticalDisks)) {
             Log::channel($this->logChannel)->warning('[EgiUploadHandler] Invalid critical storage disk configuration, treating all as non-critical.', array_merge($logContext, ['config_key' => 'egi.storage.critical_disks']));
             $criticalDisks = [];
         }
         // --- End Config Validation ---

         $savedInfo = [];
         $errors = [];
         $contents = null;

         Log::channel($this->logChannel)->info('[EgiUploadHandler] Saving file to configured disks.', array_merge($logContext, ['disks' => $storageDisks, 'critical' => $criticalDisks, 'path_key' => $pathKey]));

         // --- Read File Content ---
         try {
             $contents = file_get_contents($tempPath);
             if ($contents === false) { throw new Exception("Failed to read temp file: {$tempPath}"); }
         } catch (Throwable $e) {
              Log::channel($this->logChannel)->error('[EgiUploadHandler] Cannot read temporary file content.', array_merge($logContext, ['tempPath' => $tempPath, 'error' => $e->getMessage()]));
              throw new Exception("Cannot read temporary upload file content.", 500, $e);
         }
         // --- End Read Content ---

         // --- Attempt Save to Each Disk ---
         foreach ($storageDisks as $disk) {
             // Verify disk is configured
             if (!Config::has("filesystems.disks.{$disk}")) {
                  Log::channel($this->logChannel)->error("[EgiUploadHandler] Storage disk '{$disk}' not configured in filesystems.php. Skipping.", $logContext);
                  $errors[$disk] = "Disk '{$disk}' not configured.";
                  continue;
             }

             $diskLogContext = array_merge($logContext, ['disk' => $disk]); // Context specific to this disk attempt
             try {
                 // Determine visibility (default public, make configurable if needed)
                 $visibility = Config::get("egi.storage.visibility.{$disk}", 'public');

                 $success = Storage::disk($disk)->put($pathKey, $contents, $visibility);
                 if (!$success) {
                    throw new Exception("Storage::put returned false. Check permissions/config.");
                 }

                 // Get URL or Path
                 try {
                     if (method_exists(Storage::disk($disk)->getAdapter(), 'getUrl')) {
                         $savedInfo[$disk] = Storage::disk($disk)->url($pathKey);
                     } else {
                         $savedInfo[$disk] = $pathKey; // Fallback for non-URL adapters
                     }
                 } catch (Throwable $eUrl) {
                     $savedInfo[$disk] = $pathKey; // Fallback on error
                     Log::channel($this->logChannel)->warning("[EgiUploadHandler] Could not get URL.", array_merge($diskLogContext, ['error' => $eUrl->getMessage()]));
                 }
                 Log::channel($this->logChannel)->info("[EgiUploadHandler] File successfully saved.", array_merge($diskLogContext, ['info' => $savedInfo[$disk]]));

             } catch (Throwable $e_store) {
                 $errorMsg = "Failed to save file. Error: " . $e_store->getMessage();
                 Log::channel($this->logChannel)->error('[EgiUploadHandler] ' . $errorMsg, array_merge($diskLogContext, ['exception_class' => get_class($e_store)]));
                 $errors[$disk] = $errorMsg;
             }
         }
         // --- End Save Loop ---

         // --- Check Critical Failures ---
         $criticalFailures = array_intersect_key($errors, array_flip($criticalDisks));
         if (!empty($criticalFailures)) {
             Log::channel($this->logChannel)->error('[EgiUploadHandler] CRITICAL STORAGE FAILURE. Initiating rollback.', array_merge($logContext, ['failures' => $criticalFailures]));
             $this->attemptRollbackStorage(array_keys($savedInfo), $pathKey, $logContext); // Rollback successful saves
             // Throw exception to rollback DB transaction
             throw new Exception("CRITICAL STORAGE FAILURE on disk(s): " . implode(', ', array_keys($criticalFailures)));
         }
         // --- End Critical Check ---

         // Log non-critical errors
         $nonCriticalFailures = array_diff_key($errors, $criticalFailures);
         if (!empty($nonCriticalFailures)) {
              Log::channel($this->logChannel)->warning('[EgiUploadHandler] Non-critical storage errors occurred.', array_merge($logContext, ['errors' => $nonCriticalFailures]));
         }

         return $savedInfo; // Return info on successful saves
     }

     /**
      * Attempts to delete a file from specified disks during a rollback.
      * Logs errors but does not interrupt the overall rollback process.
      *
      * @param array $disks List of disk names where the file might have been saved.
      * @param string $pathKey Storage key of the file to delete.
      * @param array $logContext Base context for logging.
      * @return void
      */
     protected function attemptRollbackStorage(array $disks, string $pathKey, array $logContext): void
     {
          Log::channel($this->logChannel)->warning('[EgiUploadHandler] Attempting storage rollback.', array_merge($logContext, ['disks_to_clean' => $disks, 'path_key' => $pathKey]));

          foreach ($disks as $disk) {
               $diskLogContext = array_merge($logContext, ['disk' => $disk]);
              try {
                   // Check existence before deleting
                   if (Storage::disk($disk)->exists($pathKey)) {
                       if (Storage::disk($disk)->delete($pathKey)) {
                           Log::channel($this->logChannel)->info("[EgiUploadHandler] Rollback: Deleted file successfully.", $diskLogContext);
                       } else {
                            // Log specific warning if delete returns false
                            Log::channel($this->logChannel)->warning("[EgiUploadHandler] Rollback Warning: Storage::delete returned false.", $diskLogContext);
                       }
                   } else {
                        Log::channel($this->logChannel)->info("[EgiUploadHandler] Rollback: File did not exist, skipping delete.", $diskLogContext);
                   }
              } catch (Throwable $e_delete) {
                  // Log any exception during delete attempt but continue
                  Log::channel($this->logChannel)->error("[EgiUploadHandler] Rollback Error: Exception during delete.", array_merge($diskLogContext, ['error' => $e_delete->getMessage()]));
              }
          }
          Log::channel($this->logChannel)->warning('[EgiUploadHandler] Storage rollback attempt finished.', $logContext);
     }

} // End Class EgiUploadHandler
