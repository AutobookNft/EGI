<?php

namespace App\Services\ImageOptimization;

use App\Models\Egi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\ImageOptimization\Contracts\ImageConverterInterface;
use App\Services\ImageOptimization\Converters\WebPConverter;
use App\Services\ImageOptimization\Converters\JpegConverter;
use Exception;

/**
 * @Oracode Service: ImageOptimizationService
 * 🎯 Purpose: Orchestrates image optimization process for EGI uploads
 * 📥 Input: EGI model with file information
 * 📤 Output: Multiple optimized variants (avatar, thumbnail, card, original)
 * 🧱 Core Logic: Uses converter strategy pattern for different formats
 *
 * @package App\Services\ImageOptimization
 * @version 1.0.0
 * @author Padmin D. Curtis for Fabio Cherici
 * @since 2025-08-28
 */
class ImageOptimizationService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected array $converters = [];
    protected string $logChannel = 'image_optimization';

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->initializeConverters();
    }

    /**
     * Process EGI image optimization
     *
     * @param Egi $egi
     * @return array Results of optimization process
     * @throws Exception
     */
    public function processEgiOptimization(Egi $egi): array {
        $logContext = [
            'egi_id' => $egi->id,
            'collection_id' => $egi->collection_id,
            'user_id' => $egi->user_id,
            'file_extension' => $egi->extension
        ];

        $this->logger->info('[ImageOptimization] Starting EGI image optimization', $logContext);

        try {
            // Validate EGI has required data
            $this->validateEgiData($egi);

            // Get original file path using existing UUM pattern
            $originalPath = $this->getOriginalFilePath($egi);

            // Verify original file exists
            if (!$this->verifyOriginalFile($originalPath)) {
                $this->errorManager->handle('IMAGE_OPTIMIZATION_INVALID_FILE', [
                    'file_path' => $originalPath
                ]);
                throw new Exception("Original file not found: {$originalPath}");
            }

            // Get conversion configurations from Egi model
            $conversions = $this->getEgiConversions($egi);

            // Process each conversion
            $results = [];
            foreach ($conversions as $variant => $config) {
                try {
                    $result = $this->processConversion($egi, $originalPath, $variant, $config);
                    $results[$variant] = $result;

                    $this->logger->info(
                        "[ImageOptimization] Variant '{$variant}' processed successfully",
                        array_merge($logContext, ['variant' => $variant, 'files_created' => count($result)])
                    );
                } catch (Exception $e) {
                    $this->errorManager->handle('IMAGE_OPTIMIZATION_VARIANT_CREATION_FAILED', [
                        'variant_type' => $variant,
                        'dimensions' => 'processing',
                        'error' => $e->getMessage()
                    ]);

                    // Log for debugging
                    $this->logger->error(
                        "[ImageOptimization] Failed to process variant '{$variant}'",
                        array_merge($logContext, ['variant' => $variant, 'error' => $e->getMessage()])
                    );

                    // Continue with other variants instead of failing completely
                    $results[$variant] = ['error' => $e->getMessage()];
                }
            }

            $this->logger->info(
                '[ImageOptimization] EGI optimization completed',
                array_merge($logContext, ['processed_variants' => array_keys($results)])
            );

            return $results;
        } catch (Exception $e) {
            $this->errorManager->handle('IMAGE_OPTIMIZATION_PROCESSING_FAILED', [
                'file_path' => $egi->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            // Still log for debugging purposes
            $this->logger->error(
                '[ImageOptimization] EGI optimization failed',
                array_merge($logContext, ['error' => $e->getMessage()])
            );
            throw $e;
        }
    }

    /**
     * Get original file path using UUM pattern
     */
    protected function getOriginalFilePath(Egi $egi): string {
        return sprintf(
            'users_files/collections_%d/creator_%d/%d.%s',
            $egi->collection_id,
            $egi->user_id,
            $egi->key_file,
            $egi->extension
        );
    }

    /**
     * Verify original file exists on storage
     */
    protected function verifyOriginalFile(string $path): bool {
        $storageDisks = Config::get('egi.storage.disks', ['public']);

        foreach ($storageDisks as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate EGI has required data for optimization
     */
    protected function validateEgiData(Egi $egi): void {
        $required = ['id', 'collection_id', 'user_id', 'key_file', 'extension'];

        foreach ($required as $field) {
            if (empty($egi->$field)) {
                $this->errorManager->handle('IMAGE_OPTIMIZATION_INVALID_FILE', [
                    'file_path' => "EGI field: {$field}"
                ]);
                throw new Exception("EGI missing required field: {$field}");
            }
        }
    }

    /**
     * Get image conversions configuration for EGI
     * This method will be enhanced when we add the trait to Egi model
     */
    protected function getEgiConversions(Egi $egi): array {
        // Default configuration matching user requirements
        return [
            'avatar' => [
                'width' => 80,
                'height' => 80,
                'circle' => true,
                'formats' => ['webp', 'jpg']
            ],
            'thumbnail' => [
                'width' => 200,
                'height' => 200,
                'formats' => ['webp', 'jpg']
            ],
            'card' => [
                'width' => 400,
                'height' => 400,
                'formats' => ['webp', 'jpg']
            ],
            'original' => [
                'optimize' => true,
                'formats' => ['webp']
            ]
        ];
    }

    /**
     * Process a single conversion variant
     */
    protected function processConversion(Egi $egi, string $originalPath, string $variant, array $config): array {
        $results = [];
        $formats = $config['formats'] ?? ['webp'];

        foreach ($formats as $format) {
            if (!isset($this->converters[$format])) {
                $this->errorManager->handle('IMAGE_OPTIMIZATION_UNSUPPORTED_FORMAT', [
                    'format' => $format,
                    'supported_formats' => 'webp, jpg, jpeg'
                ]);
                throw new Exception("No converter available for format: {$format}");
            }

            $converter = $this->converters[$format];
            $outputPath = $this->generateOutputPath($egi, $variant, $format);

            $result = $converter->convert($originalPath, $outputPath, $config);
            $results[$format] = $result;
        }

        return $results;
    }

    /**
     * Generate output path for optimized variant
     */
    protected function generateOutputPath(Egi $egi, string $variant, string $format): string {
        $basePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $egi->collection_id,
            $egi->user_id
        );

        return sprintf('%s/%d_%s.%s', $basePath, $egi->key_file, $variant, $format);
    }

    /**
     * Initialize available converters
     */
    protected function initializeConverters(): void {
        $this->converters['webp'] = new WebPConverter($this->logger);
        $this->converters['jpg'] = new JpegConverter($this->logger);
        $this->converters['jpeg'] = new JpegConverter($this->logger);
    }
}
