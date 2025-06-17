<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Ultra Error Manager Configuration
    |--------------------------------------------------------------------------
    |
    | Defines error types, handlers, default behaviors, and specific error codes.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Handlers
    |--------------------------------------------------------------------------
    | Handlers automatically registered. Order can matter for some logic.
    | Assumes handlers have been refactored for DI.
    */
    'default_handlers' => [
        // Order Suggestion: Log first, then notify, then prepare UI/recovery
        \Ultra\ErrorManager\Handlers\LogHandler::class,
        \Ultra\ErrorManager\Handlers\DatabaseLogHandler::class, // Log to DB
        \Ultra\ErrorManager\Handlers\EmailNotificationHandler::class, // Notify Devs
        \Ultra\ErrorManager\Handlers\SlackNotificationHandler::class, // Notify Slack
        \Ultra\ErrorManager\Handlers\UserInterfaceHandler::class, // Prepare UI flash messages
        \Ultra\ErrorManager\Handlers\RecoveryActionHandler::class, // Attempt recovery
        // Simulation handler (conditionally added by Service Provider if not production)
        // \Ultra\ErrorManager\Handlers\ErrorSimulationHandler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    */
    'email_notification' => [
        'enabled' => env('ERROR_EMAIL_NOTIFICATIONS_ENABLED', true),
        'to' => env('ERROR_EMAIL_RECIPIENT', 'devteam@example.com'),
        'from' => [ /* ... */ ],
        'subject_prefix' => env('ERROR_EMAIL_SUBJECT_PREFIX', '[UEM Error] '),

        // --- NUOVE OPZIONI GDPR ---
        'include_ip_address' => env('ERROR_EMAIL_INCLUDE_IP', false),        // Default: NO
        'include_user_agent' => env('ERROR_EMAIL_INCLUDE_UA', false),       // Default: NO
        'include_user_details' => env('ERROR_EMAIL_INCLUDE_USER', false),    // Default: NO (Include ID, Name, Email)
        'include_context' => env('ERROR_EMAIL_INCLUDE_CONTEXT', true),       // Default: YES (ma verrà sanitizzato)
        'include_trace' => env('ERROR_EMAIL_INCLUDE_TRACE', false),         // Default: NO (Le tracce possono essere lunghe/sensibili)
        'context_sensitive_keys' => [ // Lista specifica per email, può differire da DB
            'password', 'secret', 'token', 'auth', 'key', 'credentials', 'authorization',
            'php_auth_user', 'php_auth_pw', 'credit_card', 'creditcard', 'card_number',
            'cvv', 'cvc', 'api_key', 'secret_key', 'access_token', 'refresh_token',
            // Aggiungere chiavi specifiche se necessario
        ],
        'trace_max_lines' => env('ERROR_EMAIL_TRACE_LINES', 30), // Limita lunghezza trace inviata
    ],

     /*
    |--------------------------------------------------------------------------
    | Slack Notification Settings
    |--------------------------------------------------------------------------
    */
     'slack_notification' => [
        'enabled' => env('ERROR_SLACK_NOTIFICATIONS_ENABLED', false),
        'webhook_url' => env('ERROR_SLACK_WEBHOOK_URL'),
        'channel' => env('ERROR_SLACK_CHANNEL', '#error-alerts'),
        'username' => env('ERROR_SLACK_USERNAME', 'UEM Error Bot'),
        'icon_emoji' => env('ERROR_SLACK_ICON', ':boom:'),

        // --- NUOVE OPZIONI GDPR ---
        'include_ip_address' => env('ERROR_SLACK_INCLUDE_IP', false),       // Default: NO
        'include_user_details' => env('ERROR_SLACK_INCLUDE_USER', false),   // Default: NO (Just ID maybe?)
        'include_context' => env('ERROR_SLACK_INCLUDE_CONTEXT', true),      // Default: YES (sanitized)
        'include_trace_snippet' => env('ERROR_SLACK_INCLUDE_TRACE', false), // Default: NO (Trace can be very long for Slack)
        'context_sensitive_keys' => [ // Lista per Slack
            'password', 'secret', 'token', 'auth', 'key', 'credentials', 'authorization',
            'php_auth_user', 'php_auth_pw', 'credit_card', 'creditcard', 'card_number',
            'cvv', 'cvc', 'api_key', 'secret_key', 'access_token', 'refresh_token',
            // Aggiungere chiavi specifiche se necessario
        ],
        'context_max_length' => env('ERROR_SLACK_CONTEXT_LENGTH', 1500), // Limit context length in Slack message
        'trace_max_lines' => env('ERROR_SLACK_TRACE_LINES', 10), // Limit trace lines in Slack message
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration (UEM Specific)
    |--------------------------------------------------------------------------
    | Settings affecting logging handlers (LogHandler, DatabaseLogHandler).
    */
    'logging' => [
         // Note: Main log channel is configured in ULM, not here.
         // 'channel' => env('ERROR_LOG_CHANNEL', 'stack'), // Redundant if using ULM properly
        'detailed_context_in_log' => env('ERROR_LOG_DETAILED_CONTEXT', true), // Affects standard LogHandler context
    ],

     /*
     |--------------------------------------------------------------------------
     | Database Logging Configuration
     |--------------------------------------------------------------------------
     */
     'database_logging' => [
         'enabled' => env('ERROR_DB_LOGGING_ENABLED', true), // Enable DB logging by default
         'include_trace' => env('ERROR_DB_LOG_INCLUDE_TRACE', true), // Log stack traces to DB
         'max_trace_length' => env('ERROR_DB_LOG_MAX_TRACE_LENGTH', 10000), // Max chars for DB trace

         /**
         * 🛡️ Sensitive Keys for Context Redaction.
         * Keys listed here (case-insensitive) will have their values
         * replaced with '[REDACTED]' before the context is saved to the database log.
         * Add any application-specific keys containing PII or secrets.
         */
        'sensitive_keys' => [
            // Defaults (from DatabaseLogHandler)
            'password',
            'secret',
            'token',
            'auth',
            'key',
            'credentials',
            'authorization',
            'php_auth_user',
            'php_auth_pw',
            'credit_card',
            'creditcard', // Variations
            'card_number',
            'cvv',
            'cvc',
            'api_key',
            'secret_key',
            'access_token',
            'refresh_token',
            // Aggiungi qui chiavi specifiche di FlorenceEGI se necessario
            // 'wallet_private_key',
            // 'user_personal_identifier',
            // 'financial_details',
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | UI Error Display
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'default_display_mode' => env('ERROR_UI_DEFAULT_DISPLAY', 'sweet-alert'), // 'div', 'sweet-alert', 'toast'
        'show_error_codes' => env('ERROR_UI_SHOW_CODES', false), // Show codes like [E_...] to users?
        'generic_error_message' => 'error-manager::errors.user.generic_error', // Translation key for generic messages
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Type Definitions
    |--------------------------------------------------------------------------
    | Defines behavior associated with error severity levels.
    */
    'error_types' => [
        'critical' => [
            'log_level' => 'critical', // Maps to PSR LogLevel
            'notify_team' => true, // Default: Should Email/Slack handlers trigger?
            'http_status' => 500, // Default HTTP status
        ],
        'error' => [
            'log_level' => 'error',
            'notify_team' => false,
            'http_status' => 400, // Often client errors or recoverable server issues
        ],
        'warning' => [
            'log_level' => 'warning',
            'notify_team' => false,
            'http_status' => 400, // Often user input validation
        ],
        'notice' => [
            'log_level' => 'notice',
            'notify_team' => false,
            'http_status' => 200, // Not typically an "error" status
        ],
        // Consider adding 'info' if needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocking Level Definitions
    |--------------------------------------------------------------------------
    | Defines impact on application flow.
    */
    'blocking_levels' => [
        'blocking' => [
            'terminate_request' => true, // Should middleware stop request propagation? (UEM itself doesn't enforce this directly)
            'clear_session' => false, // Example: Should session be cleared?
        ],
        'semi-blocking' => [
            'terminate_request' => false, // Allows request to potentially complete
            'flash_session' => true, // Should UI handler flash message?
        ],
        'not' => [ // Non-blocking
            'terminate_request' => false,
            'flash_session' => true, // Still might want to inform user
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Error Configuration
    |--------------------------------------------------------------------------
    | Used if 'UNDEFINED_ERROR_CODE' itself is not defined. Should always exist.
    */
    'fallback_error' => [
        'type' => 'critical', // Treat any fallback situation as critical
        'blocking' => 'blocking',
        'dev_message_key' => 'error-manager::errors.dev.fatal_fallback_failure', // Use the fatal key here
        'user_message_key' => 'error-manager::errors.user.fatal_fallback_failure',
        'http_status_code' => 500,
        'devTeam_email_need' => true,
        'msg_to' => 'sweet-alert', // Show prominent alert
        'notify_slack' => true, // Also notify slack if configured
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Definitions (Code => Configuration)
    |--------------------------------------------------------------------------
    */
    'errors' => [

        // ====================================================
        // META / Generic Fallbacks
        // ====================================================
        'UNDEFINED_ERROR_CODE' => [
            'type' => 'critical',
            'blocking' => 'blocking', // Treat undefined code as blocking
            'dev_message_key' => 'error-manager::errors.dev.undefined_error_code',
            'user_message_key' => 'error-manager::errors.user.undefined_error_code',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true, // Notify Slack too
            'msg_to' => 'sweet-alert',
        ],
        'FATAL_FALLBACK_FAILURE' => [ // Only used if fallback_error itself fails
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.fatal_fallback_failure',
            'user_message_key' => 'error-manager::errors.user.fatal_fallback_failure',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        // FALLBACK_ERROR is defined above in 'fallback_error' key
        'UNEXPECTED_ERROR' => [ // Generic catch-all from middleware mapping
            'type' => 'critical',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.unexpected_error',
            'user_message_key' => 'error-manager::errors.user.unexpected_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'GENERIC_SERVER_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.generic_server_error',
            'user_message_key' => 'error-manager::errors.user.generic_server_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'JSON_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.json_error',
            'user_message_key' => 'error-manager::errors.user.json_error',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
         'INVALID_INPUT' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_input',
            'user_message_key' => 'error-manager::errors.user.invalid_input',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        // ====================================================
        // Authentication & Authorization Errors (Mapped from Middleware)
        // ====================================================
        'AUTHENTICATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.authentication_error',
            'user_message_key' => 'error-manager::errors.user.authentication_error',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert', // Or redirect
        ],
        'AUTHORIZATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.authorization_error',
            'user_message_key' => 'error-manager::errors.user.authorization_error',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
         'CSRF_TOKEN_MISMATCH' => [
             'type' => 'error',
             'blocking' => 'blocking',
             'dev_message_key' => 'error-manager::errors.dev.csrf_token_mismatch',
             'user_message_key' => 'error-manager::errors.user.csrf_token_mismatch',
             'http_status_code' => 419,
             'devTeam_email_need' => false,
             'notify_slack' => false,
             'msg_to' => 'sweet-alert', // Inform user to refresh
         ],

        // ====================================================
        // Routing & Request Errors (Mapped from Middleware)
        // ====================================================
         'ROUTE_NOT_FOUND' => [
             'type' => 'error',
             'blocking' => 'blocking',
             'dev_message_key' => 'error-manager::errors.dev.route_not_found',
             'user_message_key' => 'error-manager::errors.user.route_not_found',
             'http_status_code' => 404,
             'devTeam_email_need' => false,
             'notify_slack' => false,
             'msg_to' => 'log-only', // Let Laravel handle 404 page
         ],
         'METHOD_NOT_ALLOWED' => [
             'type' => 'error',
             'blocking' => 'blocking',
             'dev_message_key' => 'error-manager::errors.dev.method_not_allowed',
             'user_message_key' => 'error-manager::errors.user.method_not_allowed',
             'http_status_code' => 405,
             'devTeam_email_need' => false,
             'notify_slack' => false,
             'msg_to' => 'log-only', // Let Laravel handle 405 page
         ],
         'TOO_MANY_REQUESTS' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.too_many_requests',
            'user_message_key' => 'error-manager::errors.user.too_many_requests',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => true, // Might indicate an attack or config issue
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // Database / Model Errors (Mapped + Specifics)
        // ====================================================
        'DATABASE_ERROR' => [
             'type' => 'critical',
             'blocking' => 'blocking',
             'dev_message_key' => 'error-manager::errors.dev.database_error',
             'user_message_key' => 'error-manager::errors.user.database_error',
             'http_status_code' => 500,
             'devTeam_email_need' => true,
             'notify_slack' => true,
             'msg_to' => 'sweet-alert',
         ],
         'RECORD_NOT_FOUND' => [
             'type' => 'error', // Or warning depending on context
             'blocking' => 'blocking', // Usually stops the current action
             'dev_message_key' => 'error-manager::errors.dev.record_not_found',
             'user_message_key' => 'error-manager::errors.user.record_not_found',
             'http_status_code' => 404,
             'devTeam_email_need' => false,
             'notify_slack' => false,
             'msg_to' => 'sweet-alert',
         ],
        'ERROR_DURING_CREATE_EGI_RECORD' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.error_during_create_egi_record',
            'user_message_key' => 'error-manager::errors.user.error_during_create_egi_record',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // Validation Errors (Mapped + Specifics)
        // ====================================================
        'VALIDATION_ERROR' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.validation_error',
            'user_message_key' => 'error-manager::errors.user.validation_error',
            'http_status_code' => 422,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div', // Usually shown inline with form fields
        ],
        'INVALID_IMAGE_STRUCTURE' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_image_structure',
            'user_message_key' => 'error-manager::errors.user.invalid_image_structure',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'MIME_TYPE_NOT_ALLOWED' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.mime_type_not_allowed',
            'user_message_key' => 'error-manager::errors.user.mime_type_not_allowed',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'MAX_FILE_SIZE' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.max_file_size',
            'user_message_key' => 'error-manager::errors.user.max_file_size',
            'http_status_code' => 413, // Payload Too Large
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'INVALID_FILE_EXTENSION' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_file_extension',
            'user_message_key' => 'error-manager::errors.user.invalid_file_extension',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'INVALID_FILE_NAME' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_file_name',
            'user_message_key' => 'error-manager::errors.user.invalid_file_name',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'INVALID_FILE_PDF' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_file_pdf',
            'user_message_key' => 'error-manager::errors.user.invalid_file_pdf',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
         'INVALID_FILE' => [ // More generic file issue?
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_file',
            'user_message_key' => 'error-manager::errors.user.invalid_file',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'INVALID_FILE_VALIDATION' => [ // Specific validation context
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_file_validation',
            'user_message_key' => 'error-manager::errors.user.invalid_file_validation',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        // ====================================================
        // UUM (Upload) Related Errors (Esistenti - verified/adjusted)
        // ====================================================
        'VIRUS_FOUND' => [
            'type' => 'error', // Changed from warning, this is a security event
            'blocking' => 'blocking', // Stop processing this file
            'dev_message_key' => 'error-manager::errors.dev.virus_found',
            'user_message_key' => 'error-manager::errors.user.virus_found',
            'http_status_code' => 422, // Unprocessable Entity
            'devTeam_email_need' => false, // May become true if frequent/unexpected
            'notify_slack' => true, // Good to know about virus alerts
            'msg_to' => 'sweet-alert',
        ],
        'SCAN_ERROR' => [
            'type' => 'warning', // Scan failed, not necessarily insecure
            'blocking' => 'semi-blocking', // Allow retry potentially
            'dev_message_key' => 'error-manager::errors.dev.scan_error',
            'user_message_key' => 'error-manager::errors.user.scan_error',
            'http_status_code' => 500, // Service unavailable?
            'devTeam_email_need' => true, // If scanner service is down
            'notify_slack' => true,
            'msg_to' => 'div',
            'recovery_action' => 'retry_scan', // Defined recovery
        ],
        'TEMP_FILE_NOT_FOUND' => [
            'type' => 'error', // Changed from warning, indicates logic flaw
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.temp_file_not_found',
            'user_message_key' => 'error-manager::errors.user.temp_file_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => true, // Investigate why temp file is missing
            'notify_slack' => true,
            'msg_to' => 'div',
        ],
        'FILE_NOT_FOUND' => [ // Generic file not found
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.file_not_found',
            'user_message_key' => 'error-manager::errors.user.file_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'ERROR_GETTING_PRESIGNED_URL' => [
            'type' => 'error', // Changed from critical, maybe recoverable network issue
            'blocking' => 'semi-blocking', // Allow retry
            'dev_message_key' => 'error-manager::errors.dev.error_getting_presigned_url',
            'user_message_key' => 'error-manager::errors.user.error_getting_presigned_url',
            'http_status_code' => 500,
            'devTeam_email_need' => true, // If storage provider is down
            'notify_slack' => true,
            'msg_to' => 'div',
            'recovery_action' => 'retry_presigned',
        ],
        'ERROR_DURING_FILE_UPLOAD' => [
            'type' => 'error', // Changed from critical, network issues happen
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.error_during_file_upload',
            'user_message_key' => 'error-manager::errors.user.error_during_file_upload',
            'http_status_code' => 500, // Or maybe client-related? Needs context.
            'devTeam_email_need' => true, // If persistent
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
            'recovery_action' => 'retry_upload',
        ],
        'ERROR_DELETING_LOCAL_TEMP_FILE' => [
            'type' => 'warning', // Changed from critical, cleanup can be retried
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.error_deleting_local_temp_file',
            'user_message_key' => null, // Internal issue
            'http_status_code' => 500,
            'devTeam_email_need' => false, // Unless very frequent
            'notify_slack' => false,
            'msg_to' => 'log-only',
            'recovery_action' => 'schedule_cleanup',
        ],
        'ERROR_DELETING_EXT_TEMP_FILE' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.error_deleting_ext_temp_file',
            'user_message_key' => null,
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only',
            'recovery_action' => 'schedule_cleanup',
        ],
        'UNABLE_TO_SAVE_BOT_FILE' => [
            'type' => 'critical', // If bot relies on this, it's critical
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.unable_to_save_bot_file',
            'user_message_key' => 'error-manager::errors.user.unable_to_save_bot_file',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'div',
        ],
        'UNABLE_TO_CREATE_DIRECTORY' => [
            'type' => 'critical', // Filesystem permission issue?
            'blocking' => 'blocking', // Uploads likely blocked
            'dev_message_key' => 'error-manager::errors.dev.unable_to_create_directory',
            'user_message_key' => 'error-manager::errors.user.generic_internal_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'log-only',
            'recovery_action' => 'create_temp_directory',
        ],
        'UNABLE_TO_CHANGE_PERMISSIONS' => [
            'type' => 'critical',
            'blocking' => 'not', // May not block immediately but needs fixing
            'dev_message_key' => 'error-manager::errors.dev.unable_to_change_permissions',
            'user_message_key' => 'error-manager::errors.user.generic_internal_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'log-only',
        ],
        'IMPOSSIBLE_SAVE_FILE' => [
            'type' => 'critical', // File saving failed entirely
            'blocking' => 'semi-blocking', // User needs to know
            'dev_message_key' => 'error-manager::errors.dev.impossible_save_file',
            'user_message_key' => 'error-manager::errors.user.impossible_save_file',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
         'ERROR_SAVING_FILE_METADATA' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.error_saving_file_metadata',
            'user_message_key' => 'error-manager::errors.user.error_saving_file_metadata',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'div',
            'recovery_action' => 'retry_metadata_save',
        ],
        'ACL_SETTING_ERROR' => [
            'type' => 'critical', // Security related
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.acl_setting_error',
            'user_message_key' => 'error-manager::errors.user.acl_setting_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'div',
        ],
        'ERROR_DURING_FILE_NAME_ENCRYPTION' => [
            'type' => 'critical', // Security related
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.error_during_file_name_encryption',
            'user_message_key' => 'error-manager::errors.user.error_during_file_name_encryption',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'div',
        ],

        // ====================================================
        // UCM (Config) Related Errors (Esistenti - verified/adjusted)
        // ====================================================
        'UCM_DUPLICATE_KEY' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.ucm_duplicate_key',
            'user_message_key' => 'error-manager::errors.user.ucm_duplicate_key',
            'http_status_code' => 422, // Unprocessable entity seems appropriate
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'UCM_CREATE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.ucm_create_failed',
            'user_message_key' => 'error-manager::errors.user.ucm_create_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'UCM_UPDATE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.ucm_update_failed',
            'user_message_key' => 'error-manager::errors.user.ucm_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'UCM_NOT_FOUND' => [
            'type' => 'error', // Could be expected if key is optional
            'blocking' => 'not', // Changed to non-blocking, logic should handle null
            'dev_message_key' => 'error-manager::errors.dev.ucm_not_found',
            'user_message_key' => 'error-manager::errors.user.ucm_not_found', // Maybe a generic "setting not found"?
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only', // Log it, but don't bother user usually
        ],
        'UCM_DELETE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking', // Failed to delete, might leave inconsistent state
            'dev_message_key' => 'error-manager::errors.dev.ucm_delete_failed',
            'user_message_key' => 'error-manager::errors.user.ucm_delete_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'UCM_AUDIT_NOT_FOUND' => [
            'type' => 'notice', // Changed from info, less noisy
            'blocking' => 'not', // Non-blocking
            'dev_message_key' => 'error-manager::errors.dev.ucm_audit_not_found',
            'user_message_key' => 'error-manager::errors.user.ucm_audit_not_found',
            'http_status_code' => 404, // Consistent not found
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div', // Show in context if needed
        ],

        // ====================================================
        // UTM (Translation) Errors (Nuovi - Verified/Adjusted)
        // ====================================================
        'UTM_LOAD_FAILED' => [
            'type' => 'error', // Changed from warning, failure to load lang file is an error
            'blocking' => 'not', // But might fallback to default language
            'dev_message_key' => 'error-manager::errors.dev.utm_load_failed',
            'user_message_key' => null, // Internal issue, user sees fallback lang
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true, // Let devs know quickly
            'msg_to' => 'log-only',
        ],
        'UTM_INVALID_LOCALE' => [
            'type' => 'warning', // Invalid locale requested
            'blocking' => 'not', // System likely falls back to default
            'dev_message_key' => 'error-manager::errors.dev.utm_invalid_locale',
            'user_message_key' => null, // User sees default language content
            'http_status_code' => 400, // Bad request potentially (depending on source)
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only',
        ],

        // ====================================================
        // UEM Internal Handler Errors (Nuovi - Verified/Adjusted)
        // ====================================================
        'UEM_EMAIL_SEND_FAILED' => [
            'type' => 'critical', // Changed from error - failure to notify IS critical
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.uem_email_send_failed',
            'user_message_key' => null,
            'http_status_code' => 500,
            'devTeam_email_need' => false, // Avoid loop - logged by handler
            'notify_slack' => true, // Try alternative notification
            'msg_to' => 'log-only',
        ],
        'UEM_SLACK_SEND_FAILED' => [
            'type' => 'critical', // Changed from error
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.uem_slack_send_failed',
            'user_message_key' => null,
            'http_status_code' => 500,
            'devTeam_email_need' => true, // Try email if slack failed
            'notify_slack' => false, // Avoid loop
            'msg_to' => 'log-only',
        ],
        'UEM_RECOVERY_ACTION_FAILED' => [
            'type' => 'error', // Changed from warning - recovery failure IS an error
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.uem_recovery_action_failed',
            'user_message_key' => null, // User sees original error message
            'http_status_code' => 500,
            'devTeam_email_need' => true, // Need to know why recovery failed
            'notify_slack' => true,
            'msg_to' => 'log-only',
        ],

        'UEM_USER_UNAUTHENTICATED' => [
            'type' => 'auth', // O 'error' se preferisci
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.user_unauthenticated_access', // Chiave per messaggio tecnico
            'user_message_key' => 'error-manager::errors.user.user_unauthenticated_access', // Chiave per messaggio utente
            'http_status_code' => 401, // Unauthorized
            'devTeam_email_need' => false, // A meno che non sia un fallimento inaspettato del middleware
            'notify_slack' => false,
            'msg_to' => 'json', // Solitamente per API
        ],

        'UEM_SET_CURRENT_COLLECTION_FORBIDDEN' => [
            'type' => 'security', // O 'error'
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.set_current_collection_forbidden',
            'user_message_key' => 'error-manager::errors.user.set_current_collection_forbidden',
            'http_status_code' => 403, // Forbidden
            'devTeam_email_need' => true, // Potrebbe indicare un tentativo di accesso anomalo
            'notify_slack' => true,
            'msg_to' => 'json',
        ],

        'UEM_SET_CURRENT_COLLECTION_FAILED' => [
            'type' => 'critical', // Un fallimento nel salvare il DB è solitamente critico
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.set_current_collection_failed',
            'user_message_key' => 'error-manager::errors.user.set_current_collection_failed',
            'http_status_code' => 500, // Internal Server Error
            'devTeam_email_need' => true, // Notifica sempre per errori 500
            'notify_slack' => true,
            'msg_to' => 'json',
        ],

        // ====================================================
        // EGI Upload Specific Errors
        // ====================================================
        'EGI_AUTH_REQUIRED' => [ // User not authenticated attempting upload
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_auth_required', // "Authentication required for EGI upload."
            'user_message_key' => 'error-manager::errors.user.egi_auth_required', // "Please log in to upload an EGI."
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert', // Or redirect to login
        ],
        'EGI_UNAUTHORIZED_ACCESS' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_unauthorized_access',
            'user_message_key' => 'error-manager::errors.user.egi_unauthorized_access',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only', // Redirect diretto senza SweetAlert
        ],
        'EGI_FILE_INPUT_ERROR' => [ // Problem with the 'file' part of the request (missing, invalid upload)
            'type' => 'warning',
            'blocking' => 'blocking', // Stop the process
            'dev_message_key' => 'error-manager::errors.dev.egi_file_input_error', // "Invalid or missing 'file' input. Upload error code: :code"
            'user_message_key' => 'error-manager::errors.user.egi_file_input_error', // "Please select a valid file to upload."
            'http_status_code' => 400, // Bad Request
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div', // Show near the file input
        ],

          'EGI_PAGE_ACCESS_NOTICE' => [
            'type' => 'notice',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.egi_page_access_notice',
            'user_message_key' => null, // No user message needed
            'http_status_code' => 200,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only', // Solo log, nessuna visualizzazione all'utente
        ],

        'EGI_PAGE_RENDERING_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_page_rendering_error',
            'user_message_key' => 'error-manager::errors.user.egi_page_rendering_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true, // Notifica il team via email
            'notify_slack' => true, // Notifica anche su Slack se configurato
            'msg_to' => 'sweet-alert', // Mostra un alert all'utente
        ],

        // ====================================================
        // Errori specifici per la validazione EGI
        // ====================================================
        'INVALID_EGI_FILE' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.invalid_egi_file',
            'user_message_key' => 'error-manager::errors.user.invalid_egi_file',
            'http_status_code' => 422, // Unprocessable Entity
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div', // Mostra errori di validazione in un div
        ],


        // ====================================================
        // Errori specifici per l'elaborazione EGI
        // ====================================================

        'ERROR_DURING_EGI_PROCESSING' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.error_during_egi_processing',
            'user_message_key' => 'error-manager::errors.user.error_during_egi_processing',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_VALIDATION_FAILED' => [ // Metadata validation failed ($request->validate())
            'type' => 'warning',
            'blocking' => 'semi-blocking', // Allow user to correct and resubmit
            'dev_message_key' => 'error-manager::errors.dev.egi_validation_failed', // "EGI metadata validation failed." (Details in context/response)
            'user_message_key' => 'error-manager::errors.user.egi_validation_failed', // "Please correct the highlighted fields."
            'http_status_code' => 422, // Unprocessable Entity (standard for validation errors)
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div', // Display errors near form fields
        ],
        'EGI_COLLECTION_INIT_ERROR' => [ // Failure during findOrCreateDefaultCollection (critical part)
            'type' => 'critical',
            'blocking' => 'blocking', // Cannot proceed without collection context
            'dev_message_key' => 'error-manager::errors.dev.egi_collection_init_error', // "Critical error initializing default collection for user :user_id."
            'user_message_key' => 'error-manager::errors.user.egi_collection_init_error', // "Could not prepare your collection. Please contact support."
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
         'EGI_CRYPTO_ERROR' => [ // Failure during filename encryption
            'type' => 'critical', // Security / Data integrity related
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_crypto_error', // "Failed to encrypt filename: :filename"
            'user_message_key' => 'error-manager::errors.user.generic_internal_error', // Generic user message
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'EGI_DB_ERROR' => [ // Specific database error during Egi model save/update
            'type' => 'critical',
            'blocking' => 'blocking', // Transaction will likely rollback
            'dev_message_key' => 'error-manager::errors.dev.egi_db_error', // "Database error processing EGI :egi_id for collection :collection_id."
            'user_message_key' => 'error-manager::errors.user.generic_internal_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'EGI_STORAGE_CRITICAL_FAILURE' => [ // Failure saving to a critical disk
            'type' => 'critical',
            'blocking' => 'blocking', // Transaction will likely rollback
            'dev_message_key' => 'error-manager::errors.dev.egi_storage_critical_failure', // "Critical failure saving EGI :egi_id file to disk(s): :disks"
            'user_message_key' => 'error-manager::errors.user.egi_storage_failure', // "Failed to securely store the EGI file. Please try again or contact support."
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'EGI_STORAGE_CONFIG_ERROR' => [ // Fallback disk 'local' is not configured
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_storage_config_error', // "'local' storage disk required for fallback is not configured."
            'user_message_key' => 'error-manager::errors.user.generic_internal_error', // Config error
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'log-only', // Or sweet-alert if needed
        ],
        'EGI_UNEXPECTED_ERROR' => [ // Catch-all for other unexpected errors in the EGI handler flow
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_unexpected_error', // "Unexpected error during EGI processing for file :original_filename."
            'user_message_key' => 'error-manager::errors.user.egi_unexpected_error', // "An unexpected error occurred while processing your EGI."
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // System / Environment Errors (Esistenti - Verified/Adjusted)
        // ====================================================
        'IMAGICK_NOT_AVAILABLE' => [
            'type' => 'critical',
            'blocking' => 'blocking', // If image processing is core
            'dev_message_key' => 'error-manager::errors.dev.imagick_not_available',
            'user_message_key' => 'error-manager::errors.user.imagick_not_available', // Inform user nicely
            'http_status_code' => 500, // Misconfiguration
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'SERVER_LIMITS_RESTRICTIVE' => [ // Example: PHP memory limit, upload size etc. detected low
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.server_limits_restrictive', // E.g., "PHP memory_limit is low (:limit)"
            'user_message_key' => null, // Not a user error
            'http_status_code' => 500, // Reflects potential future issue
            'devTeam_email_need' => true, // Ops/Dev team needs to adjust server config
            'notify_slack' => true,
            'msg_to' => 'log-only',
        ],

        // ====================================================
        // Errori specifici per la creazione e gestione Wallet
        // ====================================================

        'WALLET_CREATION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_creation_failed',
            'user_message_key' => 'error-manager::errors.user.wallet_creation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'WALLET_QUOTA_CHECK_ERROR' => [
            'type' => 'error',
            'blocking' => 'not', // Non-blocking, just log
            'dev_message_key' => 'error-manager::errors.dev.wallet_quota_check_error',
            'user_message_key' => null, // No user-visible message needed
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only',
        ],

        'WALLET_INSUFFICIENT_QUOTA' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_insufficient_quota',
            'user_message_key' => 'error-manager::errors.user.wallet_insufficient_quota',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'WALLET_ADDRESS_INVALID' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_address_invalid',
            'user_message_key' => 'error-manager::errors.user.wallet_address_invalid',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'WALLET_NOT_FOUND' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_not_found',
            'user_message_key' => 'error-manager::errors.user.wallet_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'WALLET_ALREADY_EXISTS' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_already_exists',
            'user_message_key' => 'error-manager::errors.user.wallet_already_exists',
            'http_status_code' => 409, // Conflict
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'WALLET_INVALID_SECRET' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_invalid_secret',
            'user_message_key' => 'error-manager::errors.user.wallet_invalid_secret',
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'http_status_code' => 401,
            'msg_to' => 'sweet-alert',
        ],

        'WALLET_VALIDATION_FAILED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_validation_failed',
            'user_message_key' => 'error-manager::errors.user.wallet_validation_failed',
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'http_status_code' => 422,
            'msg_to' => 'div',
        ],

        'WALLET_CONNECTION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.wallet_connection_failed',
            'user_message_key' => 'error-manager::errors.user.wallet_connection_failed',
            'devTeam_email_need' => true,
            'notify_slack' => false,
            'http_status_code' => 422,
            'msg_to' => 'sweet-alert',
        ],

        'WALLET_DISCONNECT_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.wallet_disconnect_failed',
            'user_message_key' => 'error-manager::errors.user.wallet_disconnect_failed',
            'devTeam_email_need' => true,
            'notify_slack' => false,
            'http_status_code' => 422,
            'msg_to' => 'toast',
        ],

        // ====================================================
        // Errori specifici per la gestione delle collezioni
        // ====================================================

        'COLLECTION_CREATION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_creation_failed',
            'user_message_key' => 'error-manager::errors.user.collection_creation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'COLLECTION_FIND_CREATE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_find_create_failed',
            'user_message_key' => 'error-manager::errors.user.collection_find_create_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'AUTH_REQUIRED' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.auth_required',
            'user_message_key' => 'error-manager::errors.user.auth_required',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // ENHANCED REGISTRATION ERROR CODES - Add to config/error-manager.php
        // ====================================================

        // Enhanced Registration with Ecosystem Setup
        'ENHANCED_REGISTRATION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.enhanced_registration_failed',
            'user_message_key' => 'error-manager::errors.user.enhanced_registration_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_USER_CREATION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_user_creation_failed',
            'user_message_key' => 'error-manager::errors.user.registration_user_creation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_COLLECTION_CREATION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_collection_creation_failed',
            'user_message_key' => 'error-manager::errors.user.registration_collection_creation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_WALLET_SETUP_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_wallet_setup_failed',
            'user_message_key' => 'error-manager::errors.user.registration_wallet_setup_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_ROLE_ASSIGNMENT_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_role_assignment_failed',
            'user_message_key' => 'error-manager::errors.user.registration_role_assignment_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_GDPR_CONSENT_FAILED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_gdpr_consent_failed',
            'user_message_key' => 'error-manager::errors.user.registration_gdpr_consent_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_ECOSYSTEM_SETUP_INCOMPLETE' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_ecosystem_setup_incomplete',
            'user_message_key' => 'error-manager::errors.user.registration_ecosystem_setup_incomplete',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_VALIDATION_ENHANCED_FAILED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_validation_enhanced_failed',
            'user_message_key' => 'error-manager::errors.user.registration_validation_enhanced_failed',
            'http_status_code' => 422,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'REGISTRATION_USER_TYPE_INVALID' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_user_type_invalid',
            'user_message_key' => 'error-manager::errors.user.registration_user_type_invalid',
            'http_status_code' => 422,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'REGISTRATION_RATE_LIMIT_EXCEEDED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_rate_limit_exceeded',
            'user_message_key' => 'error-manager::errors.user.registration_rate_limit_exceeded',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'REGISTRATION_PAGE_LOAD_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_page_load_error',
            'user_message_key' => 'error-manager::errors.user.registration_page_load_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // EGI MODULE ERROR CODES - Add to config/error-manager.php in 'errors' array
        // ====================================================

        // EGI Upload Handler Service-Based Architecture Errors
        'EGI_COLLECTION_SERVICE_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_collection_service_error',
            'user_message_key' => 'error-manager::errors.user.egi_collection_service_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_WALLET_SERVICE_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_wallet_service_error',
            'user_message_key' => 'error-manager::errors.user.egi_wallet_service_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_ROLE_SERVICE_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_role_service_error',
            'user_message_key' => 'error-manager::errors.user.egi_role_service_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_SERVICE_INTEGRATION_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_service_integration_error',
            'user_message_key' => 'error-manager::errors.user.egi_service_integration_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_ENHANCED_AUTHENTICATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_enhanced_authentication_error',
            'user_message_key' => 'error-manager::errors.user.egi_enhanced_authentication_error',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_FILE_INPUT_VALIDATION_ERROR' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_file_input_validation_error',
            'user_message_key' => 'error-manager::errors.user.egi_file_input_validation_error',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'EGI_METADATA_VALIDATION_ERROR' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_metadata_validation_error',
            'user_message_key' => 'error-manager::errors.user.egi_metadata_validation_error',
            'http_status_code' => 422,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],

        'EGI_DATA_PREPARATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_data_preparation_error',
            'user_message_key' => 'error-manager::errors.user.egi_data_preparation_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_RECORD_CREATION_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_record_creation_error',
            'user_message_key' => 'error-manager::errors.user.egi_record_creation_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_FILE_STORAGE_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_file_storage_error',
            'user_message_key' => 'error-manager::errors.user.egi_file_storage_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_CACHE_INVALIDATION_ERROR' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.egi_cache_invalidation_error',
            'user_message_key' => 'error-manager::errors.user.egi_cache_invalidation_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'log-only',
        ],

        // Collection Service Enhanced Errors
        'COLLECTION_CREATION_ENHANCED_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_creation_enhanced_error',
            'user_message_key' => 'error-manager::errors.user.collection_creation_enhanced_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'COLLECTION_VALIDATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_validation_error',
            'user_message_key' => 'error-manager::errors.user.collection_validation_error',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'COLLECTION_LIMIT_EXCEEDED_ERROR' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_limit_exceeded_error',
            'user_message_key' => 'error-manager::errors.user.collection_limit_exceeded_error',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'COLLECTION_WALLET_ATTACHMENT_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.collection_wallet_attachment_failed',
            'user_message_key' => 'error-manager::errors.user.collection_wallet_attachment_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'log-only',
        ],

        'COLLECTION_ROLE_ASSIGNMENT_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.collection_role_assignment_failed',
            'user_message_key' => 'error-manager::errors.user.collection_role_assignment_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'log-only',
        ],

        'COLLECTION_OWNERSHIP_MISMATCH_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_ownership_mismatch_error',
            'user_message_key' => 'error-manager::errors.user.collection_ownership_mismatch_error',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'COLLECTION_CURRENT_UPDATE_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.collection_current_update_error',
            'user_message_key' => 'error-manager::errors.user.collection_current_update_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'USER_CURRENT_COLLECTION_UPDATE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.user_current_collection_update_failed',
            'user_message_key' => 'error-manager::errors.user.user_current_collection_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'USER_CURRENT_COLLECTION_VALIDATION_FAILED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.user_current_collection_validation_failed',
            'user_message_key' => 'error-manager::errors.user.user_current_collection_validation_failed',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // Enhanced Storage Errors
        'EGI_STORAGE_DISK_CONFIG_ERROR' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_storage_disk_config_error',
            'user_message_key' => 'error-manager::errors.user.egi_storage_disk_config_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_STORAGE_EMERGENCY_FALLBACK_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_storage_emergency_fallback_failed',
            'user_message_key' => 'error-manager::errors.user.egi_storage_emergency_fallback_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_TEMP_FILE_READ_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_temp_file_read_error',
            'user_message_key' => 'error-manager::errors.user.egi_temp_file_read_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // Enhanced Authentication & Session Errors
        'EGI_SESSION_AUTH_INVALID' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_session_auth_invalid',
            'user_message_key' => 'error-manager::errors.user.egi_session_auth_invalid',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'EGI_WALLET_AUTH_MISMATCH' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.egi_wallet_auth_mismatch',
            'user_message_key' => 'error-manager::errors.user.egi_wallet_auth_mismatch',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // Errori specifici per la gestione dei like
        // ====================================================

        'AUTH_REQUIRED_FOR_LIKE' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.auth_required_for_like',
            'user_message_key' => 'error-manager::errors.user.auth_required_for_like',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'LIKE_TOGGLE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.like_toggle_failed',
            'user_message_key' => 'error-manager::errors.user.like_toggle_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'toast',
        ],


        // ====================================================
        // Errori specifici per la gestione delle prenotazioni
        // ====================================================

        'RESERVATION_EGI_NOT_AVAILABLE' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'errors.dev.reservation_egi_not_available',
            'user_message_key' => 'errors.user.reservation_egi_not_available',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert'
        ],
        'RESERVATION_AMOUNT_TOO_LOW' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'errors.dev.reservation_amount_too_low',
            'user_message_key' => 'errors.user.reservation_amount_too_low',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast'
        ],
        'RESERVATION_UNAUTHORIZED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'errors.dev.reservation_unauthorized',
            'user_message_key' => 'errors.user.reservation_unauthorized',
            'http_status_code' => 401,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert'
        ],
        'RESERVATION_CERTIFICATE_GENERATION_FAILED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'errors.dev.reservation_certificate_generation_failed',
            'user_message_key' => 'errors.user.reservation_certificate_generation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert'
        ],
        'RESERVATION_CERTIFICATE_NOT_FOUND' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'errors.dev.reservation_certificate_not_found',
            'user_message_key' => 'errors.user.reservation_certificate_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div'
        ],
        'RESERVATION_ALREADY_EXISTS' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'errors.dev.reservation_already_exists',
            'user_message_key' => 'errors.user.reservation_already_exists',
            'http_status_code' => 400,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast'
        ],
        'RESERVATION_CANCEL_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'errors.dev.reservation_cancel_failed',
            'user_message_key' => 'errors.user.reservation_cancel_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast'
        ],
        'RESERVATION_UNAUTHORIZED_CANCEL' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'errors.dev.reservation_unauthorized_cancel',
            'user_message_key' => 'errors.user.reservation_unauthorized_cancel',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast'
        ],
        'RESERVATION_STATUS_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'errors.dev.reservation_status_failed',
            'user_message_key' => 'errors.user.reservation_status_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast'
        ],
        'RESERVATION_UNKNOWN_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'errors.dev.reservation_unknown_error',
            'user_message_key' => 'errors.user.reservation_unknown_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert'
        ],

        // --- STATISTICS ERRORS ---
        'STATISTICS_CALCULATION_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.statistics_calculation_failed',
            'user_message_key' => 'error-manager::errors.user.statistics_calculation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'ICON_NOT_FOUND' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.icon_not_found',
            'user_message_key' => 'error-manager::errors.user.icon_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'div',
        ],
        'ICON_RETRIEVAL_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.icon_retrieval_failed',
            'user_message_key' => 'error-manager::errors.user.icon_retrieval_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'fallback',
        ],
        'STATISTICS_CACHE_CLEAR_FAILED' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.statistics_cache_clear_failed',
            'user_message_key' => 'error-manager::errors.user.statistics_cache_clear_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast',
        ],
        'STATISTICS_SUMMARY_FAILED' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.statistics_summary_failed',
            'user_message_key' => 'error-manager::errors.user.statistics_summary_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast',
        ],
        'GDPR_CONSENT_REQUIRED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_required',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_required',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_CONSENT_UPDATE_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_update_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_update_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_CONSENT_SAVE_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_save_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_save_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_CONSENT_LOAD_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_load_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_load_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // GDPR Data Export errors
        'GDPR_EXPORT_REQUEST_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_request_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_request_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_EXPORT_LIMIT_REACHED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_limit_reached',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_limit_reached',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_EXPORT_CREATE_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_create_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_create_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_EXPORT_DOWNLOAD_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_download_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_download_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_EXPORT_STATUS_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_status_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_status_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_EXPORT_PROCESSING_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_processing_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_processing_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // GDPR Processing Restriction errors
        'GDPR_PROCESSING_RESTRICTED' => [
            'type' => 'warning',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_restricted',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_restricted',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_PROCESSING_LIMIT_VIEW_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_limit_view_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_limit_view_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_PROCESSING_RESTRICTION_CREATE_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_restriction_create_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_restriction_create_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_PROCESSING_RESTRICTION_REMOVE_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_restriction_remove_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_restriction_remove_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_PROCESSING_RESTRICTION_LIMIT_REACHED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_restriction_limit_reached',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_restriction_limit_reached',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // GDPR Account Deletion errors
        'GDPR_DELETION_REQUEST_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_deletion_request_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_deletion_request_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_DELETION_CANCELLATION_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_deletion_cancellation_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_deletion_cancellation_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_DELETION_PROCESSING_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_deletion_processing_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_deletion_processing_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // GDPR Breach Report errors
        'GDPR_BREACH_REPORT_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_report_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_report_error',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_BREACH_EVIDENCE_UPLOAD_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_evidence_upload_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_evidence_upload_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        // GDPR Activity Log errors
        'GDPR_ACTIVITY_LOG_ERROR' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_activity_log_error',
            'user_message_key' => 'error-manager::errors.user.gdpr_activity_log_error',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        // GDPR Security errors
        'GDPR_ENHANCED_SECURITY_REQUIRED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_enhanced_security_required',
            'user_message_key' => 'error-manager::errors.user.gdpr_enhanced_security_required',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],
        'GDPR_CRITICAL_SECURITY_REQUIRED' => [
            'type' => 'warning',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_critical_security_required',
            'user_message_key' => 'error-manager::errors.user.gdpr_critical_security_required',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Consent Management Errors
        // ====================================================
        'GDPR_CONSENT_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_CONSENT_PREFERENCES_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_preferences_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_preferences_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_CONSENT_UPDATE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_update_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_SERVICE_UNAVAILABLE' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_service_unavailable',
            'user_message_key' => 'error-manager::errors.user.gdpr_service_unavailable',
            'http_status_code' => 503,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_CONSENT_HISTORY_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_history_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_consent_history_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Data Export Errors
        // ====================================================
        'GDPR_EXPORT_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_EXPORT_GENERATION_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_generation_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_generation_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_EXPORT_NOT_FOUND' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_not_found',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_not_found',
            'http_status_code' => 404,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_EXPORT_DOWNLOAD_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_download_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_download_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Personal Data Management Errors
        // ====================================================
        'GDPR_EDIT_DATA_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_edit_data_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_edit_data_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_PERSONAL_DATA_UPDATE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_personal_data_update_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_personal_data_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_RECTIFICATION_REQUEST_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_rectification_request_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_rectification_request_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Processing Limitation Errors
        // ====================================================
        'GDPR_PROCESSING_LIMITS_UPDATE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_processing_limits_update_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_processing_limits_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Account Deletion Errors
        // ====================================================
        'GDPR_DELETE_ACCOUNT_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_delete_account_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_delete_account_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_DELETION_REQUEST_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_deletion_request_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_deletion_request_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_ACCOUNT_DELETION_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_account_deletion_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_account_deletion_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Activity Log Errors
        // ====================================================
        'GDPR_ACTIVITY_LOG_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_activity_log_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_activity_log_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_ACTIVITY_LOG_EXPORT_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_activity_log_export_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_activity_log_export_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Breach Reporting Errors
        // ====================================================
        'GDPR_BREACH_REPORT_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_report_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_report_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_BREACH_REPORT_SUBMISSION_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_report_submission_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_report_submission_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_BREACH_REPORT_ACCESS_DENIED' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_report_access_denied',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_report_access_denied',
            'http_status_code' => 403,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_BREACH_REPORT_STATUS_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_breach_report_status_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_breach_report_status_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR Privacy Policy & Transparency Errors
        // ====================================================
        'GDPR_PRIVACY_POLICY_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_privacy_policy_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_privacy_policy_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_PRIVACY_POLICY_CHANGELOG_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_privacy_policy_changelog_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_privacy_policy_changelog_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_DATA_PROCESSING_INFO_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_data_processing_info_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_data_processing_info_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR DPO Contact & Support Errors
        // ====================================================
        'GDPR_DPO_CONTACT_PAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_dpo_contact_page_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_dpo_contact_page_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_DPO_MESSAGE_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_dpo_message_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_dpo_message_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        // ====================================================
        // GDPR API Errors
        // ====================================================
        'GDPR_API_CONSENT_STATUS_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_api_consent_status_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_api_consent_status_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'json',
        ],

        'GDPR_API_PROCESSING_LIMITS_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_api_processing_limits_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_api_processing_limits_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'json',
        ],

        'GDPR_API_EXPORT_STATUS_FAILED' => [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_api_export_status_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_api_export_status_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'json',
        ],

        // ====================================================
        // GDPR Legacy Method Errors
        // ====================================================
        'GDPR_LEGACY_DATA_DOWNLOAD_FAILED' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_legacy_data_download_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_legacy_data_download_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_NOTIFICATION_DISPATCH_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_notification_dispatch_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_notification_dispatch_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_NOTIFICATION_PERSISTENCE_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_notification_persistence_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_notification_persistence_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'PERMISSION_BASED_REGISTRATION_FAILED' => [
            'type' => 'server_error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.permission_based_registration_failed',
            'user_message_key' => 'error-manager::errors.user.permission_based_registration_failed_user',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log', 'db'],
            'msg_to' => 'toast',
            'log_level' => 'error',
            'category' => 'registration',
            'sensitive_keys' => ['password', 'password_confirmation', 'registration_ip', 'user_agent'],
        ],

        'ALGORAND_WALLET_GENERATION_FAILED' => [
            'type' => 'server_error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.algorand_wallet_generation_failed',
            'user_message_key' => 'error-manager::errors.user.algorand_wallet_generation_failed_user',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log', 'db'],
            'msg_to' => 'toast',
            'log_level' => 'error',
            'category' => 'registration',
        ],

        'ECOSYSTEM_SETUP_FAILED' => [
            'type' => 'server_error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.ecosystem_setup_failed',
            'user_message_key' => 'error-manager::errors.user.ecosystem_setup_failed_user',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log', 'db'],
            'msg_to' => 'toast',
            'log_level' => 'error',
            'category' => 'registration',
            'sensitive_keys' => ['user_id', 'collection_id'],
        ],

        'USER_DOMAIN_INITIALIZATION_FAILED' => [
            'type' => 'server_error',
            'blocking' => 'blocking', // Non blocking - l'utente può completare i domini dopo
            'dev_message_key' => 'error-manager::errors.dev.user_domain_initialization_failed',
            'user_message_key' => 'error-manager::errors.user.user_domain_initialization_failed_user',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log'],
            'msg_to' => 'toast',
            'log_level' => 'warning',
            'category' => 'registration',
        ],

        'GDPR_VIOLATION_ATTEMPT' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_violation_attempt',
            'user_message_key' => 'error-manager::errors.user.generic_internal_error', // Non dare dettagli specifici all'utente
            'http_status_code' => 500, // Errore di configurazione/logica interna
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_NOTIFICATION_SEND_FAILED' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_notification_send_failed',
            'user_message_key' => 'error-manager::errors.user.gdpr_notification_send_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_VIOLATION_ATTEMPT' => [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_violation_attempt',
            'user_message_key' => 'error-manager::errors.user.gdpr_violation_attempt',
            'http_status_code' => 403,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'ROLE_ASSIGNMENT_FAILED' => [
            'type' => 'server_error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.role_assignment_failed',
            'user_message_key' => 'error-manager::errors.user.role_assignment_failed_user',
            'http_status_code' => 403,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log', 'db'],
            'msg_to' => 'toast',
            'log_level' => 'error',
            'category' => 'registration',
        ],

        'REGISTRATION_PAGE_LOAD_ERROR' => [
            'type' => 'server_error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.registration_page_load_error',
            'user_message_key' => 'error-manager::errors.user.registration_page_load_error_user',
            'http_status_code' => 403,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'notifiable' => true,
            'notifications' => ['log'],
            'msg_to' => 'toast',
            'log_level' => 'warning',
            'category' => 'ui',
        ],

        'PERSONAL_DATA_VIEW_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.personal_data_view_failed',
            'user_message_key' => 'error-manager::errors.user.personal_data_view_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'PERSONAL_DATA_UPDATE_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.personal_data_update_failed',
            'user_message_key' => 'error-manager::errors.user.personal_data_update_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'PERSONAL_DATA_EXPORT_ERROR' => [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.personal_data_export_failed',
            'user_message_key' => 'error-manager::errors.user.personal_data_export_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'sweet-alert',
        ],

        'PERSONAL_DATA_DELETION_ERROR' => [
            'type' => 'error',
            'blocking' => 'blocking',
            'dev_message_key' => 'error-manager::errors.dev.personal_data_deletion_failed',
            'user_message_key' => 'error-manager::errors.user.personal_data_deletion_failed',
            'http_status_code' => 500,
            'devTeam_email_need' => true,
            'notify_slack' => true,
            'msg_to' => 'sweet-alert',
        ],

        'GDPR_EXPORT_RATE_LIMIT' => [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message_key' => 'error-manager::errors.dev.gdpr_export_rate_limit',
            'user_message_key' => 'error-manager::errors.user.gdpr_export_rate_limit',
            'http_status_code' => 429,
            'devTeam_email_need' => false,
            'notify_slack' => false,
            'msg_to' => 'toast',
        ],

    ]
];
