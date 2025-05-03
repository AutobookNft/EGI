<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Reservation model for storing EGI reservations.
 *
 * Manages both weak (wallet only, 24-hour expiration) and strong 
 * (wallet + personal info, valid until mint) reservations for EGIs.
 *
 * --- Core Logic ---
 * 1. Stores reservation data with appropriate expiration times
 * 2. Links reservations to users and EGIs
 * 3. Provides methods for checking reservation status
 * 4. Handles expiration logic and status management
 * 5. Stores additional contact data for strong reservations
 * --- End Core Logic ---
 *
 * @package App\Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @property int $id
 * @property int $user_id
 * @property int $egi_id
 * @property string $type
 * @property string $status
 * @property \Carbon\Carbon|null $expires_at
 * @property json|null $contact_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'egi_id',
        'type',
        'status',
        'expires_at',
        'contact_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'contact_data' => 'json',
    ];

    /**
     * Get the user who made the reservation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the EGI that was reserved.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egi()
    {
        return $this->belongsTo(Egi::class);
    }
    
    /**
     * Check if the reservation is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        // Strong reservations don't expire
        if ($this->type === 'strong') {
            return false;
        }
        
        // Check if the expiration date is in the past
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    /**
     * Check if the reservation is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }
    
    /**
     * Automatically update expired reservations.
     *
     * This method should be called by a scheduled task to
     * automatically expire reservations that have passed their expiration date.
     *
     * @return int Number of reservations updated
     */
    public static function updateExpired()
    {
        $count = 0;
        
        // Find all active weak reservations that have expired
        $expiredReservations = self::where('status', 'active')
            ->where('type', 'weak')
            ->where('expires_at', '<', Carbon::now())
            ->get();
        
        foreach ($expiredReservations as $reservation) {
            $reservation->status = 'expired';
            $reservation->save();
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Scope a query to only include active reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->where('type', 'strong')
                    ->orWhere(function ($query) {
                        $query->where('type', 'weak')
                            ->where(function ($query) {
                                $query->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', Carbon::now());
                            });
                    });
            });
    }
    
    /**
     * Scope a query to only include weak reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeak($query)
    {
        return $query->where('type', 'weak');
    }
    
    /**
     * Scope a query to only include strong reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStrong($query)
    {
        return $query->where('type', 'strong');
    }
}
