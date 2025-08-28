<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Interface ImageOptimizationManagerInterface
 *
 * @Oracode Interface: Image optimization management contract
 * 🎯 Purpose: Defines the contract for image optimization operations
 * 🧱 Core Logic: Multi-variant optimization (avatar, thumbnail, card, original)
 * 🔧 Enhancement: Configurable quality settings and format support
 *
 * Following NAMING.md: Manager suffix required, interface contract mandatory
 * Following ULTRA_STANDARDS.md: All exceptions through UEM, all logging through ULM
 *
 * @package App\Contracts
 * @author Assistant (following Oracode standards)
 * @version 1.0.0
 */
interface ImageOptimizationManagerInterface {
    /**
     * Optimize uploaded image into multiple variants
     *
     * @param UploadedFile $uploadedFile Original uploaded file
     * @param string $storageBasePath Base path for storage (e.g., users_files/collections_123/creator_456)
     * @param string $keyFile File key without extension
     * @param array $variants Variant configurations ['avatar' => ['size' => 80, 'quality' => 85], ...]
     * @param array $disks Storage disks to save to
     *
     * @return array Paths of created variants ['avatar' => 'path/to/avatar.webp', ...]
     *
     * @throws \Ultra\ErrorManager\Exceptions\UltraBaseException On optimization failure
     */
    public function optimizeImage(
        UploadedFile $uploadedFile,
        string $storageBasePath,
        string $keyFile,
        array $variants = [],
        array $disks = ['local']
    ): array;

    /**
     * Get default variant configurations
     *
     * @return array Default variant settings
     */
    public function getDefaultVariants(): array;

    /**
     * Check if file type is supported for optimization
     *
     * @param string $mimeType MIME type to check
     *
     * @return bool True if supported
     */
    public function isOptimizationSupported(string $mimeType): bool;

    /**
     * Get optimized file extension for given input
     *
     * @param string $originalMimeType Original file MIME type
     *
     * @return string Optimized extension (e.g., 'webp', 'jpg')
     */
    public function getOptimizedExtension(string $originalMimeType): string;
}
