<?php
/**
 * @Oracode Translations: Biography Success Messages (English)
 * 🎯 Purpose: English success messages for Biography and Chapter controllers
 * 🛡️ Privacy: User-friendly success confirmations
 * 🧱 Core Logic: Laravel localization for API responses
 *
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-02
 *
 * SAVE AS: resources/lang/en/biography.php
 */

// English version:
return [

    // Biography Controller Success Messages
    'biographies_retrieved_successfully' => 'Biographies retrieved successfully',
    'biography_created_successfully' => 'Biography created successfully',
    'biography_retrieved_successfully' => 'Biography retrieved successfully',
    'biography_updated_successfully' => 'Biography updated successfully',
    'biography_deleted_successfully' => 'Biography ":title" deleted successfully' .
        '{{ $chapters_count > 0 ? " (including $chapters_count chapters)" : "" }}',

    // Biography Chapter Controller Success Messages
    'chapters_retrieved_successfully' => 'Chapters retrieved successfully',
    'chapter_retrieved_successfully' => 'Chapter retrieved successfully',
    'chapter_created_successfully' => 'Chapter created successfully',
    'chapter_updated_successfully' => 'Chapter updated successfully',
    'chapter_deleted_successfully' => 'Chapter ":title" deleted successfully',
    'chapters_reordered_successfully' => 'Chapters order updated successfully',

    // Additional Biography Messages (for future use)
    'biography_published_successfully' => 'Biography published successfully',
    'biography_unpublished_successfully' => 'Biography made private successfully',
    'biography_completed_successfully' => 'Biography marked as completed',
    'biography_media_uploaded_successfully' => 'Image uploaded successfully',
    'biography_media_deleted_successfully' => 'Image deleted successfully',

    // Chapter Additional Messages (for future use)
    'chapter_published_successfully' => 'Chapter published successfully',
    'chapter_unpublished_successfully' => 'Chapter made private successfully',
    'chapter_media_uploaded_successfully' => 'Chapter image uploaded successfully',
    'chapter_media_deleted_successfully' => 'Chapter image deleted successfully',

    'categories' => [
        'profile' => 'Profile Information',
        'account' => 'Account Details',
        'preferences' => 'Preferences and Settings',
        'activity' => 'Activity History',
        'consents' => 'Consent History',
        'collections' => 'Collections and Content',
        'purchases' => 'Purchases and Transactions',
        'comments' => 'Comments and Reviews',
        'messages' => 'Messages and Communications',
        'biography' => 'Biographies and Chapters',
    ],
    'manage.chapters' => 'Chapters',

];
