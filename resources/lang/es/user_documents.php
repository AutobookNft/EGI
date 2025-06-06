<?php

/**
 * @Oracode Translation File: Document Management - Español
 * 🎯 Purpose: Complete Spanish translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for Spanish users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\Es
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS Y ENCABEZADOS DE PÁGINA
    'management_title' => 'Gestión de Documentos',
    'management_subtitle' => 'Sube y gestiona tus documentos de identidad',
    'upload_title' => 'Subir Documento',
    'upload_subtitle' => 'Sube un nuevo documento para su verificación',
    'verification_title' => 'Estado de Verificación',
    'verification_subtitle' => 'Revisa el estado de verificación de tus documentos',

    // TIPOS DE DOCUMENTOS
    'types' => [
        'identity_card' => 'DNI / Cédula de Identidad',
        'passport' => 'Pasaporte',
        'driving_license' => 'Permiso de Conducir',
        'fiscal_code_card' => 'Tarjeta de Código Fiscal',
        'residence_certificate' => 'Certificado de Residencia',
        'birth_certificate' => 'Certificado de Nacimiento',
        'business_registration' => 'Certificado de Registro Mercantil',
        'vat_certificate' => 'Certificado de IVA',
        'bank_statement' => 'Extracto Bancario',
        'utility_bill' => 'Factura (Prueba de Domicilio)',
        'other' => 'Otro Documento',
    ],

    // ESTADO DE VERIFICACIÓN
    'status' => [
        'pending' => 'Pendiente',
        'under_review' => 'En Revisión',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
        'expired' => 'Caducado',
        'requires_reupload' => 'Requiere Nueva Subida',
    ],

    'status_descriptions' => [
        'pending' => 'Documento subido, pendiente de verificación',
        'under_review' => 'El documento está siendo verificado por nuestro equipo',
        'approved' => 'Documento verificado y aprobado',
        'rejected' => 'Documento rechazado. Revisa los motivos y vuelve a subirlo',
        'expired' => 'Documento caducado. Sube una versión actualizada',
        'requires_reupload' => 'Se requiere subir un documento de mayor calidad',
    ],

    // FORMULARIO DE SUBIDA
    'upload_form' => [
        'document_type' => 'Tipo de Documento',
        'document_type_placeholder' => 'Selecciona el tipo de documento',
        'document_file' => 'Archivo del Documento',
        'document_file_help' => 'Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 10MB',
        'document_notes' => 'Notas (Opcional)',
        'document_notes_placeholder' => 'Añade notas o información adicional...',
        'expiry_date' => 'Fecha de Caducidad',
        'expiry_date_placeholder' => 'Introduce la fecha de caducidad del documento',
        'expiry_date_help' => 'Introduce la fecha de caducidad si es aplicable',
        'upload_button' => 'Subir Documento',
        'replace_button' => 'Reemplazar Documento',
    ],

    // LISTA DE DOCUMENTOS
    'list' => [
        'your_documents' => 'Tus Documentos',
        'no_documents' => 'No hay documentos subidos',
        'no_documents_desc' => 'Sube tus documentos para completar la verificación de identidad',
        'document_name' => 'Nombre del Documento',
        'upload_date' => 'Fecha de Subida',
        'status' => 'Estado',
        'actions' => 'Acciones',
        'download' => 'Descargar',
        'replace' => 'Reemplazar',
        'delete' => 'Eliminar',
        'view_details' => 'Ver Detalles',
    ],

    // ACCIONES Y BOTONES
    'upload_new' => 'Subir Nuevo Documento',
    'view_document' => 'Ver Documento',
    'download_document' => 'Descargar Documento',
    'delete_document' => 'Eliminar Documento',
    'replace_document' => 'Reemplazar Documento',
    'request_verification' => 'Solicitar Verificación',
    'back_to_list' => 'Volver a la Lista',

    // MENSAJES DE ÉXITO Y ERROR
    'upload_success' => 'Documento subido correctamente',
    'upload_error' => 'Error al subir el documento',
    'delete_success' => 'Documento eliminado correctamente',
    'delete_error' => 'Error al eliminar el documento',
    'verification_requested' => 'Verificación solicitada. Recibirás actualizaciones por correo electrónico.',
    'verification_completed' => 'Verificación del documento completada',

    // MENSAJES DE VALIDACIÓN
    'validation' => [
        'document_type_required' => 'El tipo de documento es obligatorio',
        'document_file_required' => 'El archivo del documento es obligatorio',
        'document_file_mimes' => 'El documento debe estar en formato PDF, JPG o PNG',
        'document_file_max' => 'El documento no debe superar los 10MB',
        'expiry_date_future' => 'La fecha de caducidad debe ser futura',
        'document_already_exists' => 'Ya has subido un documento de este tipo',
    ],

    // SEGURIDAD Y PRIVACIDAD
    'security' => [
        'encryption_notice' => 'Todos los documentos están cifrados y almacenados de forma segura',
        'access_log' => 'Todos los accesos a los documentos quedan registrados por seguridad',
        'retention_policy' => 'Los documentos se conservan según la legislación vigente',
        'delete_warning' => 'La eliminación de un documento es irreversible',
        'verification_required' => 'Los documentos son verificados manualmente por nuestro equipo',
        'processing_time' => 'La verificación suele tardar entre 2 y 5 días laborables',
    ],

    // REQUISITOS DE ARCHIVO
    'requirements' => [
        'title' => 'Requisitos del Documento',
        'quality' => 'Imagen nítida y bien iluminada',
        'completeness' => 'Documento completo, no recortado',
        'readability' => 'Texto claramente legible',
        'validity' => 'Documento válido y no caducado',
        'authenticity' => 'Documento original, no fotocopias de fotocopias',
        'format' => 'Formato admitido: PDF, JPG, PNG',
        'size' => 'Tamaño máximo: 10MB',
    ],

    // DETALLES DE VERIFICACIÓN
    'verification' => [
        'process_title' => 'Proceso de Verificación',
        'step1' => '1. Subida del documento',
        'step2' => '2. Control automático de calidad',
        'step3' => '3. Verificación manual por el equipo',
        'step4' => '4. Notificación del resultado',
        'rejection_reasons' => 'Motivos de Rechazo Comunes',
        'poor_quality' => 'Calidad de imagen insuficiente',
        'incomplete' => 'Documento incompleto o recortado',
        'expired' => 'Documento caducado',
        'unreadable' => 'Texto ilegible',
        'wrong_type' => 'Tipo de documento incorrecto',
        'suspected_fraud' => 'Sospecha de falsificación',
    ],
];

