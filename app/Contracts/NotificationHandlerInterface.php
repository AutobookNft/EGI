<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for notification handlers
 *
 * @package App\Interfaces
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Notification System v3)
 * @date 2025-08-15
 * @purpose Define contract for all notification handlers
 */
interface NotificationHandlerInterface
{
    /**
     * Handle notification response action
     *
     * @param string $action The action to perform
     * @param Model $payload The notification payload model
     * @param array $data Additional data for the action
     * @return array Response array with success status and message
     */
    public function handle(string $action, Model $payload, array $data = []): array;

    /**
     * Get supported actions for this handler
     *
     * @return array List of supported actions
     */
    public function getSupportedActions(): array;
}
