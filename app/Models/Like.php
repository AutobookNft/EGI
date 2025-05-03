<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Like model for storing collection and EGI likes.
 *
 * Uses a polymorphic relationship to support liking different entity types.
 * Implements Oracode principles to maintain code clarity and maintainability.
 *
 * --- Core Logic ---
 * 1. Stores user likes for different entity types (polymorphic)
 * 2. Links likes to users who created them
 * 3. Establishes bidirectional relationships with liked entities
 * 4. Provides convenient access to like metrics
 * --- End Core Logic ---
 *
 * @package App\Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @property int $id
 * @property int $user_id
 * @property int $likeable_id
 * @property string $likeable_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Like extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Get the user who created the like.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the likeable entity (polymorphic).
     *
     * This can be a Collection, EGI, or any other likeable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
    
    /**
     * Scope a query to only include likes for a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope a query to only include likes for a specific entity type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type The class name of the entity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('likeable_type', $type);
    }
}
