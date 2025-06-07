<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages - English
    |--------------------------------------------------------------------------
    */

    'dev' => [
        // == Existing Entries ==
        'authentication_error' => 'Unauthenticated access attempt.',
        'ucm_delete_failed' => 'Failed to delete configuration with key :key: :message',
        'undefined_error_code' => 'Undefined error code encountered: :errorCode. Original code was [:_original_code].',
        'invalid_input' => 'Invalid input provided for parameter :param.',
        'invalid_image_structure' => 'The structure of the image file is invalid.',
        'mime_type_not_allowed' => 'The MIME type of the file (:mime) is not allowed.',
        'max_file_size' => 'The file size (:size) exceeds the maximum allowed size (:max_size).',
        'invalid_file_extension' => 'The file has an invalid extension (:extension).',
        'invalid_file_name' => 'Invalid file name received during upload process: :filename.',
        'invalid_file_pdf' => 'The PDF file provided is invalid or corrupted.',
        'virus_found' => 'A virus was detected in the file: :filename.',
        'scan_error' => 'An error occurred during the virus scan for file: :filename.',
        'temp_file_not_found' => 'Temporary file not found at path: :path.',
        'file_not_found' => 'The requested file was not found: :path.',
        'error_getting_presigned_url' => 'An error occurred while retrieving the presigned URL for :object.',
        'error_during_file_upload' => 'An error occurred during the file upload process for :filename.',
        'error_deleting_local_temp_file' => 'Failed to delete the local temporary file: :path.',
        'error_deleting_ext_temp_file' => 'Failed to delete the external temporary file: :path.',
        'unable_to_save_bot_file' => 'Unable to save the file for the bot: :filename.',
        'unable_to_create_directory' => 'Failed to create directory for file upload: :directory.',
        'unable_to_change_permissions' => 'Failed to change permissions for file/directory: :path.',
        'impossible_save_file' => 'It was impossible to save the file: :filename to disk :disk.',
        'error_during_create_egi_record' => 'An error occurred while creating the EGI record in the database.',
        'error_during_file_name_encryption' => 'An error occurred during the file name encryption process.',
        'acl_setting_error' => 'An error occurred while setting the ACL (:acl) for object :object.',
        'imagick_not_available' => 'The Imagick PHP extension is not available or configured correctly.',
        'unexpected_error' => 'An unexpected error occurred in the system. Check logs for details.',
        'generic_server_error' => 'A generic server error occurred. Details: :details',
        'json_error' => 'JSON processing error. Type: :type, Message: :message',
        'fallback_error' => 'An error occurred but no specific error configuration was found for code [:_original_code].',
        'fatal_fallback_failure' => 'FATAL: Fallback configuration missing or invalid. System cannot respond.',
        'ucm_audit_not_found' => 'No audit records found for the given configuration ID: :id.',
        'ucm_duplicate_key' => 'Attempted to create a configuration with a duplicate key: :key.',
        'ucm_create_failed' => 'Failed to create configuration entry: :key. Reason: :reason',
        'ucm_update_failed' => 'Failed to update configuration entry: :key. Reason: :reason',
        'ucm_not_found' => 'Configuration key not found: :key.',
        'invalid_file' => 'Invalid file provided: :reason',
        'invalid_file_validation' => 'File validation failed for field :field. Reason: :reason',
        'error_saving_file_metadata' => 'Failed to save metadata for file ID :file_id. Reason: :reason',
        'server_limits_restrictive' => 'Server limits might be too restrictive. Check :limit_name (:limit_value).',
        'egi_auth_required' => 'Authentication required for EGI upload.',
        'egi_file_input_error' => "Invalid or missing 'file' input. Upload error code: :code",
        'egi_validation_failed' => 'EGI metadata validation failed. Check validation errors in context.',
        'egi_collection_init_error' => 'Critical error initializing default collection for user :user_id.',
        'egi_crypto_error' => 'Failed to encrypt filename: :filename',
        'egi_db_error' => 'Database error processing EGI :egi_id for collection :collection_id.',
        'egi_storage_critical_failure' => 'Critical failure saving EGI :egi_id file to disk(s): :disks',
        'egi_storage_config_error' => "'local' storage disk required for fallback is not configured.",
        'egi_unexpected_error' => 'Unexpected error during EGI processing for file :original_filename.',
        'egi_unauthorized_access' => 'Unauthenticated attempt to access the EGI upload page.',
        // UI Related Errors (developer messages)
        'egi_page_access_notice' => 'EGI upload page accessed successfully by administrator with ID :user_id.',
        'egi_page_rendering_error' => 'Exception during EGI upload page rendering: :exception_message',

        // Validation Related Errors (developer messages)
        'invalid_egi_file' => 'EGI file validation failed with errors: :validation_errors',

        // Processing Related Errors (developer messages)
        'error_during_egi_processing' => 'Error during EGI file processing at stage ":processing_stage": :exception_message',

        // Wallet Related Errors (user messages)
        'wallet_creation_failed' => 'Failed to create wallet for collection :collection_id, user :user_id: :error_message',
        'wallet_quota_check_error' => 'Error checking wallet quota for user :user_id, collection :collection_id: :error_message',
        'wallet_insufficient_quota' => 'User :user_id has insufficient quota for collection :collection_id. Required: mint=:required_mint_quota, rebind=:required_rebind_quota. Available: mint=:current_mint_quota, rebind=:current_rebind_quota.',
        'wallet_address_invalid' => 'Invalid wallet address format provided for user :user_id: :wallet_address',
        'wallet_not_found' => 'Wallet not found for user :user_id and collection :collection_id',
        'wallet_already_exists' => 'Wallet already exists for user :user_id and collection :collection_id with ID :wallet_id',
        'wallet_invalid_secret' => 'Invalid secret key provided for wallet :wallet from IP :ip',
        'wallet_validation_failed' => 'Wallet validation failed. Errors: :errors',
        'wallet_connection_failed' => 'Failed to establish wallet connection. Error: :message',
        'wallet_disconnect_failed' => 'Failed to disconnect wallet. Error: :error',

        // COLLECTION_CREATION_FAILED
        'collection_creation_failed' => 'Failed to create default collection for user :user_id. Error details: :error_details',

        // COLLECTION_FIND_CREATE_FAILED
        'collection_find_create_failed' => 'Failed to find or create collection for user :user_id. Error details: :error_details',

        // User Current Collection Update Errors
        'user_current_collection_update_failed' => 'Critical failure updating current_collection_id for user :user_id to collection :collection_id. Database operation failed: :error_message. This prevents proper user-collection association in FlorenceEGI workflow.',
        'user_current_collection_validation_failed' => 'Validation failed during current collection update for user :user_id and collection :collection_id. Validation type: :validation_type. Error: :validation_error. This indicates data integrity issues that must be resolved.',

        // == New Entries ==
        'authorization_error' => 'Authorization denied for the requested action: :action.',
        'csrf_token_mismatch' => 'CSRF token mismatch detected.',
        'route_not_found' => 'The requested route or resource was not found: :url.',
        'method_not_allowed' => 'HTTP method :method not allowed for this route: :url.',
        'too_many_requests' => 'Too many requests hitting the rate limiter.',
        'database_error' => 'A database query or connection error occurred. Details: :details',
        'record_not_found' => 'The requested database record was not found (Model: :model, ID: :id).',
        'validation_error' => 'Input validation failed. Check context for specific errors.', // Generic dev message
        'utm_load_failed' => 'Failed to load translation file: :file for locale :locale.',
        'utm_invalid_locale' => 'Attempted to use an invalid or unsupported locale: :locale.',
        'uem_email_send_failed' => 'EmailNotificationHandler failed to send notification for :errorCode. Reason: :reason',
        'uem_slack_send_failed' => 'SlackNotificationHandler failed to send notification for :errorCode. Reason: :reason',
        'uem_recovery_action_failed' => 'Recovery action :action failed for error :errorCode. Reason: :reason',
        'user_unauthenticated_access' => 'User unauthenticated: Attempt to access a protected resource without valid authentication. Target Collection ID (if applicable): :target_collection_id. IP: :ip_address.',
        'set_current_collection_forbidden' => 'Forbidden: User ID :user_id attempted to set Collection ID :collection_id as current without authorization. IP: :ip_address.',
        'set_current_collection_failed' => 'Database Error: Failed to update current collection for User ID :user_id to Collection ID :collection_id. Details: :exception_message.',
        'auth_required' => 'Authentication required to perform this action. User not logged in.',
        'auth_required_for_like' => 'User must be authenticated to like items. Current auth status: :status',
        'like_toggle_failed' => 'Failed to toggle like for :resource_type :resource_id. Error: :error',

        // Dev message for reservations sistem
        'reservation_egi_not_available' => 'The EGI with ID :egi_id is not available for reservation. It may be already minted or not published.',
        'reservation_amount_too_low' => 'The offer amount of :amount EUR is below the minimum required for this EGI.',
        'reservation_unauthorized' => 'Unauthorized attempt to reserve EGI :egi_id. User must be authenticated or have a connected wallet.',
        'reservation_certificate_generation_failed' => 'Failed to generate certificate for reservation :reservation_id. Error: :error',
        'reservation_certificate_not_found' => 'Certificate with UUID :uuid not found.',
        'reservation_already_exists' => 'User already has an active reservation for EGI :egi_id.',
        'reservation_cancel_failed' => 'Failed to cancel reservation :id. Error: :error',
        'reservation_unauthorized_cancel' => 'Unauthorized attempt to cancel reservation :id. Only the owner can cancel.',
        'reservation_status_failed' => 'Failed to retrieve reservation status for EGI :egi_id. Error: :error',
        'reservation_unknown_error' => 'An unknown error occurred during the reservation process. Error: :error',

        // Dev message for statistics
        'statistics_calculation_failed' => 'Statistics calculation failed for user :user_id. Context: :error_context. Error: :error_message',
        'icon_not_found' => 'Icon :icon_name with style :style not found in database. Using fallback icon.',
        'icon_retrieval_failed' => 'Failed to retrieve icon :icon_name. Error: :error_message. Using fallback icon.',
        'statistics_cache_clear_failed' => 'Failed to clear statistics cache for user :user_id. Error: :error_message',
        'statistics_summary_failed' => 'Failed to calculate statistics summary for user :user_id. Error: :error_message',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'Collection service error during EGI upload: :error_details. Operation: :operation_id',
        'egi_wallet_service_error' => 'Wallet service error during collection setup: :error_details. Collection ID: :collection_id',
        'egi_role_service_error' => 'UserRole service error during role assignment: :error_details. User ID: :user_id',
        'egi_service_integration_error' => 'EGI services integration error: :error_details. Services: :services_involved',
        'egi_enhanced_authentication_error' => 'Enhanced EGI authentication error: :auth_type failed. Session: :session_data',
        'egi_file_input_validation_error' => 'EGI file input validation error: :validation_error. File: :original_filename',
        'egi_metadata_validation_error' => 'EGI metadata validation error: :validation_errors. Request data: :request_data',
        'egi_data_preparation_error' => 'EGI data preparation error: :error_details. File: :original_filename',
        'egi_record_creation_error' => 'EGI database record creation error: :error_details. Collection: :collection_id',
        'egi_file_storage_error' => 'EGI file storage error: :error_details. Storage disks: :failed_disks',
        'egi_cache_invalidation_error' => 'EGI cache invalidation error: :error_details. Cache keys: :cache_keys',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'Enhanced collection creation error: :error_details. User: :user_id, Name: :collection_name',
        'collection_validation_error' => 'Collection validation error: :validation_error. User: :user_id',
        'collection_limit_exceeded_error' => 'Collection limit exceeded for user :user_id. Current: :current_count, Max: :max_limit',
        'collection_wallet_attachment_failed' => 'Failed to attach wallets to collection :collection_id: :error_details',
        'collection_role_assignment_failed' => 'Failed to assign creator role to user :user_id: :error_details',
        'collection_ownership_mismatch_error' => 'Collection :collection_id ownership mismatch. Owner: :actual_owner, Expected: :expected_owner',
        'collection_current_update_error' => 'Failed to update current_collection_id for user :user_id: :error_details',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Storage disk :disk_name configuration error: :error_details',
        'egi_storage_emergency_fallback_failed' => 'Emergency storage fallback failed: :error_details. All disks failed: :failed_disks',
        'egi_temp_file_read_error' => 'Temporary file :temp_path read error: :error_details',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'Invalid EGI session authentication. Session status: :session_status, User ID: :user_id',
        'egi_wallet_auth_mismatch' => 'Wallet authentication mismatch. Session wallet: :session_wallet, User wallet: :user_wallet',

        // Enhanced Registration Errors
        'enhanced_registration_failed' => 'Enhanced registration with ecosystem setup failed: :error. User ID: :user_id, Collection ID: :collection_id, Components: :partial_creation',
        'registration_user_creation_failed' => 'User creation failed during registration: :error. Email: :email, User type: :user_type',
        'registration_collection_creation_failed' => 'Default collection creation failed during registration: :error. User ID: :user_id, Collection name: :collection_name',
        'registration_wallet_setup_failed' => 'Wallet setup failed during registration: :error. User: :user_id, Collection: :collection_id',
        'registration_role_assignment_failed' => 'Role assignment failed during registration: :error. User: :user_id, User type: :user_type',
        'registration_gdpr_consent_failed' => 'GDPR consent processing failed during registration: :error. User: :user_id, Consents: :consents',
        'registration_ecosystem_setup_incomplete' => 'Ecosystem setup incomplete during registration: :error. User: :user_id, Completed steps: :completed_steps',
        'registration_validation_enhanced_failed' => 'Enhanced registration validation failed: :validation_errors. Request data: :request_data',
        'registration_user_type_invalid' => 'Invalid user type during registration: :user_type. Valid types: creator,mecenate,acquirente,azienda',
        'registration_rate_limit_exceeded' => 'Registration rate limit exceeded. IP: :ip_address, Attempts: :attempts, Time window: :time_window',
        'registration_page_load_error' => 'Registration page load error: :error. IP: :ip_address',
        'permission_based_registration_failed' => 'Error during permission-based registration. Details: :error',
        'algorand_wallet_generation_failed' => 'Unable to generate valid Algorand wallet address. Error: :error',
        'ecosystem_setup_failed' => 'Error during user ecosystem creation (collection, wallets, relationships). Details: :error',
        'user_domain_initialization_failed' => 'Error during user domain initialization (profile, personal_data, etc.). Details: :error',
        'gdpr_consent_processing_failed' => 'Error during GDPR consent processing. Details: :error',
        'role_assignment_failed' => 'Error during role assignment based on user_type. Details: :error',
        'personal_data_view_failed' => 'Si è verificato un errore nel caricamento dei tuoi dati personali. Per favore riprova tra qualche minuto o contatta il supporto se il problema persiste.',
        'personal_data_update_failed' => 'Non è stato possibile salvare le modifiche ai tuoi dati personali. Verifica che tutti i campi siano compilati correttamente e riprova.',
        'personal_data_export_failed' => 'Si è verificato un errore durante l\'esportazione dei tuoi dati. Riprova più tardi o contatta il supporto per assistenza.',
        'personal_data_deletion_failed' => 'Non è stato possibile elaborare la richiesta di cancellazione dei tuoi dati. Ti preghiamo di contattare il nostro supporto per ricevere assistenza immediata.',
        'gdpr_export_rate_limit' => 'Puoi richiedere un\'esportazione dei tuoi dati una volta ogni 30 giorni. La prossima esportazione sarà disponibile tra qualche giorno.',
        'gdpr_violation_attempt' => 'GDPR violation attempt detected. Check consent logic in PersonalDataController, user consent status and UpdatePersonalDataRequest validation.',
    ],

    'user' => [
        // == Existing Entries ==
        'authentication_error' => 'You are not authorized to perform this operation.',
        'scan_error' => 'Unable to verify the file\'s security at this time. Please try again later.',
        'virus_found' => 'The file ":fileName" contains potential threats and has been blocked for your safety.',
        'invalid_file_extension' => 'The file extension is not supported. Allowed extensions are: :allowed_extensions.',
        'max_file_size' => 'The file is too large. The maximum allowed size is :max_size.',
        'invalid_file_pdf' => 'The uploaded PDF is invalid or may be corrupted. Please try again.',
        'mime_type_not_allowed' => 'The uploaded file type is not supported. Allowed types are: :allowed_types.',
        'invalid_image_structure' => 'The uploaded image does not appear to be valid. Try another image.',
        'invalid_file_name' => 'The file name contains invalid characters. Use only letters, numbers, spaces, hyphens, and underscores.',
        'error_getting_presigned_url' => 'A temporary issue occurred while preparing the upload. Please try again.',
        'error_during_file_upload' => 'An error occurred during file upload. Please try again or contact support if the issue persists.',
        'unable_to_save_bot_file' => 'Unable to save the generated file at this time. Please try again later.',
        'unable_to_create_directory' => 'Internal system error while preparing to save. Please contact support.',
        'unable_to_change_permissions' => 'Internal system error while saving the file. Please contact support.',
        'impossible_save_file' => 'Unable to save your file due to a system error. Please try again or contact support.',
        'error_during_create_egi_record' => 'An error occurred while saving the information. Our technical team has been notified.',
        'error_during_file_name_encryption' => 'A security error occurred while processing the file. Please try again.',
        'imagick_not_available' => 'The system is temporarily unable to process images. Contact the administrator if the issue persists.',
        'json_error' => 'An error occurred while processing the data. Check the input data or try again later. [Ref: JSON]',
        'generic_server_error' => 'A server error occurred. Please try again later or contact support if the issue persists. [Ref: SERVER]',
        'file_not_found' => 'The requested file was not found.',
        'unexpected_error' => 'An unexpected error occurred. Our technical team has been notified. Please try again later. [Ref: UNEXPECTED]',
        'error_deleting_local_temp_file' => 'Internal error while cleaning up temporary files. Please contact support.',
        'acl_setting_error' => 'Unable to set the correct permissions for the file. Please try again or contact support.',
        'invalid_input' => 'The provided value for :param is invalid. Please check the input and try again.',
        'temp_file_not_found' => 'A temporary issue occurred with the file :file. Please try again.',
        'error_deleting_ext_temp_file' => 'Internal error while cleaning up external temporary files. Please contact support.',
        'ucm_delete_failed' => 'An error occurred while deleting the configuration. Please try again later.',
        'undefined_error_code' => 'An unexpected error occurred. Please contact support if the issue persists. [Ref: UNDEFINED]',
        'fallback_error' => 'An unexpected system issue occurred. Please try again later or contact support. [Ref: FALLBACK]',
        'fatal_fallback_failure' => 'A critical system error occurred. Please contact support immediately. [Ref: FATAL]',
        'ucm_audit_not_found' => 'No historical information is available for this item.',
        'ucm_duplicate_key' => 'This configuration setting already exists.',
        'ucm_create_failed' => 'Unable to save the new configuration setting. Please try again.',
        'ucm_update_failed' => 'Unable to update the configuration setting. Please try again.',
        'ucm_not_found' => 'The requested configuration setting was not found.',
        'invalid_file' => 'The provided file is invalid. Please check the file and try again.',
        'invalid_file_validation' => 'Please check the file in the :field field. Validation failed.',
        'error_saving_file_metadata' => 'An error occurred while saving the file details. Please try uploading again.',
        'server_limits_restrictive' => 'Server configuration may be preventing this operation. Contact support if the issue persists.',
        'generic_internal_error' => 'An internal error occurred. Our technical team has been notified and is working to resolve it.',
        'egi_auth_required' => 'Please log in to upload an EGI.',
        'egi_file_input_error' => 'Please select a valid file to upload.',
        'egi_validation_failed' => 'Please correct the highlighted fields in the form.',
        'egi_collection_init_error' => 'Unable to prepare your collection. Contact support if the issue persists.',
        'egi_storage_failure' => 'Failed to securely save the EGI file. Please try again or contact support.',
        'egi_unexpected_error' => 'An unexpected error occurred while processing your EGI. Please try again later.',
        'egi_unauthorized_access' => 'Unauthorized access. Please log in.',
        'egi_page_rendering_error' => 'An issue occurred while loading the page. Please try again later or contact support.',
        'invalid_egi_file' => 'The EGI file cannot be processed due to validation errors. Please verify the file format and content.',
        'error_during_egi_processing' => 'An error occurred while processing the EGI file. Our team has been notified and will investigate the issue.',

        // Wallet Creation Errors (user messages)
        'wallet_creation_failed' => 'We encountered a problem setting up the wallet for this collection. Our team has been notified and will resolve this issue.',
        'wallet_insufficient_quota' => 'You do not have sufficient royalty quota available for this operation. Please adjust your royalty values and try again.',
        'wallet_address_invalid' => 'The wallet address provided is not valid. Please check the format and try again.',
        'wallet_not_found' => 'The requested wallet could not be found. Please verify your information and try again.',
        'wallet_already_exists' => 'A wallet is already configured for this collection. Please use the existing wallet or contact support for assistance.',
        'wallet_invalid_secret' => 'The secret key you entered is incorrect. Please try again.',
        'wallet_validation_failed' => 'The wallet address format is invalid. Please check and try again.',
        'wallet_connection_failed' => 'Unable to connect your wallet at this time. Please try again later.',
        'wallet_disconnect_failed' => 'There was a problem disconnecting your wallet. Please refresh the page.',

        // COLLECTION
        'collection_creation_failed' => 'Unable to create your collection. Please try again later or contact support.',
        'collection_find_create_failed' => 'Unable to access your collections. Please try again later.',

        // == New Entries ==
        'authorization_error' => 'You do not have permission to perform this action.',
        'csrf_token_mismatch' => 'Your session has expired or is invalid. Please refresh the page and try again.',
        'route_not_found' => 'The page or resource you requested could not be found.',
        'method_not_allowed' => 'The action you tried to perform is not allowed on this resource.',
        'too_many_requests' => 'You are performing actions too quickly. Please wait a moment and try again.',
        'database_error' => 'A database error occurred. Please try again later or contact support. [Ref: DB]',
        'record_not_found' => 'The item you requested could not be found.',
        'validation_error' => 'Please correct the errors highlighted in the form and try again.', // Generic user message
        'utm_load_failed' => 'The system encountered an issue loading language settings. Functionality may be limited.', // Generic internal error for user
        'utm_invalid_locale' => 'The requested language setting is not available.', // Slightly more specific internal issue
        // Internal UEM failures below generally shouldn't have specific user messages, map to generic ones if needed.
        'uem_email_send_failed' => null, // Use generic_internal_error
        'uem_slack_send_failed' => null, // Use generic_internal_error
        'uem_recovery_action_failed' => null, // Use generic_internal_error
        'user_unauthenticated_access' => 'Authentication required. Please log in to continue.',
        'set_current_collection_forbidden' => 'You do not have permission to access or set this collection as your current one.',
        'set_current_collection_failed' => 'An unexpected error occurred while updating your preferences. Our team has been notified. Please try again later.',
        'auth_required' => 'You must be logged in to perform this action.',
        'auth_required_for_like' => 'You must be connected to like items.',
        'like_toggle_failed' => 'Sorry, we could not process your like request. Please try again.',

        // user messages for reservations system
        'reservation_egi_not_available' => 'This EGI is not available for reservation at the moment.',
        'reservation_amount_too_low' => 'Your offer amount is too low. Please enter a higher amount.',
        'reservation_unauthorized' => 'You need to connect your wallet or log in to make a reservation.',
        'reservation_certificate_generation_failed' => 'We couldn\'t generate your reservation certificate. Our team has been notified.',
        'reservation_certificate_not_found' => 'The requested certificate could not be found.',
        'reservation_already_exists' => 'You already have an active reservation for this EGI.',
        'reservation_cancel_failed' => 'We couldn\'t cancel your reservation. Please try again later.',
        'reservation_unauthorized_cancel' => 'You don\'t have permission to cancel this reservation.',
        'reservation_status_failed' => 'Could not retrieve the reservation status. Please try again later.',
        'reservation_unknown_error' => 'Something went wrong with your reservation. Our team has been notified.',

        // user messages for statistics
        'statistics_calculation_failed' => 'Unable to load your statistics at the moment. Our team has been notified. Please try again later.',
        'icon_not_found' => 'Icon temporarily unavailable. Using default icon.',
        'icon_retrieval_failed' => 'Icon temporarily unavailable. Using default icon.',
        'statistics_cache_clear_failed' => 'Unable to refresh statistics cache. Please try again.',
        'statistics_summary_failed' => 'Unable to load statistics summary. Please try again.',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'An error occurred while managing your collection. Our technical team has been notified.',
        'egi_wallet_service_error' => 'Error during wallet setup for this collection. Please try again in a few minutes.',
        'egi_role_service_error' => 'Error assigning creator permissions. Contact support if the problem persists.',
        'egi_service_integration_error' => 'An internal system error occurred. Our technicians are already investigating.',
        'egi_enhanced_authentication_error' => 'Your session is not valid. Please log in again to your Renaissance.',
        'egi_file_input_validation_error' => 'The file you uploaded is invalid or corrupted. Check the format and try again.',
        'egi_metadata_validation_error' => 'Some entered data is incorrect. Check the highlighted fields and try again.',
        'egi_data_preparation_error' => 'Error processing your file. Verify it\'s a valid image.',
        'egi_record_creation_error' => 'Error creating your EGI. The technical team has been automatically notified.',
        'egi_file_storage_error' => 'Error during secure file storage. Please retry the upload.',
        'egi_cache_invalidation_error' => 'Your EGI has been uploaded, but it may take a few minutes to appear everywhere.',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'We couldn\'t create your collection. Please try again or contact support.',
        'collection_validation_error' => 'Collection data is invalid. Please verify and try again.',
        'collection_limit_exceeded_error' => 'You\'ve reached the maximum collection limit. Contact support to increase it.',
        'collection_wallet_attachment_failed' => 'Collection created, but with wallet configuration issues. Contact support.',
        'collection_role_assignment_failed' => 'Collection created, but with permission issues. Contact support.',
        'collection_ownership_mismatch_error' => 'You don\'t have permission to access this collection.',
        'collection_current_update_error' => 'Error updating your active collection. Please try again.',

        // User Current Collection Update Errors
        'user_current_collection_update_failed' => 'We encountered a critical issue while setting up your collection. Our technical team has been notified and will resolve this immediately. Please try again in a few moments or contact support if the problem persists.',
        'user_current_collection_validation_failed' => 'There was an issue with your collection selection. Please ensure you have the proper permissions for this collection and try again. If you continue to experience problems, please contact our support team.',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Storage system configuration problem. The technical team has been notified.',
        'egi_storage_emergency_fallback_failed' => 'Critical storage system error. Technicians are investigating.',
        'egi_temp_file_read_error' => 'We can\'t read the file you uploaded. Try again with a different file.',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'Your session has expired. Reconnect your wallet to continue.',
        'egi_wallet_auth_mismatch' => 'The connected wallet doesn\'t match your account. Verify the connection.',

        // Enhanced Registration Errors
        'enhanced_registration_failed' => 'An error occurred while setting up your account in the Digital Renaissance. Our team has been notified.',
        'registration_user_creation_failed' => 'We couldn\'t create your account. Please verify the entered data and try again.',
        'registration_collection_creation_failed' => 'Your account was created, but we couldn\'t set up your collection. Please contact support.',
        'registration_wallet_setup_failed' => 'Registration is almost complete, but there are issues with wallet configuration. Support will contact you soon.',
        'registration_role_assignment_failed' => 'Registration is almost complete, but there are issues with your account permissions. Support will help you.',
        'registration_gdpr_consent_failed' => 'Error saving your privacy preferences. Please try again or contact support.',
        'registration_ecosystem_setup_incomplete' => 'Registration was not completed fully. Our team is checking and will contact you.',
        'registration_validation_enhanced_failed' => 'Some entered data is incorrect. Check the highlighted fields and try again.',
        'registration_user_type_invalid' => 'The selected role is not valid. Choose between Creator, Mecenate, Purchaser, or Business.',
        'registration_rate_limit_exceeded' => 'Too many registration requests. Please try again in a few minutes.',
        'registration_page_load_error' => 'Error loading the registration page. Please reload the page.',
        'permission_based_registration_failed_user' => 'An error occurred during registration. Please try again or contact support if the problem persists.',
        'algorand_wallet_generation_failed_user' => 'Error creating digital wallet. Please try registration again.',
        'ecosystem_setup_failed_user' => 'Registration completed, but there was an error in initial setup. You can complete setup from your profile.',
        'user_domain_initialization_failed_user' => 'Registration completed successfully! Some profile sections may require additional configuration.',
        'gdpr_consent_processing_failed_user' => 'Error processing privacy consents. Please verify your choices and try again.',
        'role_assignment_failed_user' => 'Error in account type configuration. Please contact support.',
        'personal_data_view_failed' => 'An error occurred while loading your personal data. Please try again in a few minutes or contact support if the problem persists.',
        'personal_data_update_failed' => 'Unable to save changes to your personal data. Please verify that all fields are filled correctly and try again.',
        'personal_data_export_failed' => 'An error occurred while exporting your data. Please try again later or contact support for assistance.',
        'personal_data_deletion_failed' => 'Unable to process your data deletion request. Please contact our support team for immediate assistance.',
        'gdpr_export_rate_limit' => 'You can request a data export once every 30 days. Your next export will be available in a few days.',
        'gdpr_violation_attempt' => 'You cannot update your personal data without providing appropriate consent. Please accept the data processing terms to continue.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'An error has occurred. Please try again later or contact support.',
];
