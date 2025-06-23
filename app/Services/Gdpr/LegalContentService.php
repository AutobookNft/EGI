<?php

namespace App\Services\Gdpr;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Services\Gdpr
 * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
 * @deadline 2025-06-30
 *
 * @Oracode Service: Legal Content Provider
 * 🎯 Purpose: Centralized service to read versioned legal documents from the filesystem.
 * 🧱 Core Logic: Handles file path resolution, content parsing, and caching.
 * 📡 API: Provides a stable interface for other services (like ConsentService) to get legal content.
 */
class LegalContentService
{
    protected string $basePath;

    public function __construct(protected ?UltraLogManager $logger = null)
    {
        $this->basePath = resource_path('legal/terms/versions');
    }

    /**
     * Carica il contenuto dei termini ATTUALI per un dato tipo di utente e locale.
     * Utilizza la cache per ottimizzare le letture da file.
     *
     * @param string $userType Il tipo di utente (es. 'creator', 'collector').
     * @param string $locale La lingua (es. 'it').
     * @return array|null Il contenuto dei termini come array PHP, o null se non trovato.
     */
    public function getCurrentTermsContent(string $userType, string $locale): ?array
    {
        $cacheKey = "legal_terms_current_{$userType}_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($userType, $locale) {
            $filePath = "{$this->basePath}/current/{$locale}/{$userType}.php";

            return $this->loadContentFromFile($filePath);
        });
    }

    /**
     * Ottiene la stringa della versione ATTUALE dal file di metadati.
     *
     * @return string La versione corrente (es. '1.0.0').
     */
    public function getCurrentVersionString(): string
    {
        $cacheKey = 'legal_terms_current_version_string';

        return Cache::remember($cacheKey, 3600, function () {
            $metadata = $this->getMetadataForVersion('current');
            return $metadata['version'] ?? '0.0.0';
        });
    }

    /**
     * Carica il contenuto dei termini per una SPECIFICA versione.
     *
     * @param string $version La versione da caricare (es. '1.0.0').
     * @param string $userType Il tipo di utente.
     * @param string $locale La lingua.
     * @return array|null Il contenuto dei termini.
     */
    public function getTermsContentForVersion(string $version, string $userType, string $locale): ?array
    {
        $filePath = "{$this->basePath}/{$version}/{$locale}/{$userType}.php";
        return $this->loadContentFromFile($filePath);
    }

    /**
     * Carica l'array completo di metadati per una data versione.
     *
     * @param string $version La versione (es. '1.0.0' o 'current').
     * @return array|null I metadati.
     */
    public function getMetadataForVersion(string $version): ?array
    {
        $filePath = "{$this->basePath}/{$version}/metadata.php";
        return $this->loadContentFromFile($filePath);
    }

    /**
     * Invalida la cache per i contenuti legali.
     * Utile quando viene pubblicata una nuova versione.
     *
     * @param string|null $userType
     * @param string|null $locale
     * @return void
     */
    public function clearCache(?string $userType = null, ?string $locale = null): void
    {
        if ($userType && $locale) {
            Cache::forget("legal_terms_current_{$userType}_{$locale}");
        } else {
            // Un modo più selettivo per pulire la cache legata ai termini
            Cache::tags(['legal_content'])->flush();
        }
        Cache::forget('legal_terms_current_version_string');
    }

    /**
     * Helper privato per caricare un file PHP e gestire gli errori.
     *
     * @param string $filePath Il percorso completo del file.
     * @return array|null
     */
    private function loadContentFromFile(string $filePath): ?array
    {
        if (!File::exists($filePath)) {
            $this->logger?->warning('LegalContentService: File not found', [
                'file_path' => $filePath,
                'log_category' => 'LEGAL_CONTENT_SERVICE_WARNING'
            ]);
            return null;
        }

        try {
            return include $filePath;
        } catch (\Throwable $e) {
            $this->logger?->error('LegalContentService: Failed to load or parse content file', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'log_category' => 'LEGAL_CONTENT_SERVICE_ERROR'
            ]);
            return null;
        }
    }
}
