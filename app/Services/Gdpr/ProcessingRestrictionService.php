<?php

namespace App\Services\Gdpr;

use App\Enums\Gdpr\ProcessingRestrictionReason;
use App\Enums\Gdpr\ProcessingRestrictionType;
use App\Models\ProcessingRestriction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * GDPR Processing Restriction Service
 *
 * Manages user requests to restrict data processing.
 *
 * @oracode-dimension governance
 * @value-flow Controls data processing restrictions
 * @community-impact Enables user data rights
 * @transparency-level High - clear processing limitations
 * @sustainability-factor High - legal compliance
 */
class ProcessingRestrictionService
{
    /**
     * The error manager instance.
     *
     * @var \Ultra\ErrorManager\Contracts\ErrorManagerInterface
     */
    protected $errorManager;

    /**
     * The activity log service.
     *
     * @var \App\Services\Gdpr\ActivityLogService
     */
    protected $activityLogService;

    /**
     * Create a new service instance.
     *
     * @param  \Ultra\ErrorManager\Contracts\ErrorManagerInterface  $errorManager
     * @param  \App\Services\Gdpr\ActivityLogService  $activityLogService
     * @return void
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        ActivityLogService $activityLogService
    ) {
        $this->errorManager = $errorManager;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get all active processing restrictions for a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserActiveRestrictions(User $user)
    {
        return $user->processingRestrictions()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if the user has reached the maximum allowed active restrictions.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function hasReachedRestrictionLimit(User $user): bool
    {
        $activeRestrictions = $this->getUserActiveRestrictions($user)->count();
        $maxRestrictions = config('gdpr.processing_restriction.max_active_restrictions', 5);

        return $activeRestrictions >= $maxRestrictions;
    }

    /**
     * Create a new processing restriction request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Enums\ProcessingRestrictionType  $type
     * @param  \App\Enums\ProcessingRestrictionReason  $reason
     * @param  string|null  $notes
     * @param  array  $dataCategories
     * @return \App\Models\ProcessingRestriction|null
     */
    public function createRestriction(
        User $user,
        ProcessingRestrictionType $type,
        ProcessingRestrictionReason $reason,
        ?string $notes = null,
        array $dataCategories = []
    ): ?ProcessingRestriction {
        // Check if user has reached the restriction limit
        if ($this->hasReachedRestrictionLimit($user)) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_LIMIT_REACHED', [
                'user_id' => $user->id,
                'active_count' => $this->getUserActiveRestrictions($user)->count(),
                'max_allowed' => config('gdpr.processing_restriction.max_active_restrictions', 5),
            ]);

            return null;
        }

        try {
            // Calculate expiry date if auto-expiry is configured
            $expiryDays = config('gdpr.processing_restriction.auto_expiry_days');
            $expiresAt = $expiryDays ? Carbon::now()->addDays($expiryDays) : null;

            // Create the restriction
            $restriction = new ProcessingRestriction();
            $restriction->user_id = $user->id;
            $restriction->restriction_type = $type->value;
            $restriction->restriction_reason = $reason->value;
            $restriction->status = 'active';
            $restriction->data_categories = !empty($dataCategories) ? json_encode($dataCategories) : null;
            $restriction->notes = $notes;
            $restriction->expires_at = $expiresAt;
            $restriction->save();

            // Log the activity
            $this->activityLogService->logProcessingRestrictionRequested(
                $restriction->id,
                [
                    'restriction_type' => $type->value,
                    'restriction_reason' => $reason->value,
                    'data_categories' => $dataCategories,
                ],
                $user->id
            );

            // Send notification if configured
            if (config('gdpr.processing_restriction.enable_notifications', true)) {
                $this->sendRestrictionNotification($restriction);
            }

            return $restriction;
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_CREATE_ERROR', [
                'user_id' => $user->id,
                'restriction_type' => $type->value,
                'restriction_reason' => $reason->value,
                'error' => $e->getMessage(),
            ], $e);

            return null;
        }
    }

    /**
     * Remove a processing restriction.
     *
     * @param  \App\Models\ProcessingRestriction  $restriction
     * @return bool
     */
    public function removeRestriction(ProcessingRestriction $restriction): bool
    {
        try {
            // Update the restriction status
            $restriction->status = 'removed';
            $restriction->save();

            // Log the activity
            $this->activityLogService->log('processing_restriction_removed', [
                'restriction_id' => $restriction->id,
                'restriction_type' => $restriction->restriction_type,
            ], $restriction->user_id);

            return true;
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_REMOVE_ERROR', [
                'restriction_id' => $restriction->id,
                'user_id' => $restriction->user_id,
                'error' => $e->getMessage(),
            ], $e);

            return false;
        }
    }

    /**
     * Check if a user has an active restriction for the specified processing type.
     *
     * @param  \App\Models\User  $user
     * @param  string  $processingType
     * @param  string|null  $dataCategory
     * @return bool
     */
    public function hasActiveRestriction(
        User $user,
        string $processingType,
        ?string $dataCategory = null
    ): bool {
        // Get active restrictions
        $activeRestrictions = $this->getUserActiveRestrictions($user);

        // Check restrictions
        foreach ($activeRestrictions as $restriction) {
            // Check if this restriction applies to the processing type
            if ($this->restrictionAppliesToProcessingType($restriction, $processingType)) {
                // If data category is specified, check if it's included in the restriction
                if ($dataCategory) {
                    $restrictionCategories = json_decode($restriction->data_categories, true) ?? [];

                    // If no specific categories are set, the restriction applies to all
                    if (empty($restrictionCategories) || in_array($dataCategory, $restrictionCategories)) {
                        return true;
                    }
                } else {
                    // No specific category to check, so restriction applies
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a restriction applies to the specified processing type.
     *
     * @param  \App\Models\ProcessingRestriction  $restriction
     * @param  string  $processingType
     * @return bool
     */
    protected function restrictionAppliesToProcessingType(
        ProcessingRestriction $restriction,
        string $processingType
    ): bool {
        // If it's a full restriction, it applies to all processing
        if ($restriction->restriction_type === ProcessingRestrictionType::RESTRICT_PROCESSING->value) {
            return true;
        }

        // Check specific restriction types
        switch ($processingType) {
            case 'automated_decisions':
                return $restriction->restriction_type === ProcessingRestrictionType::RESTRICT_AUTOMATED_DECISIONS->value;

            case 'marketing':
                return $restriction->restriction_type === ProcessingRestrictionType::RESTRICT_MARKETING->value;

            case 'analytics':
                return $restriction->restriction_type === ProcessingRestrictionType::RESTRICT_ANALYTICS->value;

            case 'third_party':
                return $restriction->restriction_type === ProcessingRestrictionType::RESTRICT_THIRD_PARTY->value;

            default:
                // Unknown processing type, be cautious and return true for full restrictions only
                return $restriction->restriction_type === ProcessingRestrictionType::RESTRICT_PROCESSING->value;
        }
    }

    /**
     * Get expired restrictions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiredRestrictions()
    {
        return ProcessingRestriction::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->get();
    }

    /**
     * Process expired restrictions.
     *
     * @return int Number of restrictions processed
     */
    public function processExpiredRestrictions(): int
    {
        $expiredRestrictions = $this->getExpiredRestrictions();
        $count = 0;

        foreach ($expiredRestrictions as $restriction) {
            try {
                $restriction->status = 'expired';
                $restriction->save();

                // Log the expiration
                $this->activityLogService->log('processing_restriction_expired', [
                    'restriction_id' => $restriction->id,
                    'restriction_type' => $restriction->restriction_type,
                    'expiry_date' => $restriction->expires_at,
                ], $restriction->user_id);

                $count++;
            } catch (\Throwable $e) {
                Log::error('Failed to process expired restriction', [
                    'restriction_id' => $restriction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    /**
     * Send notification about the restriction.
     *
     * @param  \App\Models\ProcessingRestriction  $restriction
     * @return void
     */
    protected function sendRestrictionNotification(ProcessingRestriction $restriction): void
    {
        try {
            $user = $restriction->user;

            if ($user) {
                $notificationClass = config(
                    'gdpr.notifications.email_classes.processing_restricted',
                    \App\Notifications\Gdpr\ProcessingRestrictedNotification::class
                );

                $user->notify(new $notificationClass($restriction));
            }
        } catch (\Throwable $e) {
            // Log the error but don't throw it, as this is a non-critical feature
            Log::error('Failed to send processing restriction notification', [
                'restriction_id' => $restriction->id,
                'user_id' => $restriction->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
