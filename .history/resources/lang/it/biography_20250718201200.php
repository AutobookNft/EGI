<?php

/**
 * @Oracode Translation: Biography System Italian Translations
 * 🎯 Purpose: Complete Italian translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biografia',
    'biographies' => 'Biografie',
    'chapter' => 'Capitolo',
    'chapters' => 'Capitoli',
    'min_read' => 'min di lettura',
    'public' => 'Pubblica',
    'private' => 'Privata',
    'completed' => 'Completata',
    'draft' => 'Bozza',
    'save' => 'Salva',
    'cancel' => 'Annulla',
    'edit' => 'Modifica',
    'delete' => 'Elimina',
    'view' => 'Visualizza',
    'create' => 'Crea',
    'gallery' => 'Galleria',
    'media' => 'Media',
    'video_not_supported' => 'Il tuo browser non supporta i video HTML5',
    'link_copied' => 'Link copiato negli appunti',
    'share' => 'Condividi',
    'view_profile' => 'Visualizza Profilo',
    'discover_more' => 'Scopri di più',
    'discover_more_description' => 'Esplora altre storie straordinarie di creator e visionari',
    'media_label' => 'Media',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Gestisci le tue Biografie',
        'subtitle' => 'Crea, modifica e organizza le tue storie personali',
        'your_biographies' => 'Le tue Biografie',
        'description' => 'Gestisci le tue storie personali e condividile con il mondo',
        'create_new' => 'Crea Nuova Biografia',
        'create_first' => 'Crea la tua Prima Biografia',
        'view_biography' => 'Visualizza Biografia',
        'edit' => 'Modifica',
        'public' => 'Pubblica',
        'private' => 'Privata',
        'completed' => 'Completata',
        'chapters' => 'Capitoli',
        'min_read' => 'min lettura',
        'confirm_delete' => 'Sei sicuro di voler eliminare questa biografia? Questa azione non può essere annullata.',
        'delete_error' => 'Errore durante l\'eliminazione della biografia. Riprova.',
        'no_biographies_title' => 'Nessuna biografia ancora',
        'no_biographies_description' => 'Inizia a raccontare la tua storia creando la tua prima biografia. Condividi le tue esperienze, i tuoi progetti e la tua visione del mondo.',
        'empty_title' => 'Nessuna biografia trovata',
        'empty_description' => 'Inizia a raccontare la tua storia creando la tua prima biografia',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'La tua Biografia',
        'edit_biography' => 'Modifica Biografia',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biografia',
        'no_biography_title' => 'Nessuna biografia disponibile',
        'no_biography_description' => 'Questo utente non ha ancora creato una biografia pubblica.',
    ],

    // === SHOW PAGE SPECIFIC ===
    'main_biography_title' => 'La Biografia',
    'main_gallery_title' => 'Galleria Principale',
    'chapters_section_title' => 'I Capitoli',
    'ongoing' => 'In corso',

    // === FORM ===
    'form' => [
        'title' => 'Titolo',
        'title_placeholder' => 'Inserisci il titolo della tua biografia',
        'type' => 'Tipo di Biografia',
        'content' => 'Contenuto',
        'content_placeholder' => 'Racconta la tua storia...',
        'excerpt' => 'Estratto',
        'excerpt_placeholder' => 'Breve descrizione della tua biografia',
        'excerpt_help' => 'Massimo 500 caratteri. Utilizzato per anteprime e condivisioni.',
        'is_public' => 'Pubblica',
        'is_public_help' => 'Rendi la biografia visibile a tutti',
        'is_completed' => 'Completata',
        'is_completed_help' => 'Marca come completata',
        'settings' => 'Impostazioni',
        'featured_image' => 'Immagine in evidenza',
        'featured_image_hint' => 'Carica un\'immagine rappresentativa (JPEG, PNG, WebP, max 10MB). Usata per anteprime e condivisioni.',
        'save_biography' => 'Salva Biografia',
        'create_biography' => 'Crea Biografia',
        'update_biography' => 'Aggiorna Biografia',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Singola',
        'single_description' => 'Una biografia in formato singolo, ideale per racconti brevi',
        'chapters' => 'Capitoli',
        'chapters_description' => 'Biografia organizzata in capitoli, perfetta per storie lunghe e dettagliate',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'Il titolo è obbligatorio',
        'title_max' => 'Il titolo non può superare i 255 caratteri',
        'content_required' => 'Il contenuto è obbligatorio',
        'type_required' => 'Il tipo di biografia è obbligatorio',
        'type_invalid' => 'Il tipo di biografia non è valido',
        'excerpt_max' => 'L\'estratto non può superare i 500 caratteri',
        'slug_unique' => 'Questo slug è già in uso',
        'featured_image_max' => 'L\'immagine non può superare i 2MB',
        'featured_image_mimes' => 'L\'immagine deve essere in formato JPEG, PNG o WebP',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biografia creata con successo',
        'updated' => 'Biografia aggiornata con successo',
        'deleted' => 'Biografia eliminata con successo',
        'published' => 'Biografia pubblicata con successo',
        'unpublished' => 'Biografia resa privata con successo',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biografia non trovata',
        'unauthorized' => 'Non autorizzato ad accedere a questa biografia',
        'create_failed' => 'Errore durante la creazione della biografia',
        'update_failed' => 'Errore durante l\'aggiornamento della biografia',
        'delete_failed' => 'Errore durante l\'eliminazione della biografia',
        'generic' => 'Si è verificato un errore. Riprova.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Titolo Capitolo',
        'content' => 'Contenuto Capitolo',
        'date_from' => 'Data di Inizio',
        'date_to' => 'Data di Fine',
        'is_ongoing' => 'In Corso',
        'sort_order' => 'Ordine',
        'is_published' => 'Pubblicato',
        'chapter_type' => 'Tipo Capitolo',
        'add_chapter' => 'Aggiungi Capitolo',
        'edit_chapter' => 'Modifica Capitolo',
        'delete_chapter' => 'Elimina Capitolo',
        'reorder_chapters' => 'Riordina Capitoli',
        'no_chapters' => 'Nessun capitolo ancora',
        'no_chapters_description' => 'Inizia ad aggiungere capitoli alla tua biografia',
    ],

    // === CHAPTER TYPES ===
    'chapter_type' => [
        'standard' => 'Standard',
        'milestone' => 'Traguardo',
        'achievement' => 'Risultato',
        'experience' => 'Esperienza',
        'education' => 'Formazione',
        'career' => 'Carriera',
        'personal' => 'Personale',
        'project' => 'Progetto',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Carica Media',
        'featured_image' => 'Immagine in Evidenza',
        'gallery' => 'Galleria',
        'caption' => 'Didascalia',
        'alt_text' => 'Testo Alternativo',
        'upload_failed' => 'Errore durante il caricamento del file',
        'delete_media' => 'Elimina Media',
        'no_media' => 'Nessun media caricato',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'Tutte le Biografie',
        'my_biographies' => 'Le mie Biografie',
        'public_biographies' => 'Biografie Pubbliche',
        'create_biography' => 'Crea Biografia',
        'manage_biographies' => 'Gestisci Biografie',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Biografie Totali',
        'public_biographies' => 'Biografie Pubbliche',
        'total_chapters' => 'Capitoli Totali',
        'total_words' => 'Parole Totali',
        'reading_time' => 'Tempo di Lettura',
        'last_updated' => 'Ultimo Aggiornamento',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Condividi Biografia',
        'copy_link' => 'Copia Link',
        'social_share' => 'Condividi sui Social',
        'embed_code' => 'Codice Embed',
        'qr_code' => 'Codice QR',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Visibile a tutti su internet',
        'private_description' => 'Visibile solo a te',
        'unlisted_description' => 'Visibile solo con link diretto',
        'change_privacy' => 'Cambia Privacy',
        'privacy_updated' => 'Impostazioni privacy aggiornate',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Mostra Timeline',
        'hide_timeline' => 'Nascondi Timeline',
        'chronological' => 'Cronologico',
        'reverse_chronological' => 'Cronologico Inverso',
        'custom_order' => 'Ordine Personalizzato',
        'date_range' => 'Periodo',
        'ongoing' => 'In corso',
        'present' => 'Presente',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Esporta Biografia',
        'export_pdf' => 'Esporta PDF',
        'export_word' => 'Esporta Word',
        'export_html' => 'Esporta HTML',
        'export_success' => 'Biografia esportata con successo',
        'export_failed' => 'Errore durante l\'esportazione',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Cerca Biografie',
        'search_placeholder' => 'Cerca per titolo, contenuto o autore...',
        'no_results' => 'Nessun risultato trovato',
        'results_found' => 'risultati trovati',
        'filter_by' => 'Filtra per',
        'filter_type' => 'Tipo',
        'filter_date' => 'Data',
        'filter_author' => 'Autore',
        'clear_filters' => 'Pulisci Filtri',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Commenti',
        'add_comment' => 'Aggiungi Commento',
        'no_comments' => 'Nessun commento ancora',
        'comment_added' => 'Commento aggiunto con successo',
        'comment_deleted' => 'Commento eliminato con successo',
        'enable_comments' => 'Abilita Commenti',
        'disable_comments' => 'Disabilita Commenti',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'Nuova biografia pubblicata',
        'biography_updated' => 'Biografia aggiornata',
        'new_chapter' => 'Nuovo capitolo aggiunto',
        'chapter_updated' => 'Capitolo aggiornato',
        'new_comment' => 'Nuovo commento ricevuto',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Modifica Biografia',
        'create_new_biography' => 'Crea nuova Biografia',
        'tell_story_description' => 'Racconta la tua storia e condividila con il mondo',
        'validation_errors' => 'Errori di validazione',
        'basic_info' => 'Informazioni Base',
        'media_management' => 'Gestione Media',
        'settings' => 'Impostazioni',
        'title_required' => 'Titolo *',
        'title_placeholder' => 'Inserisci il titolo della tua biografia',
        'content_required' => 'Contenuto *',
        'content_placeholder' => 'Racconta la tua storia...',
        'excerpt' => 'Estratto',
        'excerpt_placeholder' => 'Breve descrizione della tua biografia...',
        'excerpt_help' => 'Descrizione breve che apparirà in anteprima',
        'add_chapter' => 'Aggiungi Capitolo',
        'edit_chapter' => 'Modifica',
        'delete_chapter' => 'Elimina',
        'biography_images' => 'Immagini Biografia',
        'upload_images_help' => 'Carica le immagini per la tua biografia. Formati supportati: JPG, PNG, WEBP (Max 2MB ciascuna)',
        'uploading_images' => 'Caricamento immagini in corso...',
        'uploaded_images' => 'Immagini Caricate',
        'biography_public' => 'Biografia Pubblica',
        'biography_public_help' => 'Rendi visibile la tua biografia a tutti gli utenti',
        'go_back' => 'Torna Indietro',
        'update_biography' => 'Aggiorna Biografia',
        'create_biography' => 'Crea Biografia',
    ],
];
