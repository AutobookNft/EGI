<?php
/**
 * @Oracode Translation File: Document Management - English
 * 🎯 Purpose: Complete English translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for English users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */


return [
    // PAGE TITLES AND HEADERS
    'management_title' => 'Document Management',
    'management_subtitle' => 'Upload and manage your identity documents',
    'upload_title' => 'Upload Document',
    'upload_subtitle' => 'Upload a new document for verification',
    'verification_title' => 'Verification Status',
    'verification_subtitle' => 'Check the verification status of your documents',

    // DOCUMENT TYPES
    'types' => [
        'identity_card' => 'Identity Card',
        'passport' => 'Passport',
        'driving_license' => 'Driving License',
        'fiscal_code_card' => 'Tax Code Card',
        'residence_certificate' => 'Residence Certificate',
        'birth_certificate' => 'Birth Certificate',
        'business_registration' => 'Business Registration Certificate',
        'vat_certificate' => 'VAT Certificate',
        'bank_statement' => 'Bank Statement',
        'utility_bill' => 'Utility Bill (Proof of Address)',
        'other' => 'Other Document',
    ],

    // VERIFICATION STATUS
    'status' => [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'requires_reupload' => 'Requires Re-upload',
    ],

    'status_descriptions' => [
        'pending' => 'Document uploaded, pending verification',
        'under_review' => 'Document is being verified by our team',
        'approved' => 'Document verified and approved',
        'rejected' => 'Document rejected. Check the reasons and re-upload',
        'expired' => 'Document has expired. Please upload a new version',
        'requires_reupload' => 'You need to re-upload a higher quality document',
    ],

    // UPLOAD FORM
    'upload_form' => [
        'document_type' => 'Document Type',
        'document_type_placeholder' => 'Select document type',
        'document_file' => 'Document File',
        'document_file_help' => 'Supported formats: PDF, JPG, PNG. Max size: 10MB',
        'document_notes' => 'Notes (Optional)',
        'document_notes_placeholder' => 'Add notes or extra info...',
        'expiry_date' => 'Expiry Date',
        'expiry_date_placeholder' => 'Enter document expiry date',
        'expiry_date_help' => 'Enter expiry date if applicable',
        'upload_button' => 'Upload Document',
        'replace_button' => 'Replace Document',
    ],

    // DOCUMENT LIST
    'list' => [
        'your_documents' => 'Your Documents',
        'no_documents' => 'No documents uploaded',
        'no_documents_desc' => 'Upload your documents to complete identity verification',
        'document_name' => 'Document Name',
        'upload_date' => 'Upload Date',
        'status' => 'Status',
        'actions' => 'Actions',
        'download' => 'Download',
        'replace' => 'Replace',
        'delete' => 'Delete',
        'view_details' => 'View Details',
    ],

    // ACTIONS AND BUTTONS
    'upload_new' => 'Upload New Document',
    'view_document' => 'View Document',
    'download_document' => 'Download Document',
    'delete_document' => 'Delete Document',
    'replace_document' => 'Replace Document',
    'request_verification' => 'Request Verification',
    'back_to_list' => 'Back to List',

    // SUCCESS AND ERROR MESSAGES
    'upload_success' => 'Document uploaded successfully',
    'upload_error' => 'Error uploading document',
    'delete_success' => 'Document deleted successfully',
    'delete_error' => 'Error deleting document',
    'verification_requested' => 'Verification requested. You will receive updates via email.',
    'verification_completed' => 'Document verification completed',

    // VALIDATION MESSAGES
    'validation' => [
        'document_type_required' => 'Document type is required',
        'document_file_required' => 'Document file is required',
        'document_file_mimes' => 'Document must be a PDF, JPG, or PNG file',
        'document_file_max' => 'Document must not exceed 10MB',
        'expiry_date_future' => 'Expiry date must be in the future',
        'document_already_exists' => 'You have already uploaded a document of this type',
    ],

    // SECURITY AND PRIVACY
    'security' => [
        'encryption_notice' => 'All documents are encrypted and securely stored',
        'access_log' => 'All document access is logged for security',
        'retention_policy' => 'Documents are kept according to legal requirements',
        'delete_warning' => 'Deleting a document is irreversible',
        'verification_required' => 'Documents are manually verified by our team',
        'processing_time' => 'Verification usually takes 2-5 business days',
    ],

    // FILE REQUIREMENTS
    'requirements' => [
        'title' => 'Document Requirements',
        'quality' => 'Clear and well-lit image',
        'completeness' => 'Full document, not cropped',
        'readability' => 'Clearly readable text',
        'validity' => 'Valid and unexpired document',
        'authenticity' => 'Original document, not photocopies of photocopies',
        'format' => 'Supported format: PDF, JPG, PNG',
        'size' => 'Max size: 10MB',
    ],

    // VERIFICATION DETAILS
    'verification' => [
        'process_title' => 'Verification Process',
        'step1' => '1. Upload document',
        'step2' => '2. Automatic quality check',
        'step3' => '3. Manual team verification',
        'step4' => '4. Notification of result',
        'rejection_reasons' => 'Common Rejection Reasons',
        'poor_quality' => 'Poor image quality',
        'incomplete' => 'Incomplete or cropped document',
        'expired' => 'Expired document',
        'unreadable' => 'Unreadable text',
        'wrong_type' => 'Document type mismatch',
        'suspected_fraud' => 'Suspected forgery',
    ],
];

