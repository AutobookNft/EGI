<?php
// resources/lang/en/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | GDPR Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for GDPR-related functionality.
    |
    */

    // General
    'gdpr' => 'GDPR',
    'gdpr_center' => 'GDPR Data Control Center',
    'dashboard' => 'Dashboard',
    'back_to_dashboard' => 'Back to Dashboard',
    'save' => 'Save',
    'submit' => 'Submit',
    'cancel' => 'Cancel',
    'continue' => 'Continue',
    'loading' => 'Loading...',
    'success' => 'Success',
    'error' => 'Error',
    'warning' => 'Warning',
    'info' => 'Information',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'pending' => 'Pending',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'processing' => 'Processing',
    'retry' => 'Retry',
    'required_field' => 'Required field',
    'required_consent' => 'Required consent',
    'select_all_categories' => 'Select all categories',
    'no_categories_selected' => 'No categories selected',
    'compliance_badge'=> 'Compliance Badge',

    // Breadcrumb
    'breadcrumb' => [
        'dashboard' => 'Dashboard',
        'gdpr' => 'Privacy & GDPR',
    ],

    // Alert Messages
    'alerts' => [
        'success' => 'Operation completed!',
        'error' => 'Error:',
        'warning' => 'Warning:',
        'info' => 'Information:',
    ],

    // Menu items
    'menu' => [
        'gdpr_center' => 'GDPR Data Control Center',
        'consent_management' => 'Consent Management',
        'data_export' => 'Export My Data',
        'processing_restrictions' => 'Limit Data Processing',
        'delete_account' => 'Delete My Account',
        'breach_report' => 'Report Data Breach',
        'activity_log' => 'My GDPR Activity Log',
        'privacy_policy' => 'Privacy Policy',
    ],

    // Consent Management
    'consent' => [
        'title' => 'Manage Your Consent Preferences',
        'description' => 'Control how your data is used within our platform. You can update your preferences at any time.',
        'update_success' => 'Your consent preferences have been updated.',
        'update_error' => 'There was an error updating your consent preferences. Please try again.',
        'save_all' => 'Save All Preferences',
        'last_updated' => 'Last updated:',
        'never_updated' => 'Never updated',
        'privacy_notice' => 'Privacy Notice',
        'not_given' => 'Not Given',
        'given_at' => 'Given at',
        'your_consents' => 'Your Consents',
        'subtitle' => 'Manage your privacy preferences and view the status of your consents.',
        'breadcrumb' => 'Consents',
        'history_title' => 'Consent History',
        'back_to_consents' => 'Back to Consents',
        'preferences_title' => 'Consent Preferences Management',
        'preferences_subtitle' => 'Configure your detailed privacy preferences',
        'preferences_breadcrumb' => 'Preferences',
        'preferences_info_title' => 'Granular Consent Management',
        'preferences_info_description' => 'Here you can configure each type of consent in detail...',
        'required' => 'Required',
        'optional' => 'Optional',
        'toggle_label' => 'Enable/Disable',
        'always_enabled' => 'Always Enabled',
        'benefits_title' => 'Benefits for You',
        'consequences_title' => 'If You Disable',
        'third_parties_title' => 'Third-Party Services',
        'save_preferences' => 'Save Preferences',
        'back_to_overview' => 'Back to Overview',
        'never_updated' => 'Never updated',

         // Consent Details
        'given_at' => 'Given on',
        'withdrawn_at' => 'Withdrawn on',
        'not_given' => 'Not given',
        'method' => 'Method',
        'version' => 'Version',
        'unknown_version' => 'Unknown version',

         // Actions
        'withdraw' => 'Withdraw Consent',
        'withdraw_confirm' => 'Are you sure you want to withdraw this consent? This action may limit some functionalities.',
        'renew' => 'Renew Consent',
        'view_history' => 'View History',

        // Empty States
        'no_consents' => 'No Consents Present',
        'no_consents_description' => 'You have not yet provided any consent for data processing. You can manage your preferences using the button below.',

        // Manage Preferences
        'manage_preferences' => 'Manage Your Preferences',
        'update_preferences' => 'Update Privacy Preferences',

        // Summary Dashboard
        'summary' => [
            'active' => 'Active Consents',
            'total' => 'Total Consents',
            'compliance' => 'Compliance Score',
        ],

        // Consent Methods
        'methods' => [
            'web' => 'Web Interface',
            'api' => 'API',
            'import' => 'Import',
            'admin' => 'Administrator',
        ],

        // Consent Purposes
        'purposes' => [
            'functional' => 'Functional Consents',
            'analytics' => 'Analytics Consents',
            'marketing' => 'Marketing Consents',
            'profiling' => 'Profiling Consents',
        ],

        // Consent Descriptions
        'descriptions' => [
            'functional' => 'Necessary for the basic operation of the platform and to provide requested services.',
            'analytics' => 'Used to analyze site usage and improve user experience.',
            'marketing' => 'Used to send you promotional communications and personalized offers.',
            'profiling' => 'Used to create personalized profiles and suggest relevant content.',
        ],

        // Consent Status
        'status' => [
            'granted' => 'Granted',
            'denied' => 'Denied',
            'active' => 'Active',
            'withdrawn' => 'Withdrawn',
            'expired' => 'Expired',
        ],

        'essential' => [
            'label' => 'Essential Cookies',
            'description' => 'These cookies are necessary for the website to function and cannot be switched off in our systems.',
        ],
        'functional' => [
            'label' => 'Functional Cookies',
            'description' => 'These cookies enable the website to provide enhanced functionality and personalization.',
        ],
        'analytics' => [
            'label' => 'Analytics Cookies',
            'description' => 'These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site.',
        ],
        'marketing' => [
            'label' => 'Marketing Cookies',
            'description' => 'These cookies may be set through our site by our advertising partners to build a profile of your interests.',
        ],
        'profiling' => [
            'label' => 'Profiling',
            'description' => 'We use profiling to better understand your preferences and tailor our services to your needs.',
        ],
        'saving_consent' => 'Saving...',
        'consent_saved' => 'Saved',
        'saving_all_consents' => 'Saving all preferences...',
        'all_consents_saved' => 'All consent preferences have been saved successfully.',
        'all_consents_save_error' => 'There was an error saving all consent preferences.',
        'consent_save_error' => 'There was an error saving this consent preference.',

        // Processing Purposes
        'processing_purposes' => [
            'functional' => 'Essential platform operations: authentication, security, service delivery, user preferences storage',
            'analytics' => 'Platform improvement: usage analytics, performance monitoring, user experience optimization',
            'marketing' => 'Communication: newsletters, product updates, promotional offers, event notifications',
            'profiling' => 'Personalization: content recommendations, user behavior analysis, targeted suggestions',
        ],

        // Retention Periods
        'retention_periods' => [
            'functional' => 'Duration of account + 1 year for legal compliance',
            'analytics' => '2 years from last activity',
            'marketing' => '3 years from last interaction or consent withdrawal',
            'profiling' => '1 year from last activity or consent withdrawal',
        ],

        // User Benefits
        'user_benefits' => [
            'functional' => [
                'Secure access to your account',
                'Personalized user settings',
                'Reliable platform performance',
                'Protection against fraud and abuse',
            ],
            'analytics' => [
                'Improved platform performance',
                'Better user experience design',
                'Faster loading times',
                'Enhanced feature development',
            ],
            'marketing' => [
                'Relevant product updates',
                'Exclusive offers and promotions',
                'Event invitations and announcements',
                'Educational content and tips',
            ],
            'profiling' => [
                'Personalized content recommendations',
                'Tailored user experience',
                'Relevant project suggestions',
                'Customized dashboard and features',
            ],
        ],

        // Third Parties
        'third_parties' => [
            'functional' => [
                'CDN providers (static content delivery)',
                'Security services (fraud prevention)',
                'Infrastructure providers (hosting)',
            ],
            'analytics' => [
                'Analytics platforms (anonymized usage data)',
                'Performance monitoring services',
                'Error tracking services',
            ],
            'marketing' => [
                'Email service providers',
                'Marketing automation platforms',
                'Social media platforms (for advertising)',
            ],
            'profiling' => [
                'Recommendation engines',
                'Behavioral analysis services',
                'Content personalization platforms',
            ],
        ],

        // Withdrawal Consequences
        'withdrawal_consequences' => [
            'functional' => [
                'Cannot withdraw - essential for platform operation',
                'Account access would be compromised',
                'Security features would be disabled',
            ],
            'analytics' => [
                'Platform improvements may not reflect your usage patterns',
                'Generic experience instead of optimized performance',
                'No impact on core functionality',
            ],
            'marketing' => [
                'No promotional emails or updates',
                'May miss important announcements',
                'No impact on platform functionality',
                'Can be re-enabled at any time',
            ],
            'profiling' => [
                'Generic content instead of personalized recommendations',
                'Standard dashboard layout',
                'Less relevant project suggestions',
                'No impact on core platform features',
            ],
        ],

    ],

    // Data Export
    'export' => [
        'title' => 'Export Your Data',
        'description' => 'Request a copy of your personal data. This may take a few minutes to process.',
        'request_button' => 'Request Data Export',
        'format' => 'Export Format',
        'format_json' => 'JSON (recommended for developers)',
        'format_csv' => 'CSV (spreadsheet compatible)',
        'format_pdf' => 'PDF (human-readable document)',
        'include_metadata' => 'Include metadata',
        'include_timestamps' => 'Include timestamps',
        'password_protection' => 'Password protect the export',
        'password' => 'Export password',
        'confirm_password' => 'Confirm password',
        'data_categories' => 'Data categories to export',
        'request_success' => 'Your data export request has been submitted.',
        'request_error' => 'There was an error requesting your data export. Please try again.',
        'recent_exports' => 'Recent Exports',
        'no_recent_exports' => 'You have no recent exports.',
        'export_status' => 'Export Status',
        'export_date' => 'Export Date',
        'export_size' => 'Export Size',
        'export_id' => 'Export ID',
        'download' => 'Download',
        'download_export' => 'Download Export',
        'export_preparing' => 'Preparing your data export...',
        'export_queued' => 'Your export is queued and will start soon...',
        'export_processing' => 'Processing your data export...',
        'export_ready' => 'Your data export is ready for download.',
        'export_failed' => 'Your data export failed.',
        'export_failed_details' => 'There was an error processing your data export. Please try again or contact support.',
        'export_unknown_status' => 'Export status unknown.',
        'check_status' => 'Check Status',
        'retry_export' => 'Retry Export',
        'export_download_error' => 'There was an error downloading your export.',
        'export_status_error' => 'Error checking export status.',
        'categories' => [
            'profile' => 'Profile Information',
            'account' => 'Account Details',
            'preferences' => 'Preferences & Settings',
            'activity' => 'Activity History',
            'consents' => 'Consent History',
            'collections' => 'Collections & Content',
            'purchases' => 'Purchases & Transactions',
            'comments' => 'Comments & Reviews',
            'messages' => 'Messages & Communications',
        ],
        'limit_reached' => 'You have reached the maximum number of exports allowed per day.',
        'existing_in_progress' => 'You already have an export in progress. Please wait for it to complete.',
    ],

    // Processing Restrictions
    'restriction' => [
        'title' => 'Limit Data Processing',
        'description' => 'You can request to limit how we process your data in certain circumstances.',
        'active_restrictions' => 'Active Restrictions',
        'no_active_restrictions' => 'You have no active processing restrictions.',
        'request_new' => 'Request New Restriction',
        'restriction_type' => 'Restriction Type',
        'restriction_reason' => 'Restriction Reason',
        'data_categories' => 'Data Categories',
        'notes' => 'Additional Notes',
        'notes_placeholder' => 'Please provide any additional details to help us understand your request...',
        'submit_button' => 'Submit Restriction Request',
        'remove_button' => 'Remove Restriction',
        'processing_restriction_success' => 'Your processing restriction request has been submitted.',
        'processing_restriction_failed' => 'There was an error submitting your processing restriction request.',
        'processing_restriction_system_error' => 'A system error occurred while processing your request.',
        'processing_restriction_removed' => 'The processing restriction has been removed.',
        'processing_restriction_removal_failed' => 'There was an error removing the processing restriction.',
        'unauthorized_action' => 'You are not authorized to perform this action.',
        'date_submitted' => 'Date Submitted',
        'expiry_date' => 'Expires On',
        'never_expires' => 'Never Expires',
        'status' => 'Status',
        'limit_reached' => 'You have reached the maximum number of active restrictions allowed.',
        'categories' => [
            'profile' => 'Profile Information',
            'activity' => 'Activity Tracking',
            'preferences' => 'Preferences & Settings',
            'collections' => 'Collections & Content',
            'purchases' => 'Purchases & Transactions',
            'comments' => 'Comments & Reviews',
            'messages' => 'Messages & Communications',
        ],
        'types' => [
            'restrict_processing' => 'Restrict All Processing',
            'restrict_automated_decisions' => 'Restrict Automated Decision-Making',
            'restrict_marketing' => 'Restrict Marketing Processing',
            'restrict_analytics' => 'Restrict Analytics Processing',
            'restrict_third_party' => 'Restrict Third-Party Sharing',
        ],
        'reasons' => [
            'accuracy_dispute' => 'I dispute the accuracy of my data',
            'processing_unlawful' => 'The processing is unlawful',
            'no_longer_needed' => 'You no longer need my data but I need it for legal claims',
            'objection_pending' => 'I\'ve objected to processing and awaiting verification',
            'legitimate_interest' => 'Compelling legitimate grounds',
            'other' => 'Other reason (please specify in notes)',
        ],
    ],

    // Account Deletion
    'deletion' => [
        'title' => 'Delete My Account',
        'description' => 'This will initiate the process to delete your account and all associated data.',
        'warning' => 'Warning: Account deletion is permanent and cannot be reversed.',
        'processing_delay' => 'Your account will be scheduled for deletion in :days days.',
        'confirm_deletion' => 'I understand that this action is permanent and cannot be undone.',
        'password_confirmation' => 'Please enter your password to confirm',
        'reason' => 'Reason for deletion (optional)',
        'additional_comments' => 'Additional comments (optional)',
        'submit_button' => 'Request Account Deletion',
        'request_submitted' => 'Your account deletion request has been submitted.',
        'request_error' => 'There was an error submitting your account deletion request.',
        'pending_deletion' => 'Your account is scheduled for deletion on :date.',
        'cancel_deletion' => 'Cancel Deletion Request',
        'cancellation_success' => 'Your account deletion request has been cancelled.',
        'cancellation_error' => 'There was an error cancelling your account deletion request.',
        'reasons' => [
            'no_longer_needed' => 'I no longer need this service',
            'privacy_concerns' => 'Privacy concerns',
            'moving_to_competitor' => 'Moving to another service',
            'unhappy_with_service' => 'Unhappy with the service',
            'other' => 'Other reason',
        ],
        'confirmation_email' => [
            'subject' => 'Account Deletion Request Confirmation',
            'line1' => 'We have received your request to delete your account.',
            'line2' => 'Your account is scheduled for deletion on :date.',
            'line3' => 'If you did not request this, please contact us immediately.',
        ],
        'data_retention_notice' => 'Please note that some anonymized data may be retained for legal and analytical purposes.',
        'blockchain_data_notice' => 'Data stored on blockchain cannot be fully deleted due to the immutable nature of the technology.',
    ],

    // Breach Report
    'breach' => [
        'title' => 'Report a Data Breach',
        'description' => 'If you believe there has been a breach of your personal data, please report it here.',
        'reporter_name' => 'Your Name',
        'reporter_email' => 'Your Email',
        'incident_date' => 'When did the incident occur?',
        'breach_description' => 'Describe the potential breach',
        'breach_description_placeholder' => 'Please provide as much detail as possible about the potential data breach...',
        'affected_data' => 'What data do you believe was affected?',
        'affected_data_placeholder' => 'E.g., personal information, financial data, etc.',
        'discovery_method' => 'How did you discover this potential breach?',
        'supporting_evidence' => 'Supporting Evidence (optional)',
        'upload_evidence' => 'Upload Evidence',
        'file_types' => 'Accepted file types: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Maximum file size: 10MB',
        'consent_to_contact' => 'I consent to being contacted regarding this report',
        'submit_button' => 'Submit Breach Report',
        'report_submitted' => 'Your breach report has been submitted.',
        'report_error' => 'There was an error submitting your breach report.',
        'thank_you' => 'Thank you for your report',
        'thank_you_message' => 'Thank you for reporting this potential breach. Our data protection team will investigate and may contact you for more information.',
        'breach_description_min' => 'Please provide at least 20 characters describing the potential breach.',
    ],

    // Activity Log
    'activity' => [
        'title' => 'My GDPR Activity Log',
        'description' => 'View a record of all your GDPR-related activities and requests.',
        'no_activities' => 'No activities found.',
        'date' => 'Date',
        'activity' => 'Activity',
        'details' => 'Details',
        'ip_address' => 'IP Address',
        'user_agent' => 'User Agent',
        'download_log' => 'Download Activity Log',
        'filter' => 'Filter Activities',
        'filter_all' => 'All Activities',
        'filter_consent' => 'Consent Activities',
        'filter_export' => 'Data Export Activities',
        'filter_restriction' => 'Processing Restriction Activities',
        'filter_deletion' => 'Account Deletion Activities',
        'types' => [
            'consent_updated' => 'Consent Preferences Updated',
            'data_export_requested' => 'Data Export Requested',
            'data_export_completed' => 'Data Export Completed',
            'data_export_downloaded' => 'Data Export Downloaded',
            'processing_restricted' => 'Processing Restriction Requested',
            'processing_restriction_removed' => 'Processing Restriction Removed',
            'account_deletion_requested' => 'Account Deletion Requested',
            'account_deletion_cancelled' => 'Account Deletion Cancelled',
            'account_deletion_completed' => 'Account Deletion Completed',
            'breach_reported' => 'Data Breach Reported',
        ],
    ],

    // Validation
    'validation' => [
        'consents_required' => 'Consent preferences are required.',
        'consents_format' => 'Consent preferences format is invalid.',
        'consent_value_required' => 'Consent value is required.',
        'consent_value_boolean' => 'Consent value must be a boolean.',
        'format_required' => 'Export format is required.',
        'data_categories_required' => 'At least one data category must be selected.',
        'data_categories_format' => 'Data categories format is invalid.',
        'data_categories_min' => 'At least one data category must be selected.',
        'data_categories_distinct' => 'Data categories must be distinct.',
        'export_password_required' => 'Password is required when password protection is enabled.',
        'export_password_min' => 'Password must be at least 8 characters.',
        'restriction_type_required' => 'Restriction type is required.',
        'restriction_reason_required' => 'Restriction reason is required.',
        'notes_max' => 'Notes cannot exceed 500 characters.',
        'reporter_name_required' => 'Your name is required.',
        'reporter_email_required' => 'Your email is required.',
        'reporter_email_format' => 'Please enter a valid email address.',
        'incident_date_required' => 'Incident date is required.',
        'incident_date_format' => 'Incident date must be a valid date.',
        'incident_date_past' => 'Incident date must be in the past or today.',
        'breach_description_required' => 'Breach description is required.',
        'breach_description_min' => 'Breach description must be at least 20 characters.',
        'affected_data_required' => 'Affected data information is required.',
        'discovery_method_required' => 'Discovery method is required.',
        'supporting_evidence_format' => 'Evidence must be a PDF, JPG, JPEG, PNG, TXT, DOC or DOCX file.',
        'supporting_evidence_max' => 'Evidence file cannot exceed 10MB.',
        'consent_to_contact_required' => 'Consent to contact is required.',
        'consent_to_contact_accepted' => 'Consent to contact must be accepted.',
        'required_consent_message' => 'This consent is required to use the platform.',
        'confirm_deletion_required' => 'You must confirm that you understand the consequences of account deletion.',
        'form_error_title' => 'Please fix the errors below',
        'form_error_message' => 'There are one or more errors in the form that need to be fixed.',
    ],

    // Error Messages
    'errors' => [
        'general' => 'An unexpected error occurred.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'forbidden' => 'This action is forbidden.',
        'not_found' => 'The requested resource was not found.',
        'validation_failed' => 'The submitted data is invalid.',
        'rate_limited' => 'Too many requests. Please try again later.',
        'service_unavailable' => 'The service is currently unavailable. Please try again later.',
    ],
];
