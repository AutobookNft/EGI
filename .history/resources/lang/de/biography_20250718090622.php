<?php

/**
 * @Oracode Translation: Biography System German Translations
 * 🎯 Purpose: Complete German translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\De
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biografie',
    'biographies' => 'Biografien',
    'chapter' => 'Kapitel',
    'chapters' => 'Kapitel',
    'min_read' => 'Min Lesezeit',
    'public' => 'Öffentlich',
    'private' => 'Privat',
    'completed' => 'Abgeschlossen',
    'draft' => 'Entwurf',
    'save' => 'Speichern',
    'cancel' => 'Abbrechen',
    'edit' => 'Bearbeiten',
    'delete' => 'Löschen',
    'view' => 'Anzeigen',
    'create' => 'Erstellen',
    'gallery' => 'Galerie',
    'media' => 'Medien',
    'video_not_supported' => 'Ihr Browser unterstützt keine HTML5-Videos',
    'link_copied' => 'Link in die Zwischenablage kopiert',
    'share' => 'Teilen',
    'view_profile' => 'Profil anzeigen',
    'discover_more' => 'Mehr entdecken',
    'discover_more_description' => 'Entdecken Sie andere außergewöhnliche Geschichten von Schöpfern und Visionären',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Verwalten Sie Ihre Biografien',
        'subtitle' => 'Erstellen, bearbeiten und organisieren Sie Ihre persönlichen Geschichten',
        'your_biographies' => 'Ihre Biografien',
        'description' => 'Verwalten Sie Ihre persönlichen Geschichten und teilen Sie sie mit der Welt',
        'create_new' => 'Neue Biografie erstellen',
        'create_first' => 'Erstellen Sie Ihre erste Biografie',
        'view_biography' => 'Biografie anzeigen',
        'edit' => 'Bearbeiten',
        'public' => 'Öffentlich',
        'private' => 'Privat',
        'completed' => 'Abgeschlossen',
        'chapters' => 'Kapitel',
        'min_read' => 'Min Lesezeit',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diese Biografie löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
        'delete_error' => 'Fehler beim Löschen der Biografie. Bitte versuchen Sie es erneut.',
        'no_biographies_title' => 'Noch keine Biografien',
        'no_biographies_description' => 'Beginnen Sie, Ihre Geschichte zu erzählen, indem Sie Ihre erste Biografie erstellen. Teilen Sie Ihre Erfahrungen, Projekte und Ihre Vision der Welt.',
        'empty_title' => 'Keine Biografien gefunden',
        'empty_description' => 'Beginnen Sie, Ihre Geschichte zu erzählen, indem Sie Ihre erste Biografie erstellen',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'Ihre Biografie',
        'edit_biography' => 'Biografie bearbeiten',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biografie',
        'no_biography_title' => 'Keine Biografie verfügbar',
        'no_biography_description' => 'Dieser Benutzer hat noch keine öffentliche Biografie erstellt.',
    ],

    // === FORM ===
    'form' => [
        'title' => 'Titel',
        'title_placeholder' => 'Geben Sie den Titel Ihrer Biografie ein',
        'type' => 'Biografie-Typ',
        'content' => 'Inhalt',
        'content_placeholder' => 'Erzählen Sie Ihre Geschichte...',
        'excerpt' => 'Auszug',
        'excerpt_placeholder' => 'Kurze Beschreibung Ihrer Biografie',
        'excerpt_help' => 'Maximal 500 Zeichen. Wird für Vorschauen und Teilen verwendet.',
        'is_public' => 'Öffentlich',
        'is_public_help' => 'Biografie für alle sichtbar machen',
        'is_completed' => 'Abgeschlossen',
        'is_completed_help' => 'Als abgeschlossen markieren',
        'settings' => 'Einstellungen',
        'featured_image' => 'Beitragsbild',
        'featured_image_hint' => 'Laden Sie ein repräsentatives Bild hoch (JPEG, PNG, WebP, max. 2MB). Wird für Vorschauen und Teilen verwendet.',
        'save_biography' => 'Biografie speichern',
        'create_biography' => 'Biografie erstellen',
        'update_biography' => 'Biografie aktualisieren',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Einzeln',
        'single_description' => 'Eine Biografie im Einzelformat, ideal für kurze Erzählungen',
        'chapters' => 'Kapitel',
        'chapters_description' => 'Biografie in Kapiteln organisiert, perfekt für lange und detaillierte Geschichten',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'Titel ist erforderlich',
        'title_max' => 'Titel darf 255 Zeichen nicht überschreiten',
        'content_required' => 'Inhalt ist erforderlich',
        'type_required' => 'Biografie-Typ ist erforderlich',
        'type_invalid' => 'Biografie-Typ ist nicht gültig',
        'excerpt_max' => 'Auszug darf 500 Zeichen nicht überschreiten',
        'slug_unique' => 'Dieser Slug wird bereits verwendet',
        'featured_image_max' => 'Bild darf 2MB nicht überschreiten',
        'featured_image_mimes' => 'Bild muss im JPEG-, PNG- oder WebP-Format sein',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biografie erfolgreich erstellt',
        'updated' => 'Biografie erfolgreich aktualisiert',
        'deleted' => 'Biografie erfolgreich gelöscht',
        'published' => 'Biografie erfolgreich veröffentlicht',
        'unpublished' => 'Biografie erfolgreich privat gemacht',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biografie nicht gefunden',
        'unauthorized' => 'Nicht berechtigt, auf diese Biografie zuzugreifen',
        'create_failed' => 'Fehler beim Erstellen der Biografie',
        'update_failed' => 'Fehler beim Aktualisieren der Biografie',
        'delete_failed' => 'Fehler beim Löschen der Biografie',
        'generic' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Kapiteltitel',
        'content' => 'Kapitelinhalt',
        'date_from' => 'Startdatum',
        'date_to' => 'Enddatum',
        'is_ongoing' => 'Laufend',
        'sort_order' => 'Reihenfolge',
        'is_published' => 'Veröffentlicht',
        'chapter_type' => 'Kapiteltyp',
        'add_chapter' => 'Kapitel hinzufügen',
        'edit_chapter' => 'Kapitel bearbeiten',
        'delete_chapter' => 'Kapitel löschen',
        'reorder_chapters' => 'Kapitel neu ordnen',
        'no_chapters' => 'Noch keine Kapitel',
        'no_chapters_description' => 'Beginnen Sie, Kapitel zu Ihrer Biografie hinzuzufügen',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Medien hochladen',
        'featured_image' => 'Beitragsbild',
        'gallery' => 'Galerie',
        'caption' => 'Beschriftung',
        'alt_text' => 'Alternativer Text',
        'upload_failed' => 'Fehler beim Hochladen der Datei',
        'delete_media' => 'Medien löschen',
        'no_media' => 'Keine Medien hochgeladen',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'Alle Biografien',
        'my_biographies' => 'Meine Biografien',
        'public_biographies' => 'Öffentliche Biografien',
        'create_biography' => 'Biografie erstellen',
        'manage_biographies' => 'Biografien verwalten',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Gesamte Biografien',
        'public_biographies' => 'Öffentliche Biografien',
        'total_chapters' => 'Gesamte Kapitel',
        'total_words' => 'Gesamte Wörter',
        'reading_time' => 'Lesezeit',
        'last_updated' => 'Zuletzt aktualisiert',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Biografie teilen',
        'copy_link' => 'Link kopieren',
        'social_share' => 'In sozialen Medien teilen',
        'embed_code' => 'Einbettungscode',
        'qr_code' => 'QR-Code',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Für alle im Internet sichtbar',
        'private_description' => 'Nur für Sie sichtbar',
        'unlisted_description' => 'Nur mit direktem Link sichtbar',
        'change_privacy' => 'Datenschutz ändern',
        'privacy_updated' => 'Datenschutzeinstellungen aktualisiert',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Zeitleiste anzeigen',
        'hide_timeline' => 'Zeitleiste ausblenden',
        'chronological' => 'Chronologisch',
        'reverse_chronological' => 'Umgekehrt chronologisch',
        'custom_order' => 'Benutzerdefinierte Reihenfolge',
        'date_range' => 'Zeitraum',
        'ongoing' => 'Laufend',
        'present' => 'Gegenwart',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Biografie exportieren',
        'export_pdf' => 'Als PDF exportieren',
        'export_word' => 'Als Word exportieren',
        'export_html' => 'Als HTML exportieren',
        'export_success' => 'Biografie erfolgreich exportiert',
        'export_failed' => 'Fehler beim Exportieren',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Biografien suchen',
        'search_placeholder' => 'Nach Titel, Inhalt oder Autor suchen...',
        'no_results' => 'Keine Ergebnisse gefunden',
        'results_found' => 'Ergebnisse gefunden',
        'filter_by' => 'Filtern nach',
        'filter_type' => 'Typ',
        'filter_date' => 'Datum',
        'filter_author' => 'Autor',
        'clear_filters' => 'Filter löschen',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Kommentare',
        'add_comment' => 'Kommentar hinzufügen',
        'no_comments' => 'Noch keine Kommentare',
        'comment_added' => 'Kommentar erfolgreich hinzugefügt',
        'comment_deleted' => 'Kommentar erfolgreich gelöscht',
        'enable_comments' => 'Kommentare aktivieren',
        'disable_comments' => 'Kommentare deaktivieren',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'Neue Biografie veröffentlicht',
        'biography_updated' => 'Biografie aktualisiert',
        'new_chapter' => 'Neues Kapitel hinzugefügt',
        'chapter_updated' => 'Kapitel aktualisiert',
        'new_comment' => 'Neuer Kommentar erhalten',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Biografie bearbeiten',
        'create_new_biography' => 'Neue Biografie erstellen',
        'tell_story_description' => 'Erzählen Sie Ihre Geschichte und teilen Sie sie mit der Welt',
        'validation_errors' => 'Validierungsfehler',
        'basic_info' => 'Grundinformationen',
        'media_management' => 'Medienverwaltung',
        'settings' => 'Einstellungen',
        'title_required' => 'Titel *',
        'title_placeholder' => 'Geben Sie den Titel Ihrer Biografie ein',
        'content_required' => 'Inhalt *',
        'content_placeholder' => 'Erzählen Sie Ihre Geschichte...',
        'excerpt' => 'Auszug',
        'excerpt_placeholder' => 'Kurze Beschreibung Ihrer Biografie...',
        'excerpt_help' => 'Kurze Beschreibung, die in der Vorschau erscheint',
        'add_chapter' => 'Kapitel hinzufügen',
        'edit_chapter' => 'Bearbeiten',
        'delete_chapter' => 'Löschen',
        'biography_images' => 'Biografie-Bilder',
        'upload_images_help' => 'Laden Sie Bilder für Ihre Biografie hoch. Unterstützte Formate: JPG, PNG, WEBP (Max. 2MB jeweils)',
        'uploading_images' => 'Bilder werden hochgeladen...',
        'uploaded_images' => 'Hochgeladene Bilder',
        'biography_public' => 'Öffentliche Biografie',
        'biography_public_help' => 'Machen Sie Ihre Biografie für alle Benutzer sichtbar',
        'go_back' => 'Zurück',
        'update_biography' => 'Biografie aktualisieren',
        'create_biography' => 'Biografie erstellen',
    ],
];
