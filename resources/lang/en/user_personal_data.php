<?php

/**
 * @Oracode Translation File: Personal Data Management - English
 * 🎯 Purpose: Complete English translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for English market compliance and user trust
 *
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // PAGE TITLES AND HEADERS
    'management_title' => 'Personal Data Management',
    'management_subtitle' => 'Manage your personal data in GDPR compliance',
    'edit_title' => 'Edit Personal Data',
    'edit_subtitle' => 'Securely update your personal information',
    'export_title' => 'Export Personal Data',
    'export_subtitle' => 'Download a complete copy of your personal data',
    'deletion_title' => 'Data Deletion Request',
    'deletion_subtitle' => 'Request permanent deletion of your personal data',

    // FORM SECTIONS
    'basic_information' => 'Basic Information',
    'basic_description' => 'Key identification data',
    'fiscal_information' => 'Fiscal Information',
    'fiscal_description' => 'Tax code and data for compliance',
    'address_information' => 'Address Information',
    'address_description' => 'Residence and domicile address',
    'contact_information' => 'Contact Information',
    'contact_description' => 'Phone and other contact details',
    'identity_verification' => 'Identity Verification',
    'identity_description' => 'Verify your identity for sensitive changes',

    // FORM FIELDS
    'first_name' => 'First Name',
    'first_name_placeholder' => 'Enter your first name',
    'last_name' => 'Last Name',
    'last_name_placeholder' => 'Enter your last name',
    'birth_date' => 'Date of Birth',
    'birth_date_placeholder' => 'Select your date of birth',
    'birth_place' => 'Place of Birth',
    'birth_place_placeholder' => 'City and province of birth',
    'gender' => 'Gender',
    'gender_male' => 'Male',
    'gender_female' => 'Female',
    'gender_other' => 'Other',
    'gender_prefer_not_say' => 'Prefer not to say',

    // Fiscal Fields
    'tax_code' => 'Tax Code',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Your Italian tax code (16 characters)',
    'id_card_number' => 'Identity Card Number',
    'id_card_number_placeholder' => 'Identity document number',
    'passport_number' => 'Passport Number',
    'passport_number_placeholder' => 'Passport number (if available)',
    'driving_license' => 'Driving License',
    'driving_license_placeholder' => 'Driving license number',

    // Address Fields
    'street_address' => 'Street Address',
    'street_address_placeholder' => 'Street, house number',
    'city' => 'City',
    'city_placeholder' => 'City name',
    'postal_code' => 'Postal Code',
    'postal_code_placeholder' => '00100',
    'province' => 'Province',
    'province_placeholder' => 'Province code (e.g. RM)',
    'region' => 'Region',
    'region_placeholder' => 'Region name',
    'country' => 'Country',
    'country_placeholder' => 'Select country',

    // Contact Fields
    'phone' => 'Phone',
    'phone_placeholder' => '+39 123 456 7890',
    'mobile' => 'Mobile',
    'mobile_placeholder' => '+39 123 456 7890',
    'emergency_contact' => 'Emergency Contact',
    'emergency_contact_placeholder' => 'Name and phone',

    // PRIVACY AND CONSENT
    'consent_management' => 'Consent Management',
    'consent_description' => 'Manage your consent for data processing',
    'consent_required' => 'Required Consent',
    'consent_optional' => 'Optional Consent',
    'consent_marketing' => 'Marketing and Communications',
    'consent_marketing_desc' => 'Consent to receive marketing communications',
    'consent_profiling' => 'Profiling',
    'consent_profiling_desc' => 'Consent for profiling and analytics',
    'consent_analytics' => 'Analytics',
    'consent_analytics_desc' => 'Consent for anonymized statistical analysis',
    'consent_third_party' => 'Third Parties',
    'consent_third_party_desc' => 'Consent for sharing with selected partners',

    // ACTIONS AND BUTTONS
    'update_data' => 'Update Data',
    'save_changes' => 'Save Changes',
    'cancel_changes' => 'Cancel',
    'export_data' => 'Export Data',
    'request_deletion' => 'Request Deletion',
    'verify_identity' => 'Verify Identity',
    'confirm_changes' => 'Confirm Changes',
    'back_to_profile' => 'Back to Profile',

    // SUCCESS AND ERROR MESSAGES
    'update_success' => 'Personal data updated successfully',
    'update_error' => 'Error updating personal data',
    'validation_error' => 'Some fields contain errors. Please check and try again.',
    'identity_verification_required' => 'Identity verification required for this action',
    'identity_verification_failed' => 'Identity verification failed. Please try again.',
    'export_started' => 'Data export started. You will receive an email when it is ready.',
    'export_ready' => 'Your data export is ready for download',
    'deletion_requested' => 'Deletion request submitted. It will be processed within 30 days.',

    // VALIDATION MESSAGES
    'validation' => [
        'first_name_required' => 'First name is required',
        'last_name_required' => 'Last name is required',
        'birth_date_required' => 'Date of birth is required',
        'birth_date_valid' => 'Date of birth must be valid',
        'birth_date_age' => 'You must be at least 13 years old to register',
        'tax_code_invalid' => 'Tax code is not valid',
        'tax_code_format' => 'Tax code must be 16 characters',
        'phone_invalid' => 'Phone number is not valid',
        'postal_code_invalid' => 'Postal code is not valid for the selected country',
        'country_required' => 'Country is required',
    ],

    // GDPR NOTICES
    'gdpr_notices' => [
        'data_processing_info' => 'Your personal data is processed in accordance with GDPR (EU) 2016/679',
        'data_controller' => 'Data controller: FlorenceEGI S.r.l.',
        'data_purpose' => 'Purpose: User account management and platform services',
        'data_retention' => 'Retention: Data is kept for as long as necessary for requested services',
        'data_rights' => 'Rights: You can access, rectify, delete or restrict the processing of your data',
        'data_contact' => 'To exercise your rights contact: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Warning: You are editing sensitive data. Identity verification is required.',
        'audit_notice' => 'All changes to personal data are logged for security',
    ],

    // EXPORT FUNCTIONALITY
    'export' => [
        'formats' => [
            'json' => 'JSON (Machine Readable)',
            'pdf' => 'PDF (Human Readable)',
            'csv' => 'CSV (Spreadsheet)',
        ],
        'categories' => [
            'basic' => 'Basic Information',
            'fiscal' => 'Fiscal Data',
            'address' => 'Address Data',
            'contact' => 'Contact Information',
            'consents' => 'Consents and Preferences',
            'audit' => 'Change Log',
        ],
        'select_format' => 'Select export format',
        'select_categories' => 'Select categories to export',
        'generate_export' => 'Generate Export',
        'download_ready' => 'Download Ready',
        'download_expires' => 'Download link expires in 7 days',
    ],

    // DELETION WORKFLOW
    'deletion' => [
        'confirm_title' => 'Confirm Data Deletion',
        'warning_irreversible' => 'WARNING: This action is irreversible',
        'warning_account' => 'Deleting data will permanently close your account',
        'warning_backup' => 'Data may be retained in backups for up to 90 days',
        'reason_required' => 'Reason for request (optional)',
        'reason_placeholder' => 'You can specify the reason for deletion...',
        'final_confirmation' => 'I confirm I want to permanently delete my personal data',
        'type_delete' => 'Type "DELETE" to confirm',
        'submit_request' => 'Submit Deletion Request',
        'request_submitted' => 'Deletion request submitted successfully',
        'processing_time' => 'Request will be processed within 30 business days',
    ],
];

