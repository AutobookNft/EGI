<?php

/**
 * @Oracode Translation File: Personal Data Management - Español
 * 🎯 Purpose: Complete Spanish translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for Spanish market compliance and user trust
 *
 * @package Lang\Es
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS Y ENCABEZADOS DE PÁGINA
    'management_title' => 'Gestión de Datos Personales',
    'management_subtitle' => 'Gestiona tus datos personales en conformidad con el RGPD',
    'edit_title' => 'Editar Datos Personales',
    'edit_subtitle' => 'Actualiza tu información personal de forma segura',
    'export_title' => 'Exportar Datos Personales',
    'export_subtitle' => 'Descarga una copia completa de tus datos personales',
    'deletion_title' => 'Solicitud de Eliminación de Datos',
    'deletion_subtitle' => 'Solicita la eliminación permanente de tus datos personales',

    // SECCIONES DEL FORMULARIO
    'basic_information' => 'Información Básica',
    'basic_description' => 'Datos básicos de identificación',
    'fiscal_information' => 'Información Fiscal',
    'fiscal_description' => 'Código fiscal y datos para obligaciones fiscales',
    'address_information' => 'Información de Domicilio',
    'address_description' => 'Dirección de residencia y domicilio',
    'contact_information' => 'Información de Contacto',
    'contact_description' => 'Teléfono y otros datos de contacto',
    'identity_verification' => 'Verificación de Identidad',
    'identity_description' => 'Verifica tu identidad para cambios sensibles',

    // CAMPOS DEL FORMULARIO
    'first_name' => 'Nombre',
    'first_name_placeholder' => 'Introduce tu nombre',
    'last_name' => 'Apellido',
    'last_name_placeholder' => 'Introduce tu apellido',
    'birth_date' => 'Fecha de Nacimiento',
    'birth_date_placeholder' => 'Selecciona tu fecha de nacimiento',
    'birth_place' => 'Lugar de Nacimiento',
    'birth_place_placeholder' => 'Ciudad y provincia de nacimiento',
    'gender' => 'Género',
    'gender_male' => 'Masculino',
    'gender_female' => 'Femenino',
    'gender_other' => 'Otro',
    'gender_prefer_not_say' => 'Prefiero no decirlo',

    // Campos fiscales
    'tax_code' => 'Código Fiscal',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Tu código fiscal italiano (16 caracteres)',
    'id_card_number' => 'Número de DNI',
    'id_card_number_placeholder' => 'Número del documento de identidad',
    'passport_number' => 'Número de Pasaporte',
    'passport_number_placeholder' => 'Número de pasaporte (si tienes)',
    'driving_license' => 'Permiso de Conducir',
    'driving_license_placeholder' => 'Número del permiso de conducir',

    // Campos de dirección
    'street_address' => 'Dirección',
    'street_address_placeholder' => 'Calle, número',
    'city' => 'Ciudad',
    'city_placeholder' => 'Nombre de la ciudad',
    'postal_code' => 'Código Postal',
    'postal_code_placeholder' => '00100',
    'province' => 'Provincia',
    'province_placeholder' => 'Código de provincia (ej. RM)',
    'region' => 'Región',
    'region_placeholder' => 'Nombre de la región',
    'country' => 'País',
    'country_placeholder' => 'Selecciona el país',

    // Campos de contacto
    'phone' => 'Teléfono',
    'phone_placeholder' => '+34 123 456 789',
    'mobile' => 'Móvil',
    'mobile_placeholder' => '+34 123 456 789',
    'emergency_contact' => 'Contacto de Emergencia',
    'emergency_contact_placeholder' => 'Nombre y teléfono',

    // PRIVACIDAD Y CONSENTIMIENTO
    'consent_management' => 'Gestión de Consentimientos',
    'consent_description' => 'Gestiona tus consentimientos para el tratamiento de datos',
    'consent_required' => 'Consentimiento Obligatorio',
    'consent_optional' => 'Consentimiento Opcional',
    'consent_marketing' => 'Marketing y Comunicaciones',
    'consent_marketing_desc' => 'Consentimiento para recibir comunicaciones comerciales',
    'consent_profiling' => 'Perfilado',
    'consent_profiling_desc' => 'Consentimiento para actividades de perfilado y análisis',
    'consent_analytics' => 'Análisis',
    'consent_analytics_desc' => 'Consentimiento para análisis estadísticos anonimizados',
    'consent_third_party' => 'Terceras Partes',
    'consent_third_party_desc' => 'Consentimiento para compartir con socios seleccionados',

    // ACCIONES Y BOTONES
    'update_data' => 'Actualizar Datos',
    'save_changes' => 'Guardar Cambios',
    'cancel_changes' => 'Cancelar',
    'export_data' => 'Exportar Datos',
    'request_deletion' => 'Solicitar Eliminación',
    'verify_identity' => 'Verificar Identidad',
    'confirm_changes' => 'Confirmar Cambios',
    'back_to_profile' => 'Volver al Perfil',

    // MENSAJES DE ÉXITO Y ERROR
    'update_success' => 'Datos personales actualizados correctamente',
    'update_error' => 'Error al actualizar los datos personales',
    'validation_error' => 'Algunos campos contienen errores. Revísalos y vuelve a intentarlo.',
    'identity_verification_required' => 'Se requiere verificación de identidad para esta operación',
    'identity_verification_failed' => 'La verificación de identidad falló. Intenta de nuevo.',
    'export_started' => 'Exportación de datos iniciada. Recibirás un email cuando esté lista.',
    'export_ready' => 'Tu exportación de datos está lista para descargar',
    'deletion_requested' => 'Solicitud de eliminación enviada. Se procesará en un plazo de 30 días.',

    // MENSAJES DE VALIDACIÓN
    'validation' => [
        'first_name_required' => 'El nombre es obligatorio',
        'last_name_required' => 'El apellido es obligatorio',
        'birth_date_required' => 'La fecha de nacimiento es obligatoria',
        'birth_date_valid' => 'La fecha de nacimiento debe ser válida',
        'birth_date_age' => 'Debes tener al menos 13 años para registrarte',
        'tax_code_invalid' => 'El código fiscal no es válido',
        'tax_code_format' => 'El código fiscal debe tener 16 caracteres',
        'phone_invalid' => 'El número de teléfono no es válido',
        'postal_code_invalid' => 'El código postal no es válido para el país seleccionado',
        'country_required' => 'El país es obligatorio',
    ],

    // AVISOS RGPD
    'gdpr_notices' => [
        'data_processing_info' => 'Tus datos personales son tratados de acuerdo con el RGPD (UE) 2016/679',
        'data_controller' => 'Responsable del tratamiento: FlorenceEGI S.r.l.',
        'data_purpose' => 'Finalidad: Gestión de la cuenta de usuario y servicios de la plataforma',
        'data_retention' => 'Conservación: Los datos se mantienen el tiempo necesario para los servicios solicitados',
        'data_rights' => 'Derechos: Puedes acceder, rectificar, eliminar o limitar el tratamiento de tus datos',
        'data_contact' => 'Para ejercer tus derechos contacta: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Atención: estás editando datos sensibles. Se requiere verificación de identidad.',
        'audit_notice' => 'Todos los cambios en los datos personales se registran por seguridad',
    ],

    // FUNCIONALIDAD DE EXPORTACIÓN
    'export' => [
        'formats' => [
            'json' => 'JSON (Legible por máquina)',
            'pdf' => 'PDF (Legible por humanos)',
            'csv' => 'CSV (Hoja de cálculo)',
        ],
        'categories' => [
            'basic' => 'Información Básica',
            'fiscal' => 'Datos Fiscales',
            'address' => 'Dirección',
            'contact' => 'Información de Contacto',
            'consents' => 'Consensos y Preferencias',
            'audit' => 'Historial de Cambios',
        ],
        'select_format' => 'Selecciona el formato de exportación',
        'select_categories' => 'Selecciona las categorías a exportar',
        'generate_export' => 'Generar Exportación',
        'download_ready' => 'Descarga Lista',
        'download_expires' => 'El enlace de descarga caduca en 7 días',
    ],

    // FLUJO DE ELIMINACIÓN
    'deletion' => [
        'confirm_title' => 'Confirmar Eliminación de Datos',
        'warning_irreversible' => 'ADVERTENCIA: Esta acción es irreversible',
        'warning_account' => 'Eliminar los datos supondrá el cierre permanente de la cuenta',
        'warning_backup' => 'Los datos pueden permanecer en copias de seguridad hasta 90 días',
        'reason_required' => 'Motivo de la solicitud (opcional)',
        'reason_placeholder' => 'Puedes especificar el motivo de la eliminación...',
        'final_confirmation' => 'Confirmo que deseo eliminar permanentemente mis datos personales',
        'type_delete' => 'Escribe "ELIMINAR" para confirmar',
        'submit_request' => 'Enviar Solicitud de Eliminación',
        'request_submitted' => 'Solicitud de eliminación enviada correctamente',
        'processing_time' => 'La solicitud será procesada en un plazo de 30 días laborables',
    ],
];

