<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 📜 Oracode Eloquent Model: EgiAudit
 * Represents a record of changes made to an Egi model.
 * Tracks the old and new values of attributes that were modified,
 * the user who performed the action, and the type of action (create, update, delete).
 *
 * @package     App\Models
 * @version     1.0.0
 * @author      Fabio Cherici & Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     Proprietary // Or your application's license
 *
 * @purpose     Provides an interface to the 'egi_audits' database table, enabling the tracking
 *              and querying of historical changes to EGI records. Essential for traceability,
 *              debugging, and understanding the lifecycle of an EGI. Supports Oracode's
 *              Interrogability and Resilience pillars by preserving history.
 *
 * @context     Typically created automatically by model observers (like an EgiObserver) or
 *              manually within services/handlers when significant changes occur to an Egi model.
 *              Read operations might occur in administrative interfaces or reporting tools.
 *
 * @state       Represents a single historical change event for one Egi record.
 *
 * @property int $id Primary key.
 * @property int $egi_id Foreign key to the 'egi' table, linking the audit record to the specific EGI.
 * @property int $user_id Foreign key to the 'users' table, indicating the user who initiated the change.
 * @property array|null $old_values JSON containing the attribute values *before* the change. Nullable (e.g., for 'create' action). Cast to array.
 * @property array|null $new_values JSON containing the attribute values *after* the change. Nullable (e.g., for 'delete' action). Cast to array.
 * @property string $action The type of action performed ('create', 'update', 'delete', or potentially others).
 * @property \Illuminate\Support\Carbon|null $created_at Timestamp when the audit record was created (effectively, when the change occurred).
 * @property \Illuminate\Support\Carbon|null $updated_at Timestamp of last update (usually same as created_at for audit logs).
 *
 * @property-read Egi $egi The Egi model associated with this audit record.
 * @property-read User $user The User who performed the action recorded in this audit log.
 *
 * @method static \Database\Factories\EgiAuditFactory factory($count = null, $state = []) // If you create a factory
 */
class EgiAudit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Explicitly defined for clarity.
     *
     * @var string
     */
    protected $table = 'egi_audits';

    /**
     * Indicates if the model should NOT be timestamped with updated_at.
     * Audit logs typically only need created_at. Set to false if you want updated_at.
     *
     * @var bool
     */
    const UPDATED_AT = null; // Disable updated_at for audit logs

    /**
     * The attributes that are mass assignable.
     * These fields are typically set when creating an audit record.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'egi_id',
        'user_id',
        'old_values',
        'new_values',
        'action',
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures JSON fields are handled as PHP arrays.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array', // Cast JSON string to PHP array
        'new_values' => 'array', // Cast JSON string to PHP array
        'created_at' => 'datetime', // Standard timestamp casting
        // 'updated_at' is disabled by setting const UPDATED_AT = null
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * 🔗 Defines the relationship: An Audit record belongs to one Egi.
     *
     * @return BelongsTo
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class, 'egi_id');
    }

    /**
     * 🔗 Defines the relationship: An Audit record belongs to one User (who performed the action).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
