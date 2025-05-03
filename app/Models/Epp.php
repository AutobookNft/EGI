<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EPP (Environment Protection Program) model.
 *
 * Represents environmental projects that can be linked to collections
 * and funded through EGI transactions. Follows Oracode principles
 * for clarity and maintainability.
 *
 * --- Core Logic ---
 * 1. Stores information about environmental protection initiatives
 * 2. Links EPPs with collections that support them
 * 3. Tracks transactions and funding allocated to each EPP
 * 4. Records milestones and progress of environmental projects
 * 5. Provides methods for impact calculation and reporting
 * --- End Core Logic ---
 *
 * @package App\Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 *
 * @property int $id
 * @property string $name
 * @property string $type ARF (Reforestation), APR (Ocean Cleanup), or BPE (Bee Protection)
 * @property string $description
 * @property string $image_path
 * @property string $status active, completed, pending
 * @property float $total_funds
 * @property float $target_funds
 * @property int $organization_id
 * @property int $manager_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Epp extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'epps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'image_path',
        'status',
        'total_funds',
        'target_funds',
        'organization_id',
        'manager_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_funds' => 'float',
        'target_funds' => 'float',
    ];

    /**
     * Get the collections associated with this EPP.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collections()
    {
        return $this->hasMany(Collection::class, 'epp_id');
    }

    /**
     * Get the transactions associated with this EPP.
     *
     * These represent funds allocated to the EPP from EGI sales/resales.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(EppTransaction::class, 'epp_id');
    }

    /**
     * Get the milestones for this EPP.
     *
     * These track progress and achievements of the environmental project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function milestones()
    {
        return $this->hasMany(EppMilestone::class, 'epp_id');
    }

    /**
     * Get the user who manages this EPP.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Calculate the completion percentage of this EPP.
     *
     * @return float
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->target_funds <= 0) {
            return 0;
        }

        $percentage = ($this->total_funds / $this->target_funds) * 100;
        return min(100, $percentage); // Cap at 100%
    }

    /**
     * Get the type name from the type code.
     *
     * @return string
     */
    public function getTypeNameAttribute()
    {
        switch ($this->type) {
            case 'ARF':
                return 'Appropriate Restoration Forestry';
            case 'APR':
                return 'Aquatic Plastic Removal';
            case 'BPE':
                return 'Bee Population Enhancement';
            default:
                return 'Unknown EPP Type';
        }
    }

    /**
     * Scope a query to only include active EPPs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include EPPs of a specific type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if this EPP has reached its funding target.
     *
     * @return bool
     */
    public function isFullyFunded()
    {
        return $this->total_funds >= $this->target_funds;
    }
}
