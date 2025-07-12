<?php

/**
 * @Oracode Translations: Biography Success Messages (Italian)
 * 🎯 Purpose: Success messages for Biography and Chapter controllers
 * 🛡️ Privacy: User-friendly success confirmations
 * 🧱 Core Logic: Laravel localization for API responses
 *
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-02
 *
 * SAVE AS: resources/lang/it/biography.php
 */

return [

    // Biography Controller Success Messages
    'biographies_retrieved_successfully' => 'Biografie recuperate con successo',
    'biography_created_successfully' => 'Biografia creata con successo',
    'biography_retrieved_successfully' => 'Biografia recuperata con successo',
    'biography_updated_successfully' => 'Biografia aggiornata con successo',
    'biography_deleted_successfully' => 'Biografia ":title" eliminata con successo' .
        '{{ $chapters_count > 0 ? " (inclusi $chapters_count capitoli)" : "" }}',

    // Biography Chapter Controller Success Messages
    'chapters_retrieved_successfully' => 'Capitoli recuperati con successo',
    'chapter_retrieved_successfully' => 'Capitolo recuperato con successo',
    'chapter_created_successfully' => 'Capitolo creato con successo',
    'chapter_updated_successfully' => 'Capitolo aggiornato con successo',
    'chapter_deleted_successfully' => 'Capitolo ":title" eliminato con successo',
    'chapters_reordered_successfully' => 'Ordine capitoli aggiornato con successo',

    // Additional Biography Messages (for future use)
    'biography_published_successfully' => 'Biografia pubblicata con successo',
    'biography_unpublished_successfully' => 'Biografia resa privata con successo',
    'biography_completed_successfully' => 'Biografia marcata come completata',
    'biography_media_uploaded_successfully' => 'Immagine caricata con successo',
    'biography_media_deleted_successfully' => 'Immagine eliminata con successo',

    // Chapter Additional Messages (for future use)
    'chapter_published_successfully' => 'Capitolo pubblicato con successo',
    'chapter_unpublished_successfully' => 'Capitolo reso privato con successo',
    'chapter_media_uploaded_successfully' => 'Immagine capitolo caricata con successo',
    'chapter_media_deleted_successfully' => 'Immagine capitolo eliminata con successo',

    'categories' => [
        'profile' => 'Informazioni del Profilo',
        'account' => 'Dettagli Account',
        'preferences' => 'Preferenze e Impostazioni',
        'activity' => 'Cronologia Attività',
        'consents' => 'Cronologia Consensi',
        'collections' => 'Collezioni e Contenuti',
        'purchases' => 'Acquisti e Transazioni',
        'comments' => 'Commenti e Recensioni',
        'messages' => 'Messaggi e Comunicazioni',
        'biography' => 'Biografie e Capitoli',
    ],

    // Web View Translations
    'show' => [
        'title' => 'Biografia',
        'no_biography_title' => 'Nessuna biografia disponibile',
        'no_biography_description' => 'Questo utente non ha ancora pubblicato una biografia.',
    ],

    'view' => [
        'title' => 'La mia biografia',
    ],

    'manage' => [
        'title' => 'Gestisci biografie',
        'create_new' => 'Crea nuova biografia',
        'edit' => 'Modifica biografia',
        'delete' => 'Elimina biografia',
        'public' => 'Pubblica',
        'private' => 'Privata',
        'completed' => 'Completata',
        'draft' => 'Bozza',
    ],

    'listing' => [
        'title' => 'Biografie',
        'meta_description' => 'Scopri le biografie degli artisti e creatori su FlorenceEGI',
    ],

    // Common Biography Terms
    'min_read' => 'min di lettura',
    'chapters' => 'capitoli',
    'ongoing' => 'in corso',
    'media' => 'Media',
    'gallery' => 'Galleria',
    'video_not_supported' => 'Il tuo browser non supporta la riproduzione video.',
    'discover_more' => 'Scopri di più',
    'discover_more_description' => 'Esplora altre opere e collezioni di questo creatore.',
    'view_profile' => 'Vedi profilo',
    'share' => 'Condividi',
    'public' => 'Pubblica',
    'private' => 'Privata',

    // Chapter Types
    'chapter_type' => [
        'personal' => 'Personale',
        'professional' => 'Professionale',
        'academic' => 'Accademico',
        'artistic' => 'Artistico',
        'other' => 'Altro',
    ],
];
