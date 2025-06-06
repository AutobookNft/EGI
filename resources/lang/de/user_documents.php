<?php

/**
 * @Oracode Translation File: Document Management - Deutsch
 * 🎯 Purpose: Complete German translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for German users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\De
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */

return [
    // SEITENTITEL UND KOPFZEILEN
    'management_title' => 'Dokumentenverwaltung',
    'management_subtitle' => 'Lade deine Identitätsdokumente hoch und verwalte sie',
    'upload_title' => 'Dokument Hochladen',
    'upload_subtitle' => 'Lade ein neues Dokument zur Überprüfung hoch',
    'verification_title' => 'Überprüfungsstatus',
    'verification_subtitle' => 'Prüfe den Status deiner Dokumente',

    // DOKUMENTENTYPEN
    'types' => [
        'identity_card' => 'Personalausweis',
        'passport' => 'Reisepass',
        'driving_license' => 'Führerschein',
        'fiscal_code_card' => 'Steuerkarte',
        'residence_certificate' => 'Wohnsitzbescheinigung',
        'birth_certificate' => 'Geburtsurkunde',
        'business_registration' => 'Handelsregisterauszug',
        'vat_certificate' => 'USt-IdNr.-Bescheinigung',
        'bank_statement' => 'Kontoauszug',
        'utility_bill' => 'Nebenkostenabrechnung (Adressnachweis)',
        'other' => 'Anderes Dokument',
    ],

    // STATUS DER ÜBERPRÜFUNG
    'status' => [
        'pending' => 'Ausstehend',
        'under_review' => 'In Überprüfung',
        'approved' => 'Genehmigt',
        'rejected' => 'Abgelehnt',
        'expired' => 'Abgelaufen',
        'requires_reupload' => 'Erneutes Hochladen Erforderlich',
    ],

    'status_descriptions' => [
        'pending' => 'Dokument hochgeladen, wartet auf Überprüfung',
        'under_review' => 'Das Dokument wird von unserem Team geprüft',
        'approved' => 'Dokument überprüft und genehmigt',
        'rejected' => 'Dokument abgelehnt. Gründe prüfen und erneut hochladen',
        'expired' => 'Dokument ist abgelaufen. Bitte neue Version hochladen',
        'requires_reupload' => 'Bitte Dokument in besserer Qualität erneut hochladen',
    ],

    // UPLOAD-FORMULAR
    'upload_form' => [
        'document_type' => 'Dokumententyp',
        'document_type_placeholder' => 'Dokumententyp auswählen',
        'document_file' => 'Dokumentdatei',
        'document_file_help' => 'Erlaubte Formate: PDF, JPG, PNG. Max. Größe: 10MB',
        'document_notes' => 'Notizen (Optional)',
        'document_notes_placeholder' => 'Fügen Sie Notizen oder Zusatzinfos hinzu...',
        'expiry_date' => 'Ablaufdatum',
        'expiry_date_placeholder' => 'Ablaufdatum des Dokuments eingeben',
        'expiry_date_help' => 'Ablaufdatum angeben, falls zutreffend',
        'upload_button' => 'Dokument Hochladen',
        'replace_button' => 'Dokument Ersetzen',
    ],

    // DOKUMENTENLISTE
    'list' => [
        'your_documents' => 'Deine Dokumente',
        'no_documents' => 'Keine Dokumente hochgeladen',
        'no_documents_desc' => 'Lade deine Dokumente hoch, um die Identitätsprüfung abzuschließen',
        'document_name' => 'Dokumentenname',
        'upload_date' => 'Hochladedatum',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'download' => 'Herunterladen',
        'replace' => 'Ersetzen',
        'delete' => 'Löschen',
        'view_details' => 'Details Anzeigen',
    ],

    // AKTIONEN UND BUTTONS
    'upload_new' => 'Neues Dokument Hochladen',
    'view_document' => 'Dokument Anzeigen',
    'download_document' => 'Dokument Herunterladen',
    'delete_document' => 'Dokument Löschen',
    'replace_document' => 'Dokument Ersetzen',
    'request_verification' => 'Überprüfung Anfordern',
    'back_to_list' => 'Zurück zur Liste',

    // ERFOLGS- UND FEHLERMELDUNGEN
    'upload_success' => 'Dokument erfolgreich hochgeladen',
    'upload_error' => 'Fehler beim Hochladen des Dokuments',
    'delete_success' => 'Dokument erfolgreich gelöscht',
    'delete_error' => 'Fehler beim Löschen des Dokuments',
    'verification_requested' => 'Überprüfung angefordert. Du erhältst Updates per E-Mail.',
    'verification_completed' => 'Dokumentenüberprüfung abgeschlossen',

    // VALIDIERUNGSNACHRICHTEN
    'validation' => [
        'document_type_required' => 'Dokumententyp ist erforderlich',
        'document_file_required' => 'Dokumentdatei ist erforderlich',
        'document_file_mimes' => 'Das Dokument muss im PDF-, JPG- oder PNG-Format sein',
        'document_file_max' => 'Dokument darf 10MB nicht überschreiten',
        'expiry_date_future' => 'Ablaufdatum muss in der Zukunft liegen',
        'document_already_exists' => 'Du hast bereits ein Dokument dieses Typs hochgeladen',
    ],

    // SICHERHEIT UND PRIVATSPHÄRE
    'security' => [
        'encryption_notice' => 'Alle Dokumente sind verschlüsselt und sicher gespeichert',
        'access_log' => 'Jeder Zugriff auf Dokumente wird zu Sicherheitszwecken protokolliert',
        'retention_policy' => 'Dokumente werden gemäß den geltenden Vorschriften aufbewahrt',
        'delete_warning' => 'Das Löschen eines Dokuments ist unwiderruflich',
        'verification_required' => 'Dokumente werden manuell von unserem Team geprüft',
        'processing_time' => 'Die Überprüfung dauert normalerweise 2-5 Werktage',
    ],

    // DATEIANFORDERUNGEN
    'requirements' => [
        'title' => 'Dokumentenanforderungen',
        'quality' => 'Klares und gut beleuchtetes Bild',
        'completeness' => 'Vollständiges, nicht beschnittenes Dokument',
        'readability' => 'Deutlich lesbarer Text',
        'validity' => 'Gültiges, nicht abgelaufenes Dokument',
        'authenticity' => 'Originaldokument, keine Kopien von Kopien',
        'format' => 'Unterstütztes Format: PDF, JPG, PNG',
        'size' => 'Maximale Größe: 10MB',
    ],

    // ÜBERPRÜFUNGSDETAILS
    'verification' => [
        'process_title' => 'Überprüfungsprozess',
        'step1' => '1. Dokument hochladen',
        'step2' => '2. Automatische Qualitätsprüfung',
        'step3' => '3. Manuelle Überprüfung durch das Team',
        'step4' => '4. Ergebnisbenachrichtigung',
        'rejection_reasons' => 'Häufige Ablehnungsgründe',
        'poor_quality' => 'Unzureichende Bildqualität',
        'incomplete' => 'Unvollständiges oder beschnittenes Dokument',
        'expired' => 'Dokument abgelaufen',
        'unreadable' => 'Unleserlicher Text',
        'wrong_type' => 'Falscher Dokumententyp',
        'suspected_fraud' => 'Verdacht auf Fälschung',
    ],
];

