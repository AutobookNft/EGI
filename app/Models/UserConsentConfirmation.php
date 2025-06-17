<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package   App\Models
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Represents a single, immutable record of a user confirming a consent action via a notification.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To provide a dedicated audit trail for user consent confirmations, separate from the consent history itself.
 * @os1-compliance: Full.
 */
class UserConsentConfirmation extends Model
{
    use HasFactory;

    protected $table = 'user_consent_confirmations';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_consent_id',
        'notification_id',
        'ip_address',
        'user_agent',
        'confirmation_method',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userConsent(): BelongsTo
    {
        return $this->belongsTo(UserConsent::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(CustomDatabaseNotification::class, 'notification_id');
    }
}
