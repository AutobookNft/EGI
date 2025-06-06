<?php

/**
 * @Oracode Translation File: Document Management - Italian
 * 🎯 Purpose: Complete Italian translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for Italian users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\It
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */

return [
    // ===================================================================
    // PAGE TITLES AND HEADERS
    // ===================================================================
    'management_title' => 'Gestione Documenti',
    'management_subtitle' => 'Carica e gestisci i tuoi documenti di identità',
    'upload_title' => 'Carica Documento',
    'upload_subtitle' => 'Carica un nuovo documento per la verifica',
    'verification_title' => 'Stato Verifica',
    'verification_subtitle' => 'Controlla lo stato di verifica dei tuoi documenti',

    // ===================================================================
    // DOCUMENT TYPES
    // ===================================================================
    'types' => [
        'identity_card' => 'Carta d\'Identità',
        'passport' => 'Passaporto',
        'driving_license' => 'Patente di Guida',
        'fiscal_code_card' => 'Tessera Codice Fiscale',
        'residence_certificate' => 'Certificato di Residenza',
        'birth_certificate' => 'Certificato di Nascita',
        'business_registration' => 'Visura Camerale',
        'vat_certificate' => 'Certificato Partita IVA',
        'bank_statement' => 'Estratto Conto Bancario',
        'utility_bill' => 'Bolletta (Prova Indirizzo)',
        'other' => 'Altro Documento',
    ],

    // ===================================================================
    // VERIFICATION STATUS
    // ===================================================================
    'status' => [
        'pending' => 'In Attesa',
        'under_review' => 'In Verifica',
        'approved' => 'Approvato',
        'rejected' => 'Rifiutato',
        'expired' => 'Scaduto',
        'requires_reupload' => 'Richiede Ricaricamento',
    ],

    'status_descriptions' => [
        'pending' => 'Documento caricato, in attesa di verifica',
        'under_review' => 'Il documento è in fase di verifica da parte del nostro team',
        'approved' => 'Documento verificato e approvato',
        'rejected' => 'Documento rifiutato. Controlla i motivi e ricarica',
        'expired' => 'Il documento è scaduto. Carica una versione aggiornata',
        'requires_reupload' => 'È necessario ricaricare il documento con qualità migliore',
    ],

    // ===================================================================
    // UPLOAD FORM
    // ===================================================================
    'upload_form' => [
        'document_type' => 'Tipo Documento',
        'document_type_placeholder' => 'Seleziona il tipo di documento',
        'document_file' => 'File Documento',
        'document_file_help' => 'Formati supportati: PDF, JPG, PNG. Dimensione massima: 10MB',
        'document_notes' => 'Note (Facoltativo)',
        'document_notes_placeholder' => 'Aggiungi note o informazioni aggiuntive...',
        'expiry_date' => 'Data di Scadenza',
        'expiry_date_placeholder' => 'Inserisci la data di scadenza del documento',
        'expiry_date_help' => 'Inserisci la data di scadenza se applicabile',
        'upload_button' => 'Carica Documento',
        'replace_button' => 'Sostituisci Documento',
    ],

    // ===================================================================
    // DOCUMENT LIST
    // ===================================================================
    'list' => [
        'your_documents' => 'I Tuoi Documenti',
        'no_documents' => 'Nessun documento caricato',
        'no_documents_desc' => 'Carica i tuoi documenti per completare la verifica dell\'identità',
        'document_name' => 'Nome Documento',
        'upload_date' => 'Data Caricamento',
        'status' => 'Stato',
        'actions' => 'Azioni',
        'download' => 'Scarica',
        'replace' => 'Sostituisci',
        'delete' => 'Elimina',
        'view_details' => 'Dettagli',
    ],

    // ===================================================================
    // ACTIONS AND BUTTONS
    // ===================================================================
    'upload_new' => 'Carica Nuovo Documento',
    'view_document' => 'Visualizza Documento',
    'download_document' => 'Scarica Documento',
    'delete_document' => 'Elimina Documento',
    'replace_document' => 'Sostituisci Documento',
    'request_verification' => 'Richiedi Verifica',
    'back_to_list' => 'Torna all\'Elenco',

    // ===================================================================
    // SUCCESS AND ERROR MESSAGES
    // ===================================================================
    'upload_success' => 'Documento caricato con successo',
    'upload_error' => 'Errore durante il caricamento del documento',
    'delete_success' => 'Documento eliminato con successo',
    'delete_error' => 'Errore durante l\'eliminazione del documento',
    'verification_requested' => 'Verifica documento richiesta. Riceverai aggiornamenti via email.',
    'verification_completed' => 'Verifica documento completata',

    // ===================================================================
    // VALIDATION MESSAGES
    // ===================================================================
    'validation' => [
        'document_type_required' => 'Il tipo di documento è obbligatorio',
        'document_file_required' => 'Il file del documento è obbligatorio',
        'document_file_mimes' => 'Il documento deve essere in formato PDF, JPG o PNG',
        'document_file_max' => 'Il documento non può superare i 10MB',
        'expiry_date_future' => 'La data di scadenza deve essere futura',
        'document_already_exists' => 'Hai già caricato un documento di questo tipo',
    ],

    // ===================================================================
    // SECURITY AND PRIVACY
    // ===================================================================
    'security' => [
        'encryption_notice' => 'Tutti i documenti sono crittografati e conservati in sicurezza',
        'access_log' => 'Ogni accesso ai documenti è registrato per sicurezza',
        'retention_policy' => 'I documenti sono conservati secondo le normative vigenti',
        'delete_warning' => 'L\'eliminazione di un documento è irreversibile',
        'verification_required' => 'I documenti sono verificati manualmente dal nostro team',
        'processing_time' => 'La verifica richiede normalmente 2-5 giorni lavorativi',
    ],

    // ===================================================================
    // FILE REQUIREMENTS
    // ===================================================================
    'requirements' => [
        'title' => 'Requisiti Documento',
        'quality' => 'Immagine nitida e ben illuminata',
        'completeness' => 'Documento completo e non ritagliato',
        'readability' => 'Testo chiaramente leggibile',
        'validity' => 'Documento valido e non scaduto',
        'authenticity' => 'Documento originale, non fotocopie di fotocopie',
        'format' => 'Formato supportato: PDF, JPG, PNG',
        'size' => 'Dimensione massima: 10MB',
    ],

    // ===================================================================
    // VERIFICATION DETAILS
    // ===================================================================
    'verification' => [
        'process_title' => 'Processo di Verifica',
        'step1' => '1. Caricamento documento',
        'step2' => '2. Controllo automatico qualità',
        'step3' => '3. Verifica manuale del team',
        'step4' => '4. Notifica risultato',
        'rejection_reasons' => 'Motivi di Rifiuto Comuni',
        'poor_quality' => 'Qualità immagine insufficiente',
        'incomplete' => 'Documento incompleto o ritagliato',
        'expired' => 'Documento scaduto',
        'unreadable' => 'Testo illeggibile',
        'wrong_type' => 'Tipo di documento non corrispondente',
        'suspected_fraud' => 'Sospetta contraffazione',
    ],
];
