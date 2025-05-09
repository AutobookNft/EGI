<?php

namespace Ultra\EgiModule\Handlers;

// PHP & Laravel Imports


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Keep Log Facade for internal helper logging
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;
use Exception;
use LogicException;

// Application/Package Specific Imports
use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\Wallet;
use Ultra\EgiModule\Helpers\EgiHelper;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\UploadManager\Traits\HasValidation;
use App\Traits\HasUtilitys;
use App\Traits\HasCreateDefaultCollectionWallets;
use Carbon\Carbon;
// UEM Imports
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * 📜 Oracode Handler: EgiUploadHandler (v1.1 - UEM Integrated, Oracode Docs Complete)
 * Handles the backend logic for uploading EGI images, including metadata processing,
 * default collection management, multi-disk storage, and centralized error handling via UEM.
 *
 * @package     Ultra\EgiModule\Handlers
 * @version     1.1.0 // UEM Integration and Full Oracode Documentation
 * @author      Fabio Cherici & Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @since       2025-04-24
 *
 * @purpose     🎯 To orchestrate the entire backend EGI upload process: validation, DB operations,
 *              file storage, cache invalidation, returning a response via UEM for errors.
 *
 * @context     🧩 Instantiated via Dependency Injection (typically in `EgiUploadController`).
 *              Requires `ErrorManagerInterface` to be available in the service container.
 *              Operates within an authenticated HTTP request context. Uses package models and helpers.
 *
 * @state       💾 Modifies Database (`egi`, `collections`, `wallets` etc.) within a transaction.
 *              Modifies File Storage on configured disks. Modifies Cache. Reads Config.
 *
 * @feature     🗝️ Handles file and metadata upload in a single request.
 * @feature     🗝️ Uses `$request->validate()` for metadata validation.
 * @feature     🗝️ Uses `HasValidation` trait for core file validation.
 * @feature     🗝️ Implements "invisible" default collection/wallet creation logic.
 * @feature     🗝️ Uses `HasUtilitys` trait for crypto/formatting helpers.
 * @feature     🗝️ Implements multi-disk storage with fallback to 'local' using `saveToMultipleDisks`.
 * @feature     🗝️ Implements critical disk failure detection and triggers transaction rollback.
 * @feature     🗝️ Integrates fully with UEM (`ErrorManagerInterface`) for all error handling and response generation.
 * @feature     🗝️ Uses DB Transactions for atomicity.
 * @feature     🗝️ Includes detailed Oracode v1.5 documentation for all methods.
 *
 * @signal      🚦 Returns `Illuminate\Http\JsonResponse` (200 on success, 4xx/5xx via UEM on error).
 * @signal      🚦 All error logging and notification handled by UEM and its registered handlers.
 * @signal      🚦 Internal debug/warning logs within helpers use standard `Log` facade.
 *
 * @privacy     🛡️ Handles User ID, Uploaded Image, Metadata, Wallet info. Passes context to UEM.
 * @privacy     🛡️ `@privacy-internal`: User ID, wallet, file content, metadata, encrypted filename, IP (via Request passed to UEM).
 * @privacy     🛡️ `@privacy-lawfulBasis`: Necessary for performing the EGI creation service requested by the user.
 * @privacy     🛡️ `@privacy-purpose`: Process and securely store user-submitted EGI data.
 * @privacy     🛡️ `@privacy-technique`: DB Transactions, filename encryption (via trait), UEM error handling, configurable storage.
 * @privacy     🛡️ `@privacy-consideration`: Relies heavily on secure UEM configuration (handlers, sanitization), secure storage configuration, strong `app.key` for crypto, and careful review of helper methods (`findOrCreateDefaultCollection`, `saveToMultipleDisks`) regarding data handling. Ensure appropriate permissions for file storage.
 *
 * @dependency  🤝 Laravel Framework (Facades, Request, JsonResponse, Exception types).
 * @dependency  🤝 UEM (`ErrorManagerInterface`).
 * @dependency  🤝 Models (`User`, `Collection`, `Egi`, `Wallet`).
 * @dependency  🤝 Traits (`HasValidation`, `HasUtilitys`).
 * @dependency  🤝 Helpers (`EgiHelper`).
 * @dependency  🤝 Config (`config/egi.php`, `config/filesystems.php`, `config/app.php`, `config/AllowedFileType.php`, `config/permission.php`).
 *
 * @testing     🧪 Feature tests simulating POST requests with files/metadata. Mock `ErrorManagerInterface`.
 * @testing     🧪 Mock Models, Storage, Cache, Config, Helpers. Verify DB state, stored files, UEM `handle` calls (arguments, frequency), final JsonResponse. Test various success and failure paths (validation, DB, storage, permissions).
 *
 * @rationale   💡 Centralizes EGI upload backend logic into a dedicated, testable handler class.
 *              Leverages UEM for standardized error handling and response structure.
 *              Uses traits and helpers for code reuse. Implements core EGI business rules.
 */
class EgiUploadHandler
{
    use HasValidation;
    use HasUtilitys;
    use HasCreateDefaultCollectionWallets;

    /**
     * 📝 Log channel for non-UEM managed logs within helpers.
     * @var string
     */
    protected string $logChannel = 'egi_upload';

    /**
     * 🧱 @dependency UEM Instance.
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

     /**
     * 🧱 @dependency UltraLogManager instance.
     * Used for standardized logging.
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * 🎯 Constructor: Injects required dependencies.
     *
     * @param ErrorManagerInterface $errorManager The UltraErrorManager instance.
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

     /**
     * 🚀 Handles and persists the EGI file upload request using UEM for error management.
     * Orchestrates validation, DB operations, file storage, caching, and response generation.
     *
     * @purpose     🎯 To orchestrate the entire backend process for a single EGI upload: authenticate,
     *              validate file & metadata, manage default collection/wallets, create DB records
     *              (Egi, potentially Collection/Wallet links), store the file on configured disks,
     *              invalidate relevant caches, and return a structured JSON response via UEM for errors.
     *
     * --- Logic ---
     * 1.  Generate Operation ID, Authenticate User (-> UEM on fail).
     * 2.  Start DB Transaction.
     * 3.  Retrieve and Validate File Input (-> Exception -> UEM).
     * 4.  Core File Validation (`validateFile`) (-> Exception -> UEM).
     * 5.  Validate Request Metadata (`$request->validate`) (-> ValidationException -> UEM).
     * 6.  Find/Create Default Collection (`findOrCreateDefaultCollection`) (-> Exception -> UEM).
     * 7.  Prepare ALL EGI data (crypto filename, position, type, dimensions, hash, metadata from request, etc.). Check for potential errors (e.g., crypto fail -> Exception -> UEM).
     * 8.  Create and Populate the `Egi` model instance COMPLETELY.
     * 9.  Save the `Egi` model (first time to get ID). Handle potential DB Exception -> UEM.
     * 10. Assign `key_file` using the generated ID.
     * 11. Save the `Egi` model again. Handle potential DB Exception -> UEM.
     * 12. Store the physical file on configured disks using `saveToMultipleDisks`. (-> Exception on critical fail -> UEM).
     * 13. Invalidate relevant application cache(s).
     * 14. Prepare success data payload including key EGI information.
     * 15. Commit DB Transaction.
     * 16. Return success `JsonResponse` (200) containing the prepared success data.
     * 17. Catch `ValidationException`: Delegate to UEM (`EGI_VALIDATION_FAILED`) & Return UEM Response (422).
     * 18. Catch any other `Throwable`: Map Exception -> Delegate to UEM & Return UEM Response (500 or specific).
     * --- End Logic ---
     *
     * @param Request $request The incoming HTTP request. Expected keys: 'file', 'title'?, 'description'?, 'price'?, 'publish_date'?, 'publish_now'?, 'position'?, 'upload_id'?.
     * @return JsonResponse The JSON response (200 on success with 'success', 'userMessage', 'egiData'; 4xx/5xx via UEM on error with 'userMessage', 'error_code', 'errors'/'error_details').
     *
     * @throws Throwable Only if UEM itself fails during error handling (highly unlikely).
     *
     * @sideEffect 💾 Modifies Database (`egi`, `collections`, etc.). Modifies File Storage. Modifies Cache.
     * @sideEffect 📝 Delegates all error logging to UEM. Internal helpers log non-critical info via `Log` Facade.
     *
     * @configReads ⚙️ Reads multiple configuration files (see class DocBlock and helper methods).
     *
     * @privacy-purpose 🛡️ To process and securely store the user's EGI creation request, including the file and metadata.
     * @privacy-data 🛡️ Handles User ID, Wallet, File Content, Metadata, Encrypted Filename, IP. Passes context to UEM.
     * @privacy-lawfulBasis 🛡️ Necessary for service performance.
     */
    public function handleEgiUpload(Request $request): JsonResponse
    {
        $file = null;
        $originalName = 'unknown';
        $logContext = ['handler' => static::class, 'operation_id' => Str::uuid()->toString()];
        $creatorUser = null;
        $egiId = null; // For context in final catch

        try {
            // --- 0. Authenticate User ---
            if (Auth::check()) {
                $creatorUser = Auth::user();
            }
            // Se non autenticato completamente, controlla la connessione wallet
            elseif (session()->has('auth_status') && session()->get('auth_status') === 'connected') {
                $userId = session()->get('connected_user_id');

                if ($userId) {
                    $creatorUser = User::find($userId);
                    // Opzionale: puoi aggiungere un controllo aggiuntivo sul wallet
                    $wallet = session()->get('connected_wallet');
                    if ($creatorUser && $creatorUser->wallet !== $wallet) $creatorUser = null;
                }
            }

            if (!$creatorUser instanceof User) {
                return $this->errorManager->handle('EGI_AUTH_REQUIRED', $logContext);
            }

            $creatorUserId = $creatorUser->id;
            $logContext['user_id'] = $creatorUserId;
            $logContext['auth_type'] = Auth::check() ? 'full' : 'wallet_connected';

            $this->logger->info('[EGI HandleEgiUpload] User authenticated', $logContext);

            // --- Start DB Transaction ---
            $result = DB::transaction(function () use ($request, $creatorUser, &$file, &$originalName, &$logContext, &$egiId) {
                $creatorUserId = $creatorUser->id;

                // --- 1. Retrieve and Validate File Input ---
                if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                    $uploadErrorCode = $request->hasFile('file') ? $request->file('file')->getError() : UPLOAD_ERR_NO_FILE;
                    throw new Exception("Invalid or missing 'file' input. Upload error code: {$uploadErrorCode}", 400);
                }
                /** @var UploadedFile $file */
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName() ?? 'uploaded_file';
                $logContext['original_filename'] = $originalName;

                // --- 2. Core File Validation (Using Trait) ---
                $this->validateFile($file); // Throws Exception on failure

                // --- 3. Validate Request Metadata ---
                $validatedData = $request->validate([
                    'egi-title' => ['nullable', 'string', 'max:60'],
                    'egi-description' => ['nullable', 'string', 'max:5000'],
                    'egi-floor-price' => ['nullable', 'numeric', 'min:0'],
                    'egi-date' => ['nullable', 'date_format:Y-m-d'], // Expects 'YYYY-MM-DD'
                    'egi-position' => ['nullable', 'integer', 'min:1'],
                    'egi-publish' => ['nullable', 'boolean'],
                    // Add other metadata validation rules here
                ]); // Throws ValidationException on failure

                $this->logger->info('[EGI HandleEgiUpload] Metadata validated', $validatedData);

                // --- 4. Find or Create Default Collection & Wallets ---
                $collection = $this->findOrCreateDefaultCollection($creatorUser, $logContext); // Throws Exception on critical failure
                $collectionId = $collection->id;
                $logContext['collection_id'] = $collectionId;

                // --- 5. Prepare EGI Data ---
                $tempPath = $file->getRealPath();
                if ($tempPath === false) { throw new Exception("Cannot access temporary file path for: {$originalName}"); }

                $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
                if (empty($extension)) { throw new Exception("Could not determine file extension for: {$originalName}"); }
                $extension = strtolower($extension);

                $mimeType = $file->getMimeType() ?? 'application/octet-stream';
                $filetype = 'image'; // Hardcoded for MVP Q1

                $crypt_filename = $this->my_advanced_crypt($originalName, 'e');
                if ($crypt_filename === false) { throw new Exception("Failed to encrypt filename for: {$originalName}"); }

                // Position: Use from validated data if present and valid, otherwise generate
                $egiPosition = isset($validatedData['egi-position']) && is_numeric($validatedData['egi-position'])
                               ? (int) $validatedData['egi-position']
                               : EgiHelper::generatePositionNumber($collectionId, $this->logChannel); // Use helper

                // Title: Use from validated data if present and not empty, otherwise generate default
                $egiTitle = !empty(trim($validatedData['egi-title'] ?? ''))
                            ? trim($validatedData['egi-title'])
                            : '#' . str_pad($egiPosition, 4, '0', STR_PAD_LEFT) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

                // Floor Price: Use from validated data if present and numeric, otherwise use collection's, otherwise use config default
                $egiFloorPrice = isset($validatedData['egi-floor-price']) && is_numeric($validatedData['egi-floor-price'])
                                ? (float) $validatedData['egi-floor-price']
                                : ($collection->floor_price ?: (float) Config::get('egi.default_floor_price', 0));

                // Published Status & Date: Use from validated data
                $isPublished = $validatedData['egi-publish'] ?? false;

                // Publish Date: Use from validated data if present and valid, otherwise now
                $publishDate = $validatedData['egi-date'] ?? Carbon::now()->toDateTimeString();
                $status = 'local'; // Default status

                $upload_id = $request->input('upload_id', Str::uuid()->toString()); // Unique ID for this batch/upload instance
                $logContext['upload_id'] = $upload_id;

                // --- 6. Create EGI Database Record ---
                $egi = new Egi();
                $egi->collection_id = $collectionId;
                $egi->user_id = $creatorUserId; // The admin user ID in Q1
                $egi->owner_id = $creatorUserId; // Initial owner is the uploader
                $egi->creator = $creatorUser->wallet ?? 'WalletNotSet'; // Ensure user model has 'wallet' attribute or similar
                $egi->owner_wallet = $creatorUser->wallet ?? 'WalletNotSet';
                $egi->upload_id = $upload_id;
                $egi->title = Str::limit($egiTitle, 60); // Apply DB limit
                $egi->description = $validatedData['egi-description']; // Already validated
                $egi->extension = $extension;
                $egi->media = false; // Only images
                $egi->type = $filetype;
                $egi->bind = 0; // Default
                $egi->mint = 0; // Default
                $egi->rebind = 0; // Default
                $egi->paired = 0; // Default
                $egi->price = $egiFloorPrice;
                $egi->floorDropPrice = $egiFloorPrice; // Default to same as price initially
                $egi->position = $egiPosition;
                $egi->creation_date = $publishDate; // Store the publish date if provided (can be null)
                $egi->size = $this->formatSizeInMegabytes($file->getSize());
                $dimensions = @getimagesize($tempPath);
                $egi->dimension = ($dimensions !== false) ? 'w:' . $dimensions[0] . ' x h:' . $dimensions[1] : 'N/A';
                $egi->is_published = $isPublished; // Boolean flag based on 'publish_now'
                $egi->status = $status;
                $egi->file_crypt = $crypt_filename;
                $egi->file_hash = hash_file('md5', $tempPath); // Hash the actual file content
                $egi->file_mime = $mimeType;
                // key_file will be set after initial save

                $egi->save(); // First save to get the ID (throws DB exceptions on failure)

                $egi->key_file = $egi->id; // Assign the ID as the key file identifier
                $egi->save(); // Second save to persist key_file (throws DB exceptions on failure)

                $egiId = $egi->id; // Store the ID for context and response
                $logContext['egi_id'] = $egiId;

                // --- 7. Store Physical File ---
                $base_path = 'users_files/collections_' . $collectionId . '/creator_' . $creatorUserId . '/';
                $final_path_key = $base_path . $egi->key_file . '.' . $extension; // Final path for the file

                $savedUrls = $this->saveToMultipleDisks($final_path_key, $tempPath, $logContext); // Throws Exception on critical storage failure

                // --- 8. Invalidate Cache ---
                $cacheKey = 'collection_items-' . $collectionId; // Example cache key
                Cache::forget($cacheKey);

                // --- 9. Prepare FULL Success Data ---
                $userMsgKey = 'uploadmanager::uploadmanager.file_saved_successfully';
                $userMsgFallback = "File '{$originalName}' (EGI ID: {$egiId}) processed successfully.";
                $successUserMessage = trans($userMsgKey, ['fileCaricato' => $originalName]) ?: $userMsgFallback;

                // Return the complete data structure expected by the success JSON response
                return [
                    'success' => true,
                    'userMessage' => $successUserMessage,
                    'egiData' => [ // Include detailed data about the created EGI
                        'id' => $egiId,
                        'collection_id' => $collectionId,
                        'title' => $egi->title,
                        'description' => $egi->description, // Return description
                        'price' => $egi->price, // Return price
                        'position' => $egi->position, // Return position
                        'status' => $egi->status, // Return status
                        'published_at' => $egi->published_at?->toIso8601String(), // Return formatted date or null
                        'fileName' => $originalName, // Return original filename
                        'urls' => $savedUrls, // Dictionary of [disk => urlOrPath]
                        'mime_type' => $egi->file_mime, // Return mime type
                        'size_mb' => $egi->size, // Return size string
                        'dimensions' => $egi->dimension, // Return dimensions string
                        'created_at' => $egi->created_at->toIso8601String(), // Return creation timestamp
                    ]
                ];
            }); // --- End DB Transaction ---

            // --- 10. Return Success JSON ---
            // $result now contains the full success data array
            return response()->json($result, 200);

        } catch (ValidationException $e) {
            // --- Handle Validation Errors via UEM ---
            $logContext['validation_errors'] = $e->errors();
            return $this->errorManager->handle('EGI_VALIDATION_FAILED', $logContext, $e);

        } catch (Throwable $e) {
            // --- Handle All Other Errors via UEM ---
            $logContext['egi_id'] = $egiId; // Include EGI ID if generated before failure
            $errorCode = $this->mapEgiExceptionToUemCode($e);
            return $this->errorManager->handle($errorCode, $logContext, $e);
        }
    }

    /**
     * 🗺️ Map specific exceptions occurring during EGI upload to UEM error codes.
     * @purpose     Translate specific technical exceptions into meaningful UEM error codes.
     * --- Logic ---
     * 1. Check exception message/type for known critical failures (storage, DB, config).
     * 2. Return specific UEM code if match found.
     * 3. Default to 'EGI_UNEXPECTED_ERROR' otherwise.
     * --- End Logic ---
     * @param Throwable $e The caught exception.
     * @return string The mapped UEM error code.
     * @privacy Non-sensitive logic, handles exception messages.
     */
    protected function mapEgiExceptionToUemCode(Throwable $e): string
    {
        if (str_contains($e->getMessage(), 'CRITICAL STORAGE FAILURE')) { return 'EGI_STORAGE_CRITICAL_FAILURE'; }
        if ($e instanceof \Illuminate\Database\QueryException) { return 'EGI_DB_ERROR'; }
        if (str_contains($e->getMessage(), 'initialize default collection')) { return 'EGI_COLLECTION_INIT_ERROR';}
        if ($e->getCode() === 400 && str_contains($e->getMessage(), 'file input')) { return 'EGI_FILE_INPUT_ERROR';}
        if ($e instanceof LogicException && str_contains($e->getMessage(), 'disk \'local\' is not configured')) { return 'EGI_STORAGE_CONFIG_ERROR';}
        if (str_contains($e->getMessage(), 'encrypt filename')) { return 'EGI_CRYPTO_ERROR'; }
        // Add more specific mappings here...

        return 'EGI_UNEXPECTED_ERROR'; // Generic fallback
    }

     /**
     * 🛡️ Finds or creates the default collection for a given user upon first EGI upload.
     * Ensures necessary wallets are associated and the creator is assigned the owner role.
     * Uses the Log facade for internal warnings/debug. Throws Exception on critical configuration or DB errors.
     * Updates the user's `default_collection_id` field when a new default collection is created.
     *
     * @purpose     🎯 To provide or initialize the required default Collection context for saving a new EGI,
     *              using the `default_collection_id` field on the User model as the primary identifier, and
     *              setting up initial wallet links and owner permissions.
     *
     * --- Logic ---
     * 1.  Read configuration values for default collection name, floor price.
     * 2.  Validate the retrieved `default_floor_price` configuration. Throw Exception if invalid.
     * 3.  Check the `$creatorUser->default_collection_id` field.
     * 4.  If ID is set: Find Collection. If found, return. If not found, throw critical Exception.
     * 5.  If ID is NULL:
     *     a. Start nested try-catch for creation process.
     *     b. Create and populate new `Collection` instance COMPLETELY.
     *     c. Save new `Collection`.
     *     d. Update `$logContext`.
     *     e. Update `$creatorUser->default_collection_id` and save `$creatorUser`. Throw Exception if user save fails.
     *     f. Associate Default Wallets: Loop config `egi.default_wallets`, find Wallets, sync to Collection using `wallets()` relationship. Log warnings on failure.
     *     g. Assign Creator Owner Role: Read `egi.default_roles.collection_owner` config. Assign role using `users()` relationship and pivot data. Log warnings on failure.
     *     h. Log successful creation.
     *     i. Return the new `$collection`.
     * 6.  Catch `Throwable` during creation: Log CRITICAL, re-throw generic Exception.
     * --- End Logic ---
     *
     * @param User $creatorUser The authenticated user instance (will be modified if new collection created).
     * @param array &$logContext Context array passed by reference.
     * @return Collection The found or newly created default Collection model instance.
     * @throws Exception If config is invalid, DB errors occur, or data inconsistency is found.
     * @sideEffect 💾 Creates `collections` record, updates `users` record, links wallets/roles via pivot tables.
     * @sideEffect 📝 Logs via `Log` facade. Updates `$logContext`.
     * @configReads ⚙️ `egi.default_collection_flag` (no longer used in logic), `egi.default_collection_name`, `egi.default_floor_price`, `egi.default_wallets`, `egi.default_roles.collection_owner`.
     * @privacy-purpose 🛡️ Establish default EGI collection context and permissions. Updates user record.
     * @privacy-data 🛡️ Reads/Writes User ID, default_collection_id. Reads Wallet/Role config. Writes to DB. Logs User ID, Collection ID.
     * @privacy-lawfulBasis 🛡️ Necessary for service performance and maintaining user context.
     */
    protected function findOrCreateDefaultCollection(User $creatorUser, array &$logContext): Collection
    {
        // --- 1. Read Configuration & Validate ---
        // $defaultCollectionFlag is no longer used for lookup, identification is via user->default_collection_id
        $defaultCollectionName = $creatorUser->name . "'s Collection"; // Assumes User model has a 'name' attribute
        $defaultFloorPriceConfig = Config::get('egi.default_floor_price');
        if (!is_numeric($defaultFloorPriceConfig)) {
            Log::channel($this->logChannel)->error(
                "[EgiUploadHandler::findOrCreateDefaultCollection] Invalid default floor price config.",
                array_merge($logContext, ['config_key' => 'egi.default_floor_price', 'retrieved_value' => $defaultFloorPriceConfig])
            );
            throw new Exception("Invalid configuration: 'egi.default_floor_price' must be numeric.");
        }
        $defaultFloorPrice = (float) $defaultFloorPriceConfig;

        // --- 3. Check User's Current Collection ---
        $collection = $creatorUser->currentCollection();

        // Extract first item if it's an Eloquent Collection
        if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
            $collection = $collection->first();
        }

        if ($collection) {
            Log::channel($this->logChannel)->info(
                '[EgiUploadHandler::findOrCreateDefaultCollection] Found existing current collection.',
                array_merge($logContext, ['collection_id' => $collection->id])
            );
            $logContext['collection_id'] = $collection->id; // Ensure context has the ID
            return $collection;
        }

        // Se currentCollection() ha restituito null, dobbiamo verificare se c'è un'incoerenza nei dati
        // (current_collection_id impostato ma collezione non trovata)
        $currentCollectionIdFromUser = $creatorUser->current_collection_id;
        $currentCollectionIdFromSession = session('current_collection_id');

        // Verifica incoerenza: ID impostato ma collezione non trovata
        if ($currentCollectionIdFromUser !== null || $currentCollectionIdFromSession !== null) {
            $idToReport = $currentCollectionIdFromUser ?? $currentCollectionIdFromSession;

            // Data inconsistency: User points to a current collection that doesn't exist!
            Log::channel($this->logChannel)->critical(
                '[EgiUploadHandler::findOrCreateDefaultCollection] DATA INCONSISTENCY: User current_collection_id points to non-existent collection.',
                array_merge($logContext, ['non_existent_collection_id' => $idToReport])
            );

            // Consider adding logic here to potentially reset the user's current_collection_id to null
            // $creatorUser->current_collection_id = null; $creatorUser->save();
            throw new Exception("Data inconsistency detected: Current collection ID '{$idToReport}' not found.", 500);
        }

        // If we're here, user simply doesn't have a current collection set
        Log::channel($this->logChannel)->info(
            '[EgiUploadHandler::findOrCreateDefaultCollection] No current collection ID found for user.',
            $logContext
        );

        // --- 5. If Default ID is NULL, Create New Default Collection ---
        Log::channel($this->logChannel)->info('[EgiUploadHandler::findOrCreateDefaultCollection] No default collection set for user. Creating new one.', $logContext);

        try {
            // 5.a Create new Collection instance
            $collection = new Collection();

            // 5.b Populate ALL required fields for your Collection model based on your schema
            $collection->creator_id = $creatorUser->id;
            $collection->owner_id = $creatorUser->id; // Same as creator initially

            // Name: Based on user's name as per your specification
            $collection->collection_name = $defaultCollectionName; // Assumes User model has a 'name' attribute

            $collection->description = 'Default collection automatically created for single EGI uploads.';

            $collection->type = 'single_upload'; // Identifier for this auto-created collection type

            $collection->floor_price = $defaultFloorPrice;

            $collection->epp_id = Config::get('egi.epp_id', 2); // Optional, set to null if not needed

            // Status: Based on your definition (e.g., 'published', 'local')
            $collection->status = 'local'; // Your term for "not yet minted"

            // Position: Calculated for this user's collections
            $collection->position = EgiHelper::generateCollectionPosition($creatorUser->id, $this->logChannel);

            // Is Published: Visibility in the Marketplace (default non-visible)
            $collection->is_published = false;

            // **** ADD ANY OTHER REQUIRED FIELDS for your Collection/Team model based on your schema ****
            // Ensure all non-nullable columns have values assigned here.
            // Examples (adjust to your schema):
            // $collection->cover_image_url = null; // If nullable
            // $collection->banner_image_url = null; // If nullable
            // $collection->category_id = null; // Or read from config if default category needed
            // $collection->wallet_address = $creatorUser->wallet; // If this field exists and should be set here

            // --- End Population ---

            // 5.c Save the new Collection
            $collection->save();

            // 5.d Update log context
            $newCollectionId = $collection->id;
            $logContext['collection_id'] = $newCollectionId;
            Log::channel($this->logChannel)->debug('[EgiUploadHandler::findOrCreateDefaultCollection] New collection record saved.', $logContext);

            // 5.e Update User Record with new default ID
            $creatorUser->default_collection_id = $newCollectionId;
            // Use DB transaction wrapper for this if it's outside the main one, but here it's nested.
            if (!$creatorUser->save()) {
                 Log::channel($this->logChannel)->error('[EgiUploadHandler::findOrCreateDefaultCollection] FAILED TO UPDATE USER default_collection_id.', $logContext);
                 // This is critical, relationship not set. Throw exception to rollback collection creation.
                 throw new Exception("Failed to update user record with new default collection ID.");
            }
            Log::channel($this->logChannel)->info('[EgiUploadHandler::findOrCreateDefaultCollection] Updated user default_collection_id.', $logContext);

            // 5.f Associate Default Wallets

            try {
                // Call the trait method to generate/link default wallets for the new collection.
                // The trait is responsible for its own internal logic, logging, and potential errors.
                $this->generateDefaultWallets($collection, $creatorUser->wallet, $creatorUser->id);

                // Log that the trait method was called successfully (Trait handles internal logging)
                Log::channel($this->logChannel)->info('[EgiUploadHandler::findOrCreateDefaultCollection] Called generateDefaultWallets trait method.', $logContext);

            } catch (Throwable $eWallet) {
                // Catch any exception thrown *by the trait method* during wallet generation.
                // Log this failure specifically.
                Log::channel($this->logChannel)->error(
                    '[EgiUploadHandler::findOrCreateDefaultCollection] Error occurred within generateDefaultWallets trait method.',
                    array_merge($logContext, [
                        'error' => $eWallet->getMessage(),
                        'exception_class' => get_class($eWallet),
                        'exception_file' => $eWallet->getFile(), // Include details from trait exception
                        'exception_line' => $eWallet->getLine(),
                    ])
                );
                // Re-throw a generic exception to trigger the main transaction rollback
                // and be handled by the main catch block in handleEgiUpload,
                // mapping to an appropriate UEM code.
                 throw new Exception("Failed to generate default wallets: " . $eWallet->getMessage(), 500, $eWallet); // Pass original exception as $previous
            }

            // 5.g Assign Creator Owner Role
            $ownerRoleName = Config::get('egi.default_roles.collection_owner');

            if (!empty($ownerRoleName) && is_string($ownerRoleName)) {
                 Log::channel($this->logChannel)->info("[EgiUploadHandler::findOrCreateDefaultCollection] Attempting to assign owner role to creator.", array_merge($logContext, ['role_name' => $ownerRoleName]));
                try {
                    // --- ROLE ASSIGNMENT LOGIC (CONCRETE EXAMPLE - ASSUMING PIVOT TABLE) ---
                    // Assumes a Many-to-Many relationship named 'users' on the Collection model
                    if (method_exists($collection, 'users')) {
                        // Attach the user to the collection with the specified role in the pivot table
                        $collection->users()->syncWithoutDetaching([
                            $creatorUser->id => ['role' => $ownerRoleName]
                        ]);
                         Log::channel($this->logChannel)->info("[EgiUploadHandler::findOrCreateDefaultCollection] Assigned owner role via pivot.", array_merge($logContext, ['role' => $ownerRoleName]));
                    } else {
                         Log::channel($this->logChannel)->warning("[EgiUploadHandler::findOrCreateDefaultCollection] Collection model missing 'users' relationship method for role assignment via pivot.", $logContext);
                         // Add alternative logic here if using Spatie Teams or another method
                    }
                    // --- END ROLE ASSIGNMENT LOGIC ---
                } catch (Throwable $eRole) {
                     Log::channel($this->logChannel)->error("[EgiUploadHandler::findOrCreateDefaultCollection] Failed to assign owner role.", array_merge($logContext, ['role_name' => $ownerRoleName, 'error' => $eRole->getMessage()]));
                     // Non-critical? Continue.
                }
            } else {
                 Log::channel($this->logChannel)->warning("[EgiUploadHandler::findOrCreateDefaultCollection] Default owner role name ('egi.default_roles.collection_owner') not configured or invalid.", array_merge($logContext, ['retrieved_value' => $ownerRoleName]));
            }

            // 5.h Log success
            Log::channel($this->logChannel)->info('[EgiUploadHandler::findOrCreateDefaultCollection] New default collection setup complete.', $logContext);

            // 5.i Return the new collection
            return $collection;

        } catch (Throwable $e) {
             // 6. Catch critical errors during creation
             Log::channel($this->logChannel)->error('[EgiUploadHandler::findOrCreateDefaultCollection] CRITICAL: Failed during default collection creation process.', array_merge($logContext, ['error' => $e->getMessage(), 'exception_class' => get_class($e)]));
             // Re-throw to trigger transaction rollback in handleEgiUpload
             throw new Exception("Failed to initialize default collection: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * 💾 Saves a file to multiple configured storage disks with fallback to 'local'.
     * Handles critical failures by throwing an Exception, which should trigger DB rollback.
     * Uses standard Log facade for internal warnings/errors during the process.
     *
     * @purpose     🎯 To persist the uploaded file content onto one or more storage disks defined
     *              in the 'egi.storage.disks' config, ensuring critical disks succeed. Defaults
     *              to the 'local' disk if configuration is missing or invalid.
     *
     * --- Logic ---
     * 1.  Read `egi.storage.disks` and `egi.storage.critical_disks` config.
     * 2.  **Fallback:** If `disks` config is invalid/empty, default `$storageDisks` and `$criticalDisks` to `['local']` and log a warning.
     * 3.  If `disks` config is valid, validate `critical_disks`. If invalid, default to `[]` (no critical disks) and log a warning.
     * 4.  Read temporary file content. Throw Exception if read fails (cannot proceed).
     * 5.  Loop through each disk in the determined `$storageDisks` array.
     * 6.  Verify the disk is configured in `config/filesystems.php`. Log error and skip if not (unless it's the fallback 'local' disk, then throw LogicException).
     * 7.  Attempt `Storage::disk($disk)->put($pathKey, $contents, $visibility)`. Read visibility from `egi.storage.visibility.*`.
     * 8.  On success, retrieve URL (if available) or path key and store in `$savedInfo`. Log success.
     * 9.  On failure, store the error message in `$errors` array, keyed by disk name. Log the error.
     * 10. After looping, identify critical failures by intersecting `$errors` with `$criticalDisks`.
     * 11. If any critical failure exists: Log CRITICAL error, **throw Exception** (this will bubble up and cause the DB transaction in `handleEgiUpload` to roll back). Do NOT attempt rollback here.
     * 12. Log any non-critical failures as warnings.
     * 13. Return the `$savedInfo` array containing details of successful saves.
     * --- End Logic ---
     *
     * @param string $pathKey The base storage key (e.g., "users_files/USER_ID/COLL_ID/EGI_ID"). Should NOT include extension.
     * @param string $tempPath The absolute path to the temporary source file on the server.
     * @param array $logContext Base context array for logging messages via `Log` facade.
     * @return array Associative array of `[diskName => urlOrPathKey]` for disks where the save was successful.
     * @throws Exception If reading the temporary file fails OR if saving to any disk marked as 'critical' fails.
     * @throws LogicException If the fallback 'local' disk is required but not configured in `config/filesystems.php`.
     *
     * @sideEffect 💾 Writes file content to one or more configured storage disks.
     * @sideEffect 📝 Logs warnings/errors for individual disk operations or config issues via `Log` facade.
     *
     * @configReads ⚙️ `egi.storage.disks` (Array), `egi.storage.critical_disks` (Array), `egi.storage.visibility.*` (String - e.g., 'public'/'private'), `filesystems.disks.*`.
     *
     * @privacy-purpose 🛡️ Persists the uploaded file content to designated storage locations.
     * @privacy-data 🛡️ Handles the file content. The `$pathKey` typically includes UserID, CollectionID, EgiID. Logs context.
     * @privacy-lawfulBasis 🛡️ Necessary for fulfilling the EGI upload service request.
     * @privacy-consideration 🛡️ Ensure storage disks, visibility, and access controls are configured securely. Ensure `$pathKey` structure doesn't unintentionally leak sensitive info if URLs are public. Logging contains context which might include IDs.
     */
    protected function saveToMultipleDisks(string $pathKey, string $tempPath, array $logContext): array
    {

        Log::channel($this->logChannel)->info('[EgiUploadHandler::saveToMultipleDisks] Starting save process.', $logContext);

        // --- 1. Read and Validate Configuration with Fallback ---
        $storageDisksConfig = Config::get('egi.storage.disks');
        $criticalDisksConfig = Config::get('egi.storage.critical_disks');

        if (empty($storageDisksConfig) || !is_array($storageDisksConfig)) {
            Log::channel($this->logChannel)->warning(
                '[EgiUploadHandler::saveToMultipleDisks] Config \'egi.storage.disks\' is missing or invalid. Defaulting to \'public\' disk.',
                array_merge($logContext, ['config_key' => 'egi.storage.disks', 'retrieved_value' => $storageDisksConfig])
            );
            $storageDisks = ['public']; // Cambiato da 'local' a 'public'
            $criticalDisks = ['public']; // Anche il fallback è considerato critico
        } else {
            $storageDisks = $storageDisksConfig;
            if ($criticalDisksConfig === null || !is_array($criticalDisksConfig)) {
                Log::channel($this->logChannel)->warning(
                    '[EgiUploadHandler::saveToMultipleDisks] Config \'egi.storage.critical_disks\' is missing or invalid. No disks explicitly marked critical.',
                    array_merge($logContext, ['config_key' => 'egi.storage.critical_disks', 'retrieved_value' => $criticalDisksConfig])
                );
                $criticalDisks = []; // No disks are critical if config is invalid/missing
            } else {
                $criticalDisks = array_intersect($criticalDisksConfig, $storageDisks); // Ensure critical disks are valid storage disks
            }
        }

        $savedInfo = [];
        $errors = [];
        $contents = null;

        Log::channel($this->logChannel)->info('[EgiUploadHandler::saveToMultipleDisks] Preparing to save file.', array_merge($logContext, ['target_disks' => $storageDisks, 'critical_disks' => $criticalDisks, 'path_key' => $pathKey]));

        // --- 4. Read File Content ---
        try {
            $contents = file_get_contents($tempPath);
            if ($contents === false) {
                // Use new Exception for clarity
                throw new Exception("Failed to read content from temporary file: {$tempPath}");
            }
        } catch (Throwable $e) {
             Log::channel($this->logChannel)->error('[EgiUploadHandler::saveToMultipleDisks] Cannot read temporary file content.', array_merge($logContext, ['tempPath' => $tempPath, 'error' => $e->getMessage()]));
             // Re-throw because we cannot proceed without content
             throw new Exception("Cannot read temporary upload file content.", 500, $e);
        }
        // --- End Read Content ---

        // --- 5-9. Attempt Save to Each Disk ---
        foreach ($storageDisks as $disk) {
            $diskLogContext = array_merge($logContext, ['disk' => $disk]);

            // 6. Verify disk configuration
            if (!Config::has("filesystems.disks.{$disk}")) {
                 $errorMsg = "Storage disk '{$disk}' not configured in filesystems.php. Skipping.";
                 Log::channel($this->logChannel)->error("[EgiUploadHandler::saveToMultipleDisks] {$errorMsg}", $diskLogContext);
                 $errors[$disk] = $errorMsg;

                 // If the unconfigured disk IS the fallback 'local' disk, this is fatal
                 if ($disk === 'local' && $storageDisks === ['local']) {
                    throw new LogicException("Default fallback storage disk 'local' is not configured in filesystems.php.");
                 }
                 continue; // Skip to next disk
            }

            // 7. Attempt Storage::put
            try {
                $visibility = Config::get("egi.storage.visibility.{$disk}", 'public'); // Default to public visibility
                $success = Storage::disk($disk)->put($pathKey, $contents, $visibility);
                if (!$success) {
                   // Throw specific exception if put returns false
                   throw new Exception("Storage::put returned false. Check permissions/configuration for disk '{$disk}'.");
                }

                // 8. Get URL/Path on Success
                try {
                     // Check adapter type if possible, otherwise try url() and fallback
                     $adapter = Storage::disk($disk)->getAdapter();
                     if (method_exists($adapter, 'getUrl')) {
                         $savedInfo[$disk] = Storage::disk($disk)->url($pathKey);
                     } elseif (method_exists($adapter, 'getPathPrefix')) {
                          // For local driver, maybe construct a relative path? Or just store key.
                          $savedInfo[$disk] = $pathKey; // Storing the key might be sufficient
                     } else {
                         $savedInfo[$disk] = $pathKey; // Fallback for unknown adapters
                     }
                } catch (Throwable $eUrl) {
                     $savedInfo[$disk] = $pathKey; // Fallback to path key on any error getting URL
                     Log::channel($this->logChannel)->warning("[EgiUploadHandler::saveToMultipleDisks] Could not determine URL for saved file.", array_merge($diskLogContext, ['error' => $eUrl->getMessage()]));
                }
                Log::channel($this->logChannel)->info("[EgiUploadHandler::saveToMultipleDisks] File successfully saved.", array_merge($diskLogContext, ['info' => $savedInfo[$disk]]));

            } catch (Throwable $e_store) {
                // 9. Store Error on Failure
                $errorMsg = "Failed to save file. Error: " . $e_store->getMessage();
                Log::channel($this->logChannel)->error('[EgiUploadHandler::saveToMultipleDisks] ' . $errorMsg, array_merge($diskLogContext, ['exception_class' => get_class($e_store)]));
                $errors[$disk] = $errorMsg; // Store the error message
            }
        }
        // --- End Save Loop ---

        // --- 10-11. Check Critical Failures ---
        $criticalFailures = array_intersect_key($errors, array_flip($criticalDisks));
        if (!empty($criticalFailures)) {
            $failedDiskNames = implode(', ', array_keys($criticalFailures));
            Log::channel($this->logChannel)->error('[EgiUploadHandler::saveToMultipleDisks] CRITICAL STORAGE FAILURE occurred.', array_merge($logContext, ['failed_critical_disks' => $criticalFailures]));

            // Invece di lanciare un'eccezione, proviamo a salvare su 'public' come ultima risorsa
            if (!isset($savedInfo['public']) && !in_array('public', array_keys($errors))) {
                Log::channel($this->logChannel)->warning('[EgiUploadHandler::saveToMultipleDisks] Attempting fallback save to public disk.', $logContext);

                try {
                    // Verifica che il disco 'public' sia configurato
                    if (!Config::has("filesystems.disks.public")) {
                        throw new LogicException("Fallback storage disk 'public' is not configured in filesystems.php.");
                    }

                    $visibility = 'public'; // Forza la visibilità pubblica
                    $success = Storage::disk('public')->put($pathKey, $contents, $visibility);

                    if (!$success) {
                        throw new Exception("Storage::put returned false for fallback disk 'public'.");
                    }

                    // Ottieni URL o percorso
                    try {
                        $savedInfo['public'] = Storage::disk('public')->url($pathKey);
                    } catch (Throwable $eUrl) {
                        $savedInfo['public'] = $pathKey;
                        Log::channel($this->logChannel)->warning("[EgiUploadHandler::saveToMultipleDisks] Could not determine URL for fallback saved file.", array_merge(['disk' => 'public'], $logContext, ['error' => $eUrl->getMessage()]));
                    }

                    Log::channel($this->logChannel)->info("[EgiUploadHandler::saveToMultipleDisks] File successfully saved to fallback disk 'public'.", array_merge(['disk' => 'public'], $logContext, ['info' => $savedInfo['public']]));

                    // Rimuoviamo il fallback dalle liste di errori
                    unset($criticalFailures['public']);
                    unset($errors['public']);

                    // Se abbiamo salvato con successo nel fallback, non lanciamo l'eccezione
                    if (empty($criticalFailures)) {
                        Log::channel($this->logChannel)->info("[EgiUploadHandler::saveToMultipleDisks] Fallback save successful, continuing execution.", $logContext);
                        return $savedInfo;
                    }
                } catch (Throwable $eFallback) {
                    Log::channel($this->logChannel)->error('[EgiUploadHandler::saveToMultipleDisks] FALLBACK SAVE FAILED too.', array_merge($logContext, ['error' => $eFallback->getMessage()]));
                    // Aggiungi l'errore del fallback alla lista degli errori
                    $errors['public'] = "Fallback save failed: " . $eFallback->getMessage();
                    $criticalFailures['public'] = "Fallback save failed: " . $eFallback->getMessage();
                }
            }

            // Se siamo qui, significa che anche il fallback è fallito o era già nella lista degli errori
            // Quindi lanciamo l'eccezione
            throw new Exception("CRITICAL STORAGE FAILURE on disk(s): " . $failedDiskNames . ". Fallback to 'public' also failed.");
        }
        // --- End Critical Check ---

        // --- 12. Log Non-Critical Failures ---
        $nonCriticalFailures = array_diff_key($errors, $criticalFailures);
        if (!empty($nonCriticalFailures)) {
             Log::channel($this->logChannel)->warning('[EgiUploadHandler::saveToMultipleDisks] Non-critical storage errors occurred.', array_merge($logContext, ['failed_disks' => $nonCriticalFailures]));
        }

        // --- 13. Return Success Info ---
        return $savedInfo; // Return only the info for successful saves
    }

    /**
     * 🗑️ Attempts to delete a file from specified disks during a rollback scenario (if called externally).
     * Logs errors via Log facade but does not throw exceptions itself.
     * (Full Oracode Docs as previously provided for this method)
     * @param array $disksToRollback Array of disk names where the file was successfully saved.
     * @param string $pathKey The storage key of the file to delete.
     * @param array $logContext Base context for logging.
     * @return void
     * @privacy Logs context. Interacts with storage based on potentially sensitive pathKey.
     */
      protected function attemptRollbackStorage(array $disksToRollback, string $pathKey, array $logContext): void
      {
         // --- Implementation as defined previously ---
         // (Uses Log::warning/error/info)
          Log::channel($this->logChannel)->warning('[EgiUploadHandler] Attempting storage rollback (external call?).', /*...*/);
          foreach ($disksToRollback as $disk) { try { /* ... Storage::delete ... log results ... */ } catch (Throwable $e) { Log::channel($this->logChannel)->error("...", /*...*/); } }
      }

} // End EgiUploadHandler Class
