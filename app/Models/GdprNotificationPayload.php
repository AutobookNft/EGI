<?php

namespace App\Models;

use App\Enums\Gdpr\GdprNotificationStatus;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @package   App\Models
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Represents the stateful subject (payload) of a GDPR notification, decoupling the event's state from the delivery mechanism.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To provide a persistent, queryable representation for every specific GDPR-related notification event within the system. This model acts as the "subject" or "payload" of a notification, decoupling the event's data from the notification delivery mechanism.
 * @oracode-value-flow:
 * 1.  INPUT: An event (e.g., a user's data export is ready) triggers the creation of a NotificationPayloadGdpr instance.
 * 2.  PROCESS: The instance stores the event type, the user involved, and its current status ('pending', 'done').
 * 3.  OUTPUT: It becomes the relatable model for one or more CustomDatabaseNotification records, providing them with specific context and state.
 * @oracode-arch-pattern: Payload Model (in a Notification System). This centralizes the state of a specific communication, allowing multiple notification records (e.g., initial request, reminders) to refer to a single, stateful source of truth.
 * @oracode-sustainability-factor: HIGH. Its structure is generic enough to accommodate any new GDPR notification type by simply adding a new 'type' string, without requiring database schema changes.
 * @os1-compliance: Full.
 *
 * 🎯 **NotificationPayloadGdpr Model**
 *
 * 🧱 **Core Logic:** Represents a specific GDPR-related event that requires notifying a user.
 * It serves as the central stateful payload for all associated notifications. For instance, when a data export is completed,
 * a single NotificationPayloadGdpr record is created. The actual notification sent to the user (the CustomDatabaseNotification record)
 * will then point to this payload. This ensures that the state of the GDPR event itself (e.g., from 'pending' to 'done') is tracked
 * independently of the notification's read/unread status.
 *
 * 📡 **Communicates With:**
 * - `GdprNotificationService`: Creates instances of this model.
 * - `CustomDatabaseNotification`: Relates to this model via a polymorphic relationship.
 * - `User`: Belongs to a specific user.
 *
 * 🧪 **Testability:** Highly testable. Can be created, updated, and asserted against in feature tests (e.g., `GdprNotificationTest`).
 *
 * 🛡️ **GDPR Considerations:** This model stores user_id and potentially sensitive context in the `data` field.
 * Access must be restricted via policies. The `data` field should not store raw PII if it can be avoided;
 * instead, it should store references or non-sensitive metadata.
 */
class GdprNotificationPayload extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gdpr_notification_payloads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gdpr_notification_type',
        'previous_value',
        'new_value',
        'email',
        'role',
        'message',
        'ip_address',
        'user_agent',
        'payload_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload_status' => GdprNotificationStatus::class, // Casts the status to the GdprNotificationStatus Enum
    ];

    public function updateStatus(GdprNotificationStatus $status): self
    {
        $this->payload_status = $status;
        $this->save();
        return $this;
    }

    /**
     * Update the status of the notification payload.
     *
     * @param GdprNotificationStatus $status The new status to set.
     * @constant USER_CONFIRMED_ACTION
     * @return self
     */
    public function markAsUserConfirmed(): self {
        return $this->updateStatus(GdprNotificationStatus::USER_CONFIRMED_ACTION);
    }

    /**
     * Update the status of the notification payload.
     *
     * @param GdprNotificationStatus $status The new status to set.
     * @constant USER_REVOKED_CONSENT
     * @return $this
     */
    public function markAsUserRevoked(): self {
        return $this->updateStatus(GdprNotificationStatus::USER_REVOKED_CONSENT);
    }

    /**
     * Update the status of the notification payload.
     *
     * @param GdprNotificationStatus $status The new status to set.
     * @constant USER_DISAVOWED_SUSPICIOUS
     * @return $this
     */
    public function markAsUserDisavowedSuspicious(): self {
        return $this->updateStatus(GdprNotificationStatus::USER_DISAVOWED_SUSPICIOUS);
    }
    /**
     * Check if the notification is awaiting user confirmation.
     *
     * @param GdprNotificationStatus $status The status to check against.
     * @constant PENDING_USER_CONFIRMATION
     * @return $this
     */
    public function isAwaitingUserConfirmation(): bool {
        return $this->payload_status === GdprNotificationStatus::PENDING_USER_CONFIRMATION;
    }

    /**
     * Check if the user has confirmed the legitimacy of the notification.
     *
     * @param GdprNotificationStatus $status The status to check against.
     * @constant USER_CONFIRMED_ACTION
     * @return $this
     */
    public function hasUserConfirmedLegitimacy(): bool {
        return $this->payload_status === GdprNotificationStatus::USER_CONFIRMED_ACTION;
    }

    /**
     * Get all the notifications for this payload.
     *
     * 🎯 **Relationship**: Defines the polymorphic one-to-many relationship to the notification records.
     * This is the core link that allows a single GDPR event payload to have multiple notification entries if needed.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(CustomDatabaseNotification::class, 'model');
    }

    /**
     * Get the user that this payload belongs to.
     *
     * 🎯 **Relationship**: Defines the inverse one-to-one relationship with the User model.
     * Each GDPR payload is associated with exactly one user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }




}
