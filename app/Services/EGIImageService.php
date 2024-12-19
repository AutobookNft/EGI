<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EGIImageService
{
    /**
     * Rimuove i file esistenti con un determinato prefisso dai servizi di hosting attivi.
     */
    public static function removeOldImage(string $prefix, int $collectionId, string $pathKey): bool
    {
        try {
            $services = config('paths.hosting');
            $relativePathTemplate = config("paths.paths.{$pathKey}");

            if (!$relativePathTemplate) {
                Log::channel('florenceegi')->error("Percorso non valido per la chiave: {$pathKey}");
                return false;
            }

            $relativePath = str_replace('{collectionId}', $collectionId, $relativePathTemplate);
            $allSuccess = true;

            foreach ($services as $serviceName => $serviceConfig) {
                if (!$serviceConfig['is_active']) {
                    Log::channel('florenceegi')->info("Servizio {$serviceName} disattivato. Eliminazione saltata.");
                    continue;
                }

                $disk = $serviceConfig['disk'];

                $files = Storage::disk($disk)->files($relativePath);

                foreach ($files as $file) {
                    if (str_starts_with(basename($file), $prefix)) {
                        Log::channel('florenceegi')->info('Rimozione vecchia immagine', [
                            'file' => $file,
                            'disk' => $disk,
                            'service' => $serviceName,
                        ]);

                        if (!Storage::disk($disk)->delete($file)) {
                            Log::channel('florenceegi')->error("Errore durante l'eliminazione del file: {$file} su {$serviceName}");
                            $allSuccess = false;
                        }
                    }
                }
            }

            return $allSuccess;
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Errore durante la rimozione del vecchio file', [
                'error' => $e->getMessage(),
                'prefix' => $prefix,
                'collectionId' => $collectionId,
                'pathKey' => $pathKey,
            ]);
            return false;
        }
    }

    /**
     * Salva un'immagine nei servizi di hosting attivi.
     */
    public static function saveEGIImage(int $collectionId, string $filename, $file, string $pathKey): bool
    {
        $services = config('paths.hosting');
        $relativePathTemplate = config("paths.paths.{$pathKey}");

        if (!$relativePathTemplate) {
            Log::error("Percorso non valido per la chiave: {$pathKey}");
            return false;
        }

        $relativePath = str_replace('{collectionId}', $collectionId, $relativePathTemplate);
        $fullPath = $relativePath . $filename;

        $atLeastOneSuccess = false;

        foreach ($services as $serviceName => $serviceConfig) {
            if (!$serviceConfig['is_active']) {
                Log::info("Servizio {$serviceName} disattivato. Salvataggio saltato.");
                continue;
            }

            $disk = $serviceConfig['disk'];

            try {
                Storage::disk($disk)->put($fullPath, $file->get());
                Log::info("File salvato su {$serviceName}: {$fullPath}");
                $atLeastOneSuccess = true;
            } catch (\Exception $e) {
                Log::error("Errore nel salvataggio su {$serviceName}: " . $e->getMessage());
            }
        }

        return $atLeastOneSuccess;
    }

    /**
     * Ottiene il percorso dell'immagine memorizzata nella cache.
     */
    public static function getCachedEGIImagePath(int $collectionId, string $filename, bool $isPublished, ?string $hostingService = null, string $pathKey = 'head.banner'): ?string
    {
        $cacheKey = "egi_image_path_{$collectionId}_{$filename}";
        if ($hostingService) {
            $cacheKey .= "_{$hostingService}";
        }
        $cacheDuration = $isPublished ? now()->addDays(7) : now()->addMinutes(30);

        Log::channel('florenceegi')->info('getCachedEGIImagePath chiamato', [
            'collectionId' => $collectionId,
            'filename' => $filename,
            'hostingService' => $hostingService,
            'cacheKey' => $cacheKey,
            'pathKey' => $pathKey,
        ]);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($collectionId, $filename, $hostingService, $pathKey) {
            $imagePath = self::getEGIImagePath($collectionId, $filename, $hostingService, $pathKey);

            Log::channel('florenceegi')->info('Percorso immagine calcolato', [
                'imagePath' => $imagePath,
            ]);

            return $imagePath;
        });
    }

    /**
     * Ottiene il percorso dell'immagine dall'hosting attivo o dai fallback.
     */
    protected static function getEGIImagePath(int $collectionId, string $filename, ?string $hostingService = null, string $pathKey = 'head.banner'): ?string
    {
        $hostingService = $hostingService ?? self::getDefaultHostingService();
        $servicesToTry = array_merge([$hostingService], Config::get("paths.hosting.{$hostingService}.fallback", []));
        $services = Config::get('paths.hosting');

        Log::channel('florenceegi')->info('Inizio getEGIImagePath', [
            'collectionId' => $collectionId,
            'filename' => $filename,
            'hostingService' => $hostingService,
            'servicesToTry' => $servicesToTry,
            'pathKey' => $pathKey,
        ]);

        foreach ($servicesToTry as $service) {
            if (empty($services[$service]['is_active']) || !$services[$service]['is_active']) {
                Log::channel('florenceegi')->info("Servizio {$service} disattivato. Verifica saltata.");
                continue;
            }

            $baseUrl = $services[$service]['url'];
            $relativePathTemplate = Config::get("paths.paths.{$pathKey}");

            Log::channel('florenceegi')->info('Template del percorso relativo', [
                'relativePathTemplate' => $relativePathTemplate,
            ]);

            $relativePath = str_replace('{collectionId}', $collectionId, $relativePathTemplate);
            $fullPath = rtrim($baseUrl, '/') . '/' . $relativePath . $filename;

            Log::channel('florenceegi')->info('Tentativo di verifica immagine', [
                'service' => $service,
                'disk' => 'public',
                'relativePath' => $relativePath,
                'fullPath' => $fullPath,
            ]);

            if ($service === 'Local') {
                if (Storage::disk('public')->exists($relativePath . $filename)) {
                    Log::channel('florenceegi')->info('File trovato su disco locale', [
                        'path' => $relativePath . $filename,
                        'url' => asset('storage/' . $relativePath . $filename),
                    ]);
                    return asset('storage/' . $relativePath . $filename);
                } else {
                    Log::channel('florenceegi')->error('File non trovato su disco locale', [
                        'path' => $relativePath . $filename,
                    ]);
                }
            } else {
                if (self::checkImageExists($fullPath)) {
                    return $fullPath;
                } else {
                    Log::channel('florenceegi')->error('File non trovato su servizio remoto', [
                        'service' => $service,
                        'url' => $fullPath,
                    ]);
                }
            }
        }

        Log::channel('florenceegi')->error("Immagine non disponibile su nessun servizio di hosting per il file: {$filename}");
        return null;
    }

    /**
     * Verifica se un'immagine esiste all'URL specificato.
     */
    protected static function checkImageExists(string $url): bool
    {
        try {
            $response = Http::head($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Errore durante il controllo dell'immagine: {$url}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Invalida la cache dell'immagine.
     */
    public static function invalidateEGIImageCache(int $collectionId, string $filename, ?string $hostingService = null): void
    {
        $cacheKey = "egi_image_path_{$collectionId}_{$filename}_{$hostingService}";
        Cache::forget($cacheKey);
    }

    /**
     * Ottiene il servizio di hosting predefinito.
     */
    protected static function getDefaultHostingService(): string
    {
        $services = Config::get('paths.hosting');

        // Cerca il primo servizio attivo con is_default = true
        foreach ($services as $serviceName => $serviceConfig) {
            if (!empty($serviceConfig['is_default']) && !empty($serviceConfig['is_active'])) {
                return $serviceName;
            }
        }

        // Se nessun servizio è marcato come default, restituisci il primo servizio attivo
        foreach ($services as $serviceName => $serviceConfig) {
            if (!empty($serviceConfig['is_active'])) {
                return $serviceName;
            }
        }

        // Se nessun servizio è attivo, ritorna 'Local' come fallback
        return 'Local';
    }

}
