<?php

namespace App\Repositories;

use App\Models\Icon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Repository: Icon Management with Performance Optimization
 * 🎯 Purpose: Centralized icon retrieval with intelligent caching and fallback
 * 🧱 Core Logic: SVG icon management with style support and user preferences
 * 📡 Performance: Redis caching with 1-hour TTL, eliminated clearCache bottleneck
 * 🛡️ GDPR: No personal data stored, only user preferences for icon style
 *
 * @package App\Repositories
 * @version 2.0.0 MVP Performance Fix
 */
class IconRepository
{
    private UltraLogManager $logger;
    private int $cacheTime = 3600; // 1 hour cache

    /**
     * Constructor with logger injection
     *
     * @param UltraLogManager $logger Ultra log manager for tracking icon operations
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Retrieve specific icon from database or cache
     * PERFORMANCE FIX: Removed automatic cache clearing that killed performance
     *
     * @param string $name Icon name identifier
     * @param string|null $style Icon style (null uses user preference or default)
     * @param string|null $customClass Custom CSS class override
     * @return string|null HTML content of icon or null if not found
     */
    public function getIcon(string $name, ?string $style = null, ?string $customClass = null): ?string
    {
        try {
            // Use user's preferred style if not specified
            if ($style === null) {
                $style = $this->getUserIconStyle();
            }

            // Build cache key
            $cacheKey = $this->buildCacheKey($name, $style, $customClass);

            $this->logger->debug('Icon retrieval attempt', [
                'icon_name' => $name,
                'style' => $style,
                'custom_class' => $customClass,
                'cache_key' => $cacheKey,
                'log_category' => 'ICON_RETRIEVAL'
            ]);

            // Check cache first (PERFORMANCE CRITICAL)
            return Cache::tags(['icons'])->remember($cacheKey, $this->cacheTime, function () use ($name, $style, $customClass) {
                return $this->fetchIconFromDatabase($name, $style, $customClass);
            });

        } catch (\Throwable $e) {
            // Log error and return fallback
            $this->logger->error('Icon retrieval failed', [
                'icon_name' => $name,
                'style' => $style,
                'error_message' => $e->getMessage(),
                'log_category' => 'ICON_ERROR'
            ]);

            // Use UEM for error handling but return fallback
            // UltraError::handle('ICON_RETRIEVAL_FAILED', [
            //     'icon_name' => $name,
            //     'style' => $style,
            //     'error_message' => $e->getMessage()
            // ], $e);

            return $this->getFallbackIcon();
        }
    }

    /**
     * Retrieve icon with default user style
     * Convenience method for most common use case
     *
     * @param string $name Icon name identifier
     * @return string HTML content of icon (guaranteed non-null with fallback)
     */
    public function getDefaultIcon(string $name): string
    {
        $icon = $this->getIcon($name);
        return $icon ?? $this->getFallbackIcon();
    }

    /**
     * Fetch icon data from database
     * Separated for better testability and cache logic clarity
     *
     * @param string $name Icon name
     * @param string $style Icon style
     * @param string|null $customClass Custom CSS class
     * @return string|null Icon HTML or null if not found
     */
    private function fetchIconFromDatabase(string $name, string $style, ?string $customClass): ?string
    {
        $icon = Icon::where('name', $name)
            ->where('style', $style)
            ->first();

        if (!$icon) {
            $this->logger->warning('Icon not found in database', [
                'icon_name' => $name,
                'style' => $style,
                'log_category' => 'ICON_NOT_FOUND'
            ]);

            // Use UEM for tracking but don't throw
            UltraError::handle('ICON_NOT_FOUND', [
                'icon_name' => $name,
                'style' => $style,
                'fallback_used' => true
            ]);

            return null;
        }

        // Apply custom class if provided, otherwise use icon's default class
        $finalClass = $customClass ?? $icon->class;
        $htmlContent = str_replace('%class%', $finalClass, $icon->html);

        $this->logger->debug('Icon retrieved from database', [
            'icon_name' => $name,
            'style' => $style,
            'final_class' => $finalClass,
            'log_category' => 'ICON_SUCCESS'
        ]);

        return $htmlContent;
    }

    /**
     * Get user's preferred icon style
     * Checks authenticated user preferences or falls back to config default
     *
     * @return string User's preferred icon style
     */
    private function getUserIconStyle(): string
    {
        if (Auth::check()) {
            $userStyle = Auth::user()->icon_style;
            if ($userStyle) {
                return $userStyle;
            }
        }

        return config('icons.styles.default', 'elegant');
    }

    /**
     * Get fallback icon for missing icons
     * Provides consistent fallback experience across the application
     *
     * @return string Fallback icon HTML
     */
    protected function getFallbackIcon(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m8 0a2 2 0 01-2 2H9a2 2 0 01-2-2m6 0H9"/>
                </svg>';
    }

    /**
     * Build cache key for icon
     * Uses MD5 hash to prevent long key issues with Redis
     *
     * @param string $name Icon name
     * @param string|null $style Icon style
     * @param string|null $customClass Custom CSS class
     * @return string Cache key
     */
    protected function buildCacheKey(string $name, ?string $style = 'elegant', ?string $customClass = null): string
    {
        $rawKey = "icon:{$style}:{$name}:{$customClass}";
        return 'icon:' . md5($rawKey);
    }

    /**
     * Clear icon cache
     * Manual cache clearing for admin/maintenance operations
     *
     * @param string|null $name Specific icon name (optional)
     * @param string|null $style Specific style (optional)
     * @param string|null $customClass Specific custom class (optional)
     * @return void
     */
    public function clearCache(?string $name = null, ?string $style = null, ?string $customClass = null): void
    {
        try {
            if ($name && $style) {
                // Clear specific icon cache
                $cacheKey = $this->buildCacheKey($name, $style, $customClass);
                Cache::forget($cacheKey);

                $this->logger->info('Specific icon cache cleared', [
                    'icon_name' => $name,
                    'style' => $style,
                    'cache_key' => $cacheKey,
                    'log_category' => 'CACHE_CLEAR'
                ]);
            } else {
                // Clear all icon cache
                if (config('cache.default') === 'redis') {
                    Cache::tags(['icons'])->flush();
                    $this->logger->info('All icon cache cleared via Redis tags', [
                        'log_category' => 'CACHE_CLEAR'
                    ]);
                } else {
                    Cache::flush();
                    $this->logger->warning('Full cache cleared (Redis not available)', [
                        'log_category' => 'CACHE_CLEAR'
                    ]);
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('Cache clear operation failed', [
                'icon_name' => $name,
                'style' => $style,
                'error_message' => $e->getMessage(),
                'log_category' => 'CACHE_ERROR'
            ]);
        }
    }

    /**
     * Preload all icons into cache
     * Useful for application warmup or deployment scripts
     *
     * @return int Number of icons preloaded
     */
    public function preloadIcons(): int
    {
        try {
            $icons = Icon::all();
            $preloadedCount = 0;

            foreach ($icons as $icon) {
                $cacheKey = $this->buildCacheKey($icon->name, $icon->style, null);
                Cache::tags(['icons'])->put($cacheKey, $icon->html, $this->cacheTime);
                $preloadedCount++;
            }

            $this->logger->info('Icons preloaded into cache', [
                'count' => $preloadedCount,
                'log_category' => 'CACHE_PRELOAD'
            ]);

            return $preloadedCount;

        } catch (\Throwable $e) {
            $this->logger->error('Icon preload failed', [
                'error_message' => $e->getMessage(),
                'log_category' => 'CACHE_PRELOAD_ERROR'
            ]);

            return 0;
        }
    }

    /**
     * Get cache statistics for monitoring
     * Useful for debugging and performance monitoring
     *
     * @return array Cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $iconCount = Icon::count();
            $cacheSize = 0;

            // This is approximate - getting exact cache size is Redis-specific
            if (config('cache.default') === 'redis') {
                // Could implement Redis-specific cache size calculation here
                $cacheSize = 'Redis (size calculation not implemented)';
            }

            return [
                'total_icons_in_db' => $iconCount,
                'cache_ttl_seconds' => $this->cacheTime,
                'cache_driver' => config('cache.default'),
                'cache_size' => $cacheSize,
                'cache_tags_supported' => config('cache.default') === 'redis'
            ];

        } catch (\Throwable $e) {
            $this->logger->error('Failed to get cache stats', [
                'error_message' => $e->getMessage(),
                'log_category' => 'CACHE_STATS_ERROR'
            ]);

            return ['error' => 'Failed to retrieve cache statistics'];
        }
    }
}
