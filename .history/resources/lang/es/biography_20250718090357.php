<?php

/**
 * @Oracode Translation: Biography System Spanish Translations
 * 🎯 Purpose: Complete Spanish translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\Es
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biografía',
    'biographies' => 'Biografías',
    'chapter' => 'Capítulo',
    'chapters' => 'Capítulos',
    'min_read' => 'min de lectura',
    'public' => 'Pública',
    'private' => 'Privada',
    'completed' => 'Completada',
    'draft' => 'Borrador',
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'view' => 'Ver',
    'create' => 'Crear',
    'gallery' => 'Galería',
    'media' => 'Medios',
    'video_not_supported' => 'Tu navegador no soporta videos HTML5',
    'link_copied' => 'Enlace copiado al portapapeles',
    'share' => 'Compartir',
    'view_profile' => 'Ver Perfil',
    'discover_more' => 'Descubre más',
    'discover_more_description' => 'Explora otras historias extraordinarias de creadores y visionarios',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Gestiona tus Biografías',
        'subtitle' => 'Crea, edita y organiza tus historias personales',
        'your_biographies' => 'Tus Biografías',
        'description' => 'Gestiona tus historias personales y compártelas con el mundo',
        'create_new' => 'Crear Nueva Biografía',
        'create_first' => 'Crea tu Primera Biografía',
        'view_biography' => 'Ver Biografía',
        'edit' => 'Editar',
        'public' => 'Pública',
        'private' => 'Privada',
        'completed' => 'Completada',
        'chapters' => 'Capítulos',
        'min_read' => 'min lectura',
        'confirm_delete' => '¿Estás seguro de que quieres eliminar esta biografía? Esta acción no se puede deshacer.',
        'delete_error' => 'Error al eliminar la biografía. Inténtalo de nuevo.',
        'no_biographies_title' => 'Aún no hay biografías',
        'no_biographies_description' => 'Comienza a contar tu historia creando tu primera biografía. Comparte tus experiencias, proyectos y tu visión del mundo.',
        'empty_title' => 'No se encontraron biografías',
        'empty_description' => 'Comienza a contar tu historia creando tu primera biografía',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'Tu Biografía',
        'edit_biography' => 'Editar Biografía',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biografía',
        'no_biography_title' => 'No hay biografía disponible',
        'no_biography_description' => 'Este usuario aún no ha creado una biografía pública.',
    ],

    // === FORM ===
    'form' => [
        'title' => 'Título',
        'title_placeholder' => 'Ingresa el título de tu biografía',
        'type' => 'Tipo de Biografía',
        'content' => 'Contenido',
        'content_placeholder' => 'Cuenta tu historia...',
        'excerpt' => 'Extracto',
        'excerpt_placeholder' => 'Breve descripción de tu biografía',
        'excerpt_help' => 'Máximo 500 caracteres. Utilizado para vistas previas y compartir.',
        'is_public' => 'Pública',
        'is_public_help' => 'Hacer la biografía visible para todos',
        'is_completed' => 'Completada',
        'is_completed_help' => 'Marcar como completada',
        'settings' => 'Configuración',
        'featured_image' => 'Imagen destacada',
        'featured_image_hint' => 'Sube una imagen representativa (JPEG, PNG, WebP, máx 2MB). Usada para vistas previas y compartir.',
        'save_biography' => 'Guardar Biografía',
        'create_biography' => 'Crear Biografía',
        'update_biography' => 'Actualizar Biografía',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Única',
        'single_description' => 'Una biografía en formato único, ideal para relatos breves',
        'chapters' => 'Capítulos',
        'chapters_description' => 'Biografía organizada en capítulos, perfecta para historias largas y detalladas',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'El título es obligatorio',
        'title_max' => 'El título no puede superar los 255 caracteres',
        'content_required' => 'El contenido es obligatorio',
        'type_required' => 'El tipo de biografía es obligatorio',
        'type_invalid' => 'El tipo de biografía no es válido',
        'excerpt_max' => 'El extracto no puede superar los 500 caracteres',
        'slug_unique' => 'Este slug ya está en uso',
        'featured_image_max' => 'La imagen no puede superar los 2MB',
        'featured_image_mimes' => 'La imagen debe estar en formato JPEG, PNG o WebP',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biografía creada exitosamente',
        'updated' => 'Biografía actualizada exitosamente',
        'deleted' => 'Biografía eliminada exitosamente',
        'published' => 'Biografía publicada exitosamente',
        'unpublished' => 'Biografía hecha privada exitosamente',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biografía no encontrada',
        'unauthorized' => 'No autorizado para acceder a esta biografía',
        'create_failed' => 'Error al crear la biografía',
        'update_failed' => 'Error al actualizar la biografía',
        'delete_failed' => 'Error al eliminar la biografía',
        'generic' => 'Ocurrió un error. Inténtalo de nuevo.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Título del Capítulo',
        'content' => 'Contenido del Capítulo',
        'date_from' => 'Fecha de Inicio',
        'date_to' => 'Fecha de Fin',
        'is_ongoing' => 'En Curso',
        'sort_order' => 'Orden',
        'is_published' => 'Publicado',
        'chapter_type' => 'Tipo de Capítulo',
        'add_chapter' => 'Añadir Capítulo',
        'edit_chapter' => 'Editar Capítulo',
        'delete_chapter' => 'Eliminar Capítulo',
        'reorder_chapters' => 'Reordenar Capítulos',
        'no_chapters' => 'Aún no hay capítulos',
        'no_chapters_description' => 'Comienza a añadir capítulos a tu biografía',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Subir Medios',
        'featured_image' => 'Imagen Destacada',
        'gallery' => 'Galería',
        'caption' => 'Pie de foto',
        'alt_text' => 'Texto Alternativo',
        'upload_failed' => 'Error al subir el archivo',
        'delete_media' => 'Eliminar Medios',
        'no_media' => 'No hay medios subidos',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'Todas las Biografías',
        'my_biographies' => 'Mis Biografías',
        'public_biographies' => 'Biografías Públicas',
        'create_biography' => 'Crear Biografía',
        'manage_biographies' => 'Gestionar Biografías',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Biografías Totales',
        'public_biographies' => 'Biografías Públicas',
        'total_chapters' => 'Capítulos Totales',
        'total_words' => 'Palabras Totales',
        'reading_time' => 'Tiempo de Lectura',
        'last_updated' => 'Última Actualización',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Compartir Biografía',
        'copy_link' => 'Copiar Enlace',
        'social_share' => 'Compartir en Redes Sociales',
        'embed_code' => 'Código de Incrustación',
        'qr_code' => 'Código QR',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Visible para todos en internet',
        'private_description' => 'Visible solo para ti',
        'unlisted_description' => 'Visible solo con enlace directo',
        'change_privacy' => 'Cambiar Privacidad',
        'privacy_updated' => 'Configuración de privacidad actualizada',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Mostrar Línea de Tiempo',
        'hide_timeline' => 'Ocultar Línea de Tiempo',
        'chronological' => 'Cronológico',
        'reverse_chronological' => 'Cronológico Inverso',
        'custom_order' => 'Orden Personalizado',
        'date_range' => 'Período',
        'ongoing' => 'En curso',
        'present' => 'Presente',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Exportar Biografía',
        'export_pdf' => 'Exportar PDF',
        'export_word' => 'Exportar Word',
        'export_html' => 'Exportar HTML',
        'export_success' => 'Biografía exportada exitosamente',
        'export_failed' => 'Error durante la exportación',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Buscar Biografías',
        'search_placeholder' => 'Buscar por título, contenido o autor...',
        'no_results' => 'No se encontraron resultados',
        'results_found' => 'resultados encontrados',
        'filter_by' => 'Filtrar por',
        'filter_type' => 'Tipo',
        'filter_date' => 'Fecha',
        'filter_author' => 'Autor',
        'clear_filters' => 'Limpiar Filtros',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Comentarios',
        'add_comment' => 'Añadir Comentario',
        'no_comments' => 'Aún no hay comentarios',
        'comment_added' => 'Comentario añadido exitosamente',
        'comment_deleted' => 'Comentario eliminado exitosamente',
        'enable_comments' => 'Habilitar Comentarios',
        'disable_comments' => 'Deshabilitar Comentarios',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'Nueva biografía publicada',
        'biography_updated' => 'Biografía actualizada',
        'new_chapter' => 'Nuevo capítulo añadido',
        'chapter_updated' => 'Capítulo actualizado',
        'new_comment' => 'Nuevo comentario recibido',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Editar Biografía',
        'create_new_biography' => 'Crear Nueva Biografía',
        'tell_story_description' => 'Cuenta tu historia y compártela con el mundo',
        'validation_errors' => 'Errores de Validación',
        'basic_info' => 'Información Básica',
        'media_management' => 'Gestión de Medios',
        'settings' => 'Configuración',
        'title_required' => 'Título *',
        'title_placeholder' => 'Ingresa el título de tu biografía',
        'content_required' => 'Contenido *',
        'content_placeholder' => 'Cuenta tu historia...',
        'excerpt' => 'Extracto',
        'excerpt_placeholder' => 'Breve descripción de tu biografía...',
        'excerpt_help' => 'Descripción breve que aparecerá en vista previa',
        'add_chapter' => 'Añadir Capítulo',
        'edit_chapter' => 'Editar',
        'delete_chapter' => 'Eliminar',
        'biography_images' => 'Imágenes de Biografía',
        'upload_images_help' => 'Sube las imágenes para tu biografía. Formatos soportados: JPG, PNG, WEBP (Máx 2MB cada una)',
        'uploading_images' => 'Subiendo imágenes...',
        'uploaded_images' => 'Imágenes Subidas',
        'biography_public' => 'Biografía Pública',
        'biography_public_help' => 'Hacer tu biografía visible para todos los usuarios',
        'go_back' => 'Volver Atrás',
        'update_biography' => 'Actualizar Biografía',
        'create_biography' => 'Crear Biografía',
    ],
];
