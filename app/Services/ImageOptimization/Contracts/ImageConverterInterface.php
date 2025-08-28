<?php

namespace App\Services\ImageOptimization\Contracts;

/**
 * @Oracode Interface: ImageConverterInterface
 * 🎯 Purpose: Define contract for image format converters
 * 📥 Input: Source path, output path, conversion config
 * 📤 Output: Conversion result with file info
 *
 * @package App\Services\ImageOptimization\Contracts
 * @version 1.0.0
 */
interface ImageConverterInterface {
    /**
     * Convert image to specific format with given configuration
     *
     * @param string $sourcePath Path to source image
     * @param string $outputPath Desired output path
     * @param array $config Conversion configuration (width, height, quality, etc.)
     * @return array Result with success status, file path, size, etc.
     */
    public function convert(string $sourcePath, string $outputPath, array $config): array;

    /**
     * Check if the converter can handle the source format
     *
     * @param string $mimeType Source file MIME type
     * @return bool
     */
    public function canHandle(string $mimeType): bool;

    /**
     * Get supported input formats
     *
     * @return array Array of supported MIME types
     */
    public function getSupportedFormats(): array;
}
