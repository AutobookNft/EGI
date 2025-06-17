<?php
declare(strict_types=1);
namespace App\Supports;

use InvalidArgumentException;

/**
 * Class NotificationViewResolver
 *
 * Resolves the view configuration for notification types based on their FQCN.
 */
class NotificationViewResolver
{
    public static function resolveView(string $notificationType): array
    {
        // Extract class name from FQCN
        $className = class_basename($notificationType);

        // Determine namespace/category
        $category = self::extractCategory($notificationType);

        // Get configuration
        $config = config("notification-views.{$category}.{$className}");

        if (!$config) {
            throw new InvalidArgumentException("No view configuration found for {$className}");
        }

        return $config;
    }

    private static function extractCategory(string $fqcn): string
    {
        // 'App\Notifications\Gdpr\ConsentUpdatedNotification' → 'gdpr'
        $parts = explode('\\', $fqcn);
        return strtolower($parts[2] ?? 'default'); // Assume Notifications\{Category}\
    }
}
