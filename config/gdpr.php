<?php
// config/gdpr.php
// FlorenceEGI GDPR Configuration
return [
    /*
    |--------------------------------------------------------------------------
    | GDPR Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for the GDPR module.
    |
    */

    'current_policy_version' => '1.0.0',
    'policy_update_url' => '/gdpr/policy-update',

    /*
    |--------------------------------------------------------------------------
    | Consent Settings
    |--------------------------------------------------------------------------
    */
    'consent' => [
        // Default consent values for new users
        'defaults' => [
            'marketing' => false,
            'analytics' => false,
            'profiling' => false,
            'functional' => true,
            'essential' => true,
        ],

        // Whether consents can be updated via API
        'allow_api_updates' => true,

        // Whether to auto-save individual consent changes
        'auto_save' => true,

        // Categories that cannot be opted out of
        'required_categories' => [
            'essential',
        ],

        // Consent definitions
        'definitions' => [
            'essential' => [
                'label' => 'gdpr.consent.essential.label',
                'description' => 'gdpr.consent.essential.description',
                'required' => true,
            ],
            'functional' => [
                'label' => 'gdpr.consent.functional.label',
                'description' => 'gdpr.consent.functional.description',
                'required' => false,
            ],
            'analytics' => [
                'label' => 'gdpr.consent.analytics.label',
                'description' => 'gdpr.consent.analytics.description',
                'required' => false,
            ],
            'marketing' => [
                'label' => 'gdpr.consent.marketing.label',
                'description' => 'gdpr.consent.marketing.description',
                'required' => false,
            ],
            'profiling' => [
                'label' => 'gdpr.consent.profiling.label',
                'description' => 'gdpr.consent.profiling.description',
                'required' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Export Settings
    |--------------------------------------------------------------------------
    */
    'export' => [
        // Default export format
        'default_format' => 'json',

        // Available export formats
        'available_formats' => [
            'json' => [
                'extension' => 'json',
                'mime_type' => 'application/json',
            ],
            'csv' => [
                'extension' => 'csv',
                'mime_type' => 'text/csv',
            ],
            'pdf' => [
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
            ],
        ],

        // Maximum exports per user per day
        'max_exports_per_day' => 3,

        // Export timeout in minutes
        'timeout_minutes' => 30,

        // Whether to enable password protection option
        'enable_password_protection' => true,

        // Default inclusion settings
        'include_metadata' => true,
        'include_timestamps' => true,

        // Data categories available for export
        'data_categories' => [
            'profile' => 'gdpr.export.categories.profile',
            'account' => 'gdpr.export.categories.account',
            'preferences' => 'gdpr.export.categories.preferences',
            'activity' => 'gdpr.export.categories.activity',
            'consents' => 'gdpr.export.categories.consents',
            'collections' => 'gdpr.export.categories.collections',
            'purchases' => 'gdpr.export.categories.purchases',
            'comments' => 'gdpr.export.categories.comments',
            'messages' => 'gdpr.export.categories.messages',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Restriction Settings
    |--------------------------------------------------------------------------
    */
    'processing_restriction' => [
        // Maximum active restrictions per user
        'max_active_restrictions' => 5,

        // Auto-expiry time for restrictions in days (null = never expire)
        'auto_expiry_days' => null,

        // Whether to enable processing restriction notifications
        'enable_notifications' => true,

        // Data categories available for restriction
        'data_categories' => [
            'profile' => 'gdpr.restriction.categories.profile',
            'activity' => 'gdpr.restriction.categories.activity',
            'preferences' => 'gdpr.restriction.categories.preferences',
            'collections' => 'gdpr.restriction.categories.collections',
            'purchases' => 'gdpr.restriction.categories.purchases',
            'comments' => 'gdpr.restriction.categories.comments',
            'messages' => 'gdpr.restriction.categories.messages',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Deletion Settings
    |--------------------------------------------------------------------------
    */
    'deletion' => [
        // Whether to use soft deletes or hard deletes
        'use_soft_delete' => true,

        // Delay before processing deletion request (in days)
        'processing_delay_days' => 7,

        // Whether to allow reactivation during the delay period
        'allow_reactivation' => true,

        // Whether to keep certain anonymized data
        'keep_anonymized_data' => true,

        // Whether to require password confirmation for deletion
        'require_password_confirmation' => true,

        // Whether to require reason for deletion
        'require_reason' => true,

        // Predefined deletion reasons
        'deletion_reasons' => [
            'no_longer_needed' => 'gdpr.deletion.reasons.no_longer_needed',
            'privacy_concerns' => 'gdpr.deletion.reasons.privacy_concerns',
            'moving_to_competitor' => 'gdpr.deletion.reasons.moving_to_competitor',
            'unhappy_with_service' => 'gdpr.deletion.reasons.unhappy_with_service',
            'other' => 'gdpr.deletion.reasons.other',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Breach Report Settings
    |--------------------------------------------------------------------------
    */
    'breach_report' => [
        // Whether to allow anonymous breach reports
        'allow_anonymous' => false,

        // File types allowed for evidence uploads
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'txt', 'doc', 'docx'],

        // Maximum file size for evidence uploads (in KB)
        'max_file_size_kb' => 10240, // 10MB

        // Email addresses to notify on new breach reports
        'notification_emails' => [
            'dpo@florenceegi.com',
            'security@florenceegi.com',
        ],

        // Whether to show a custom thank you message after submission
        'show_thank_you' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Settings
    |--------------------------------------------------------------------------
    */
    'activity_log' => [
        // Whether to enable GDPR activity logging
        'enabled' => true,

        // Log retention period in days
        'retention_days' => 365,

        // Activities to log
        'log_activities' => [
            'consent_updated' => true,
            'data_exported' => true,
            'processing_restricted' => true,
            'account_deletion_requested' => true,
            'account_deleted' => true,
            'breach_reported' => true,
        ],

        // Whether to include IP address in logs
        'log_ip_address' => true,

        // Whether to include user agent in logs
        'log_user_agent' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI/UX Settings
    |--------------------------------------------------------------------------
    */
    'ui' => [
        // Brand colors for GDPR UI (FlorenceEGI brand guidelines)
        'colors' => [
            'primary' => '#D4A574', // Oro Fiorentino
            'secondary' => '#2D5016', // Verde Rinascita
            'accent' => '#1B365D', // Blu Algoritmo
            'neutral' => '#6B6B6B', // Grigio Pietra
            'danger' => '#C13120', // Rosso Urgenza
            'warning' => '#E67E22', // Arancio Energia
            'success' => '#4ADE80', // Verde Successo
        ],

        // Whether to show breadcrumbs in GDPR pages
        'show_breadcrumbs' => true,

        // Whether to show help text throughout GDPR pages
        'show_help_text' => true,

        // Whether to show privacy policy links in context
        'show_privacy_policy_links' => true,

        // Whether to use glassmorphism design in UI components
        'use_glassmorphism' => true,

        // Path to privacy policy page
        'privacy_policy_path' => '/privacy-policy',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        // Whether to send email notifications for GDPR actions
        'send_emails' => true,

        // Whether to send Slack notifications for critical GDPR actions
        'send_slack' => true,

        // Slack webhook URL for notifications
        'slack_webhook_url' => env('GDPR_SLACK_WEBHOOK_URL'),

        // Email notification classes
        // 'email_classes' => [
        //     'consent_updated' => \App\Notifications\Gdpr\ConsentUpdatedNotification::class,
        //     'data_exported' => \App\Notifications\Gdpr\DataExportedNotification::class,
        //     'processing_restricted' => \App\Notifications\Gdpr\ProcessingRestrictedNotification::class,
        //     'account_deletion_requested' => \App\Notifications\Gdpr\AccountDeletionRequestedNotification::class,
        //     'account_deletion_processed' => \App\Notifications\Gdpr\AccountDeletionProcessedNotification::class,
        //     'breach_report_received' => \App\Notifications\Gdpr\BreachReportReceivedNotification::class,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Whether to rate limit GDPR requests
        'enable_rate_limiting' => true,

        // Rate limit attempts per minute
        'rate_limit_attempts' => 100,

        // Rate limit decay minutes
        'rate_limit_decay_minutes' => 1,

        // Whether to encrypt exports
        'encrypt_exports' => true,

        // Whether to encrypt breach reports
        'encrypt_breach_reports' => true,

        // Encryption cipher to use
        'encryption_cipher' => 'AES-256-CBC',
    ],
];
