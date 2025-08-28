<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Class ImageVariantHelper
 *
 * @Oracode Helper: Static helper methods for calculating image variant paths
 * 🎯 Purpose: Provides convention-based path calculation without database dependencies
 * 🧱 Core Logic: Uses naming conventions from image-optimization config
 * 🔧 Enhancement: Static methods for easy use in Blade templates and frontend
 *
 * Following NAMING.md: Helper suffix for utility classes
 * Following PILLAR0.md: No database dependencies, pure calculation
 *
 * @package App\Services
 * @author Assistant (following Oracode standards)
 * @version 1.0.0
 */
class ImageVariantHelper {
    /**
     * Calculate variant file path using naming convention
     *
     * @param string $storageBasePath Base storage path (e.g., users_files/collections_123/creator_456)
     * @param string $keyFile File key without extension
     * @param string $variantName Variant name (avatar, thumbnail, card, original)
     * @param string $extension File extension (default: webp)
     *
     * @return string Full path to variant file
     *
     * @oracode-static-helper Convention-based, no DI needed
     */
    public static function getVariantPath(
        string $storageBasePath,
        string $keyFile,
        string $variantName,
        string $extension = 'webp'
    ): string {
        if ($variantName === 'original') {
            return "{$storageBasePath}/{$keyFile}.{$extension}";
        }

        return "{$storageBasePath}/{$keyFile}_{$variantName}.{$extension}";
    }

    /**
     * Get all possible variant paths for a file
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param array $variantNames Variant names (if empty, uses config defaults)
     * @param string $extension File extension (default: webp)
     *
     * @return array Associative array of variant paths ['avatar' => 'path/to/avatar.webp', ...]
     *
     * @oracode-config-driven Uses configuration for default variants
     */
    public static function getAllVariantPaths(
        string $storageBasePath,
        string $keyFile,
        array $variantNames = [],
        string $extension = 'webp'
    ): array {
        if (empty($variantNames)) {
            $variantNames = array_keys(config('image-optimization.variants.egi', [
                'thumbnail',
                'mobile',
                'tablet',
                'desktop'
            ]));
        }

        $paths = [];

        foreach ($variantNames as $variantName) {
            $paths[$variantName] = self::getVariantPath(
                $storageBasePath,
                $keyFile,
                $variantName,
                $extension
            );
        }

        return $paths;
    }

    /**
     * Check if optimized variants exist on storage
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param string $disk Storage disk to check (default: local)
     * @param array $variantNames Variant names to check
     *
     * @return array Array of existing variant paths
     *
     * @oracode-storage-check Dynamic existence verification
     */
    public static function getExistingVariants(
        string $storageBasePath,
        string $keyFile,
        string $disk = 'local',
        array $variantNames = []
    ): array {
        $allPaths = self::getAllVariantPaths($storageBasePath, $keyFile, $variantNames);
        $existingPaths = [];

        foreach ($allPaths as $variantName => $path) {
            if (Storage::disk($disk)->exists($path)) {
                $existingPaths[$variantName] = $path;
            }
        }

        return $existingPaths;
    }

    /**
     * Get variant URL for frontend usage
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param string $variantName Variant name
     * @param string $disk Storage disk (default: local)
     * @param string $extension File extension (default: webp)
     *
     * @return string|null URL to variant file or null if not exists
     *
     * @oracode-frontend-helper Direct URL generation for Blade templates
     */
    public static function getVariantUrl(
        string $storageBasePath,
        string $keyFile,
        string $variantName,
        string $disk = 'local',
        string $extension = 'webp'
    ): ?string {
        $path = self::getVariantPath($storageBasePath, $keyFile, $variantName, $extension);

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }

    /**
     * Get fallback to original if variant doesn't exist
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param string $variantName Preferred variant name
     * @param string $disk Storage disk (default: local)
     *
     * @return string|null URL to variant or original file
     *
     * @oracode-fallback-pattern Graceful degradation to original
     */
    public static function getVariantUrlWithFallback(
        string $storageBasePath,
        string $keyFile,
        string $variantName,
        string $disk = 'local'
    ): ?string {
        // Try to get the requested variant
        $variantUrl = self::getVariantUrl($storageBasePath, $keyFile, $variantName, $disk);

        if ($variantUrl) {
            return $variantUrl;
        }

        // Fallback to original
        return self::getVariantUrl($storageBasePath, $keyFile, 'original', $disk);
    }
}
