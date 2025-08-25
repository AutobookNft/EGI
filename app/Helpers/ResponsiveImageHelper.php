<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ResponsiveImageHelper
 *
 * Helper per gestire immagini responsive ottimizzate dall'Ultra Upload Manager
 * con fallback intelligente alle immagini originali
 */
class ResponsiveImageHelper {
    /**
     * Genera tag <picture> completo con varianti responsive
     *
     * @param string $originalUrl URL dell'immagine originale
     * @param array $options Opzioni aggiuntive (alt, class, loading, etc.)
     * @return string HTML del tag <picture>
     */
    public static function picture(string $originalUrl, array $options = []): string {
        $alt = $options['alt'] ?? '';
        $class = $options['class'] ?? '';
        $loading = $options['loading'] ?? 'lazy';
        $fetchpriority = $options['fetchpriority'] ?? null;
        $type = $options['type'] ?? 'egi'; // egi, banner, card, avatar

        // Prepara varianti per il tipo specificato
        $variants = self::getVariantsForType($type);
        $sources = [];

        foreach ($variants as $variant => $config) {
            $optimizedUrl = self::getOptimizedUrl($originalUrl, $variant, $type);
            $mediaQuery = self::getMediaQuery($variant, $config);

            if ($optimizedUrl !== $originalUrl) {
                $sources[] = sprintf(
                    '<source media="%s" srcset="%s" type="image/webp">',
                    $mediaQuery,
                    htmlspecialchars($optimizedUrl)
                );
            }
        }

        // Tag img di fallback con attributi
        $imgAttributes = [
            'src' => htmlspecialchars($originalUrl),
            'alt' => htmlspecialchars($alt),
            'loading' => $loading
        ];

        if ($class) {
            $imgAttributes['class'] = htmlspecialchars($class);
        }

        if ($fetchpriority) {
            $imgAttributes['fetchpriority'] = $fetchpriority;
        }

        $imgAttribStr = implode(' ', array_map(
            fn($key, $value) => sprintf('%s="%s"', $key, $value),
            array_keys($imgAttributes),
            $imgAttributes
        ));

        $sourcesHtml = implode("\n    ", $sources);

        return sprintf(
            "<picture>\n    %s\n    <img %s>\n</picture>",
            $sourcesHtml,
            $imgAttribStr
        );
    }

    /**
     * Ottiene URL di variante ottimizzata o fallback all'originale
     *
     * @param string $originalUrl URL originale
     * @param string $variant Nome variante (mobile, tablet, desktop, etc.)
     * @param string $type Tipo di immagine (egi, banner, card, avatar)
     * @return string URL ottimizzata o originale come fallback
     */
    public static function getOptimizedUrl(string $originalUrl, string $variant, string $type = 'egi'): string {
        // Estrae il path relativo dall'URL
        $relativePath = self::extractRelativePath($originalUrl);

        if (!$relativePath) {
            return $originalUrl; // Fallback se non riusciamo a estrarre il path
        }

        // Costruisce il path ottimizzato
        $optimizedPath = self::buildOptimizedPath($relativePath, $variant, $type);

        // Verifica se il file ottimizzato esiste
        if (Storage::disk('public')->exists($optimizedPath)) {
            return Storage::disk('public')->url($optimizedPath);
        }

        // TODO: Verifica anche su altri disk se configurati
        // if (Storage::disk('do')->exists($optimizedPath)) {
        //     return Storage::disk('do')->url($optimizedPath);
        // }

        // Fallback all'originale
        return $originalUrl;
    }

    /**
     * Verifica se esistono varianti ottimizzate per un'immagine
     *
     * @param string $originalUrl URL originale
     * @param string $type Tipo di immagine
     * @return bool True se esistono varianti ottimizzate
     */
    public static function hasOptimizedVariants(string $originalUrl, string $type = 'egi'): bool {
        $relativePath = self::extractRelativePath($originalUrl);

        if (!$relativePath) {
            return false;
        }

        $variants = self::getVariantsForType($type);

        foreach ($variants as $variant => $config) {
            $optimizedPath = self::buildOptimizedPath($relativePath, $variant, $type);

            if (Storage::disk('public')->exists($optimizedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Estrae il path relativo da un URL
     */
    private static function extractRelativePath(string $url): ?string {
        // Rimuove il dominio se presente
        $path = parse_url($url, PHP_URL_PATH);

        if (!$path) {
            return null;
        }

        // Rimuove /storage/ prefix se presente
        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, 9); // Rimuove '/storage/'
        }

        return $path;
    }

    /**
     * Costruisce il path per la variante ottimizzata
     */
    private static function buildOptimizedPath(string $originalPath, string $variant, string $type): string {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = 'webp'; // Le varianti sono sempre in WebP

        // Ottiene dimensioni per il filename
        $variants = self::getVariantsForType($type);
        $config = $variants[$variant] ?? null;

        if ($config) {
            $dimensions = sprintf('%dx%d', $config['width'], $config['height']);
            $optimizedFilename = sprintf('%s_%s.%s', $filename, $dimensions, $extension);
        } else {
            $optimizedFilename = sprintf('%s_%s.%s', $filename, $variant, $extension);
        }

        return sprintf('%s/optimized/%s/%s', $directory, $variant, $optimizedFilename);
    }

    /**
     * Ottiene le varianti per il tipo specificato
     */
    private static function getVariantsForType(string $type): array {
        $variants = [
            'egi' => [
                'desktop' => ['width' => 800, 'height' => 800, 'media' => '(min-width: 1024px)'],
                'tablet' => ['width' => 600, 'height' => 600, 'media' => '(min-width: 768px)'],
                'mobile' => ['width' => 400, 'height' => 400, 'media' => '(max-width: 767px)'],
            ],
            'banner' => [
                'desktop' => ['width' => 1920, 'height' => 960, 'media' => '(min-width: 1024px)'],
                'tablet' => ['width' => 1200, 'height' => 600, 'media' => '(min-width: 768px)'],
                'mobile' => ['width' => 800, 'height' => 400, 'media' => '(max-width: 767px)'],
            ],
            'card' => [
                'default' => ['width' => 300, 'height' => 300, 'media' => '(min-width: 0px)'],
            ],
            'avatar' => [
                'default' => ['width' => 200, 'height' => 200, 'media' => '(min-width: 0px)'],
            ],
        ];

        return $variants[$type] ?? $variants['egi'];
    }

    /**
     * Genera media query per la variante
     */
    private static function getMediaQuery(string $variant, array $config): string {
        return $config['media'] ?? '(min-width: 0px)';
    }
}
