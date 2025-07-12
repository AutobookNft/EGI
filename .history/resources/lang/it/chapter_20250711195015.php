<?php

/**
 * @Oracode Translation: Chapter System Italian Translations
 * 🎯 Purpose: Complete Italian translations for biography chapters
 * 📝 Content: Validation messages and field labels for chapters
 * 🧭 Organization: Organized by validation and field categories
 *
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Chapter System)
 * @date 2025-01-07
 */

return [
    // === VALIDATION MESSAGES ===
    'validation' => [
        'biography_id_required' => 'La biografia è obbligatoria',
        'biography_not_found' => 'La biografia specificata non esiste',
        'title_required' => 'Il titolo del capitolo è obbligatorio',
        'title_max' => 'Il titolo del capitolo non può superare i 255 caratteri',
        'content_required' => 'Il contenuto del capitolo è obbligatorio',
        'date_from_invalid' => 'La data di inizio deve essere una data valida',
        'date_to_invalid' => 'La data di fine deve essere una data valida',
        'date_to_after_from' => 'La data di fine deve essere successiva o uguale alla data di inizio',
        'sort_order_integer' => 'L\'ordine deve essere un numero intero',
        'sort_order_min' => 'L\'ordine non può essere negativo',
        'media_file' => 'Il file media deve essere un file valido',
        'media_mimes' => 'Il file media deve essere in formato JPEG, PNG, WebP, GIF, MP4, MOV o AVI',
        'media_max_size' => 'Il file media non può superare i 10MB',
    ],

    // === FIELD LABELS ===
    'fields' => [
        'biography' => 'Biografia',
        'title' => 'Titolo del Capitolo',
        'content' => 'Contenuto del Capitolo',
        'date_from' => 'Data di Inizio',
        'date_to' => 'Data di Fine',
        'is_ongoing' => 'In Corso',
        'sort_order' => 'Ordine',
        'is_published' => 'Pubblicato',
        'chapter_type' => 'Tipo di Capitolo',
        'slug' => 'Slug',
        'media' => 'Media',
        'formatting_data' => 'Dati di Formattazione',
    ],

    // === GENERAL TERMS ===
    'chapter' => 'Capitolo',
    'chapters' => 'Capitoli',
    'add_chapter' => 'Aggiungi Capitolo',
    'edit_chapter' => 'Modifica Capitolo',
    'delete_chapter' => 'Elimina Capitolo',
    'save_chapter' => 'Salva Capitolo',
    'publish_chapter' => 'Pubblica Capitolo',
    'unpublish_chapter' => 'Rimuovi Pubblicazione',
    'reorder_chapters' => 'Riordina Capitoli',

    // === CHAPTER TYPES ===
    'types' => [
        'introduction' => 'Introduzione',
        'childhood' => 'Infanzia',
        'education' => 'Educazione',
        'career' => 'Carriera',
        'personal' => 'Personale',
        'professional' => 'Professionale',
        'achievement' => 'Risultato',
        'milestone' => 'Traguardo',
        'reflection' => 'Riflessione',
        'conclusion' => 'Conclusione',
        'other' => 'Altro',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Capitolo creato con successo',
        'updated' => 'Capitolo aggiornato con successo',
        'deleted' => 'Capitolo eliminato con successo',
        'published' => 'Capitolo pubblicato con successo',
        'unpublished' => 'Capitolo reso privato con successo',
        'reordered' => 'Ordine dei capitoli aggiornato con successo',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Capitolo non trovato',
        'unauthorized' => 'Non autorizzato ad accedere a questo capitolo',
        'create_failed' => 'Errore durante la creazione del capitolo',
        'update_failed' => 'Errore durante l\'aggiornamento del capitolo',
        'delete_failed' => 'Errore durante l\'eliminazione del capitolo',
        'publish_failed' => 'Errore durante la pubblicazione del capitolo',
        'reorder_failed' => 'Errore durante il riordinamento dei capitoli',
        'media_upload_failed' => 'Errore durante il caricamento del media',
        'generic' => 'Si è verificato un errore. Riprova.',
    ],

    // === FORM HELPERS ===
    'form' => [
        'title_placeholder' => 'Inserisci il titolo del capitolo',
        'content_placeholder' => 'Racconta questa parte della tua storia...',
        'date_from_help' => 'Quando è iniziato questo periodo della tua vita?',
        'date_to_help' => 'Quando è finito questo periodo? (Lascia vuoto se ancora in corso)',
        'is_ongoing_help' => 'Spunta se questo capitolo riguarda un periodo ancora in corso',
        'sort_order_help' => 'Ordine di visualizzazione del capitolo (numeri più bassi appaiono prima)',
        'is_published_help' => 'Rendi questo capitolo visibile nel profilo pubblico',
        'chapter_type_help' => 'Seleziona il tipo di capitolo per una migliore organizzazione',
        'media_help' => 'Carica immagini o video per arricchire il capitolo',
    ],

    // === TIMELINE ===
    'timeline' => [
        'ongoing' => 'In corso',
        'present' => 'Presente',
        'date_range' => 'Periodo',
        'duration' => 'Durata',
        'years' => 'anni',
        'months' => 'mesi',
        'days' => 'giorni',
        'from' => 'Da',
        'to' => 'A',
        'since' => 'Da',
    ],

    // === CONFIRMATIONS ===
    'confirm' => [
        'delete' => 'Sei sicuro di voler eliminare questo capitolo? Questa azione non può essere annullata.',
        'unpublish' => 'Sei sicuro di voler rendere privato questo capitolo?',
        'reorder' => 'Conferma il nuovo ordine dei capitoli?',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Carica Media',
        'caption' => 'Didascalia',
        'alt_text' => 'Testo Alternativo',
        'remove' => 'Rimuovi Media',
        'supported_formats' => 'Formati supportati: JPEG, PNG, WebP, GIF, MP4, MOV, AVI',
        'max_file_size' => 'Dimensione massima: 10MB',
        'no_media' => 'Nessun media caricato per questo capitolo',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'previous_chapter' => 'Capitolo Precedente',
        'next_chapter' => 'Capitolo Successivo',
        'back_to_biography' => 'Torna alla Biografia',
        'chapter_list' => 'Elenco Capitoli',
    ],

    // === STATS ===
    'stats' => [
        'word_count' => 'Parole',
        'reading_time' => 'Tempo di lettura',
        'characters' => 'Caratteri',
        'paragraphs' => 'Paragrafi',
        'media_count' => 'Media allegati',
    ],
];
