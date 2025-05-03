<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EPP Milestone model.
 *
 * Represents achievements and progress points for Environmental Protection
 * Programs (EPPs). Tracks specific accomplishments, targets, and evidence
 * of environmental impact.
 *
 * --- Core Logic ---
 * 1. Records specific achievements for environmental projects
 * 2. Tracks progress toward environmental goals
 * 3. Stores evidence and verification of impact
 * 4. Supports transparency and reporting for EPPs
 * 5. Provides timeline of environmental contributions
 * --- End Core Logic ---
 *
 * @package App\Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @property int $id
 * @property int $epp_id
 * @property string $title
 * @property string $description
 * @property string $type accomplishment, target, update
 * @property string $status completed, in_progress, planned
 * @property float $target_value
 * @property float $current_value
 * @property string|null $evidence_url
 * @property string|null $evidence_type
 * @property array|null $media
 * @property \Carbon\Carbon $target_date
 * @property \Carbon\Carbon $completion_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EppMilestone extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'epp_milestones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'epp_id',
        'title',
        'description',
        'type',
        'status',
        'target_value',
        'current_value',
        'evidence_url',
        'evidence_type',
        'media',
        'target_date',
        'completion_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
        'media' => 'array',
        'target_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    /**
     * Get the EPP that this milestone belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function epp()
    {
        return $this->belongsTo(Epp::class, 'epp_id');
    }

    /**
     * Get the completion percentage for this milestone.
     *
     * @return float
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        $percentage = ($this->current_value / $this->target_value) * 100;
        return min(100, $percentage); // Cap at 100%
    }

    /**
     * Check if this milestone is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this milestone is overdue.
     *
     * @return bool
     */
    public function isOverdue()
    {
        return $this->target_date && $this->target_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Scope a query to only include completed milestones.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include in-progress milestones.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include planned milestones.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
     * Scope a query to only include milestones of a specific type.
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
     * Update the milestone's progress.
     *
     * @param float $newValue
     * @param bool $completed
     * @return self
     */
    public function updateProgress($newValue, $completed = false)
    {
        $this->current_value = $newValue;
        
        if ($completed || $newValue >= $this->target_value) {
            $this->status = 'completed';
            $this->completion_date = now();
        } else {
            $this->status = 'in_progress';
        }
        
        $this->save();
        
        return $this;
    }
}
