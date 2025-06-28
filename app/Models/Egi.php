<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa SoftDeletes

/**
 * 📜 Oracode Eloquent Model: Egi
 * Represents an Ecological Goods Invent (EGI) record in the database.
 * Each EGI corresponds to a unique digital asset with associated metadata,
 * file information, ownership, and relationship to a Collection.
 *
 * @package     App\Models
 * @version     1.0.0
 * @author      Fabio Cherici & Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     Proprietary // Or your application's license
 *
 * @purpose     Provides an interface to the 'egi' database table, defining fillable attributes,
 *              data type casting, relationships with other models (User, Collection, EgiAudit),
 *              and enabling soft deletion functionality. Essential for creating, updating,
 *              and querying EGI data within the FlorenceEGI application.
 *
 * @context     Used by controllers, handlers (like EgiUploadHandler), services, and potentially
 *              views/Livewire components throughout the application to interact with EGI data.
 *
 * @state       Represents the state of a single row in the 'egi' database table.
 *
 * @property int $id Primary key.
 * @property int $collection_id Foreign key to the 'collections' table.
 * @property int|null $key_file Typically stores the EGI ID itself for image path construction (MVP Q1). Nullable. Indexed.
 * @property string|null $token_EGI Blockchain token identifier (Post-MVP). Nullable.
 * @property array|null $jsonMetadata JSON field for additional metadata (Post-MVP). Nullable. Cast to array.
 * @property int|null $user_id Foreign key to the 'users' table (who uploaded/created). Nullable. Indexed.
 * @property int|null $auction_id Foreign key for auction relationship (Future). Nullable. Indexed.
 * @property int|null $owner_id Foreign key to the 'users'table (current owner). Nullable. Indexed.
 * @property int|null $drop_id Foreign key for drop relationship (Future). Nullable. Indexed.
 * @property string|null $upload_id Identifier for the batch upload process. Nullable.
 * @property string|null $creator Wallet address or identifier of the original creator. Nullable.
 * @property string|null $owner_wallet Wallet address of the current owner. Nullable.
 * @property string|null $drop_title Title of the drop event (Future). Nullable.
 * @property string|null $title The title of the EGI (max 60 chars). Nullable. Indexed.
 * @property string|null $description Textual description of the EGI. Nullable.
 * @property string|null $extension File extension (e.g., 'jpg', 'png'). Nullable.
 * @property bool $media Indicates if it's a non-image media type. Default false (MVP Q1). Nullable. Cast to boolean.
 * @property string|null $type File category (e.g., 'image', 'audio'). Nullable.
 * @property int|null $bind Field potentially related to pairing (Future/Legacy). Nullable. Cast to integer.
 * @property int|null $paired Field potentially related to pairing (Future/Legacy). Nullable. Cast to integer.
 * @property float|null $price Current listing price. Nullable. Cast to decimal:2.
 * @property float|null $floorDropPrice Floor price set during a drop event. Nullable. Cast to decimal:2.
 * @property int|null $position Display order within the collection. Nullable. Cast to integer.
 * @property \Illuminate\Support\Carbon|null $creation_date Optional artistic creation date. Nullable. Cast to date.
 * @property string|null $size Formatted file size (e.g., "1.23 MB"). Nullable.
 * @property string|null $dimension Formatted image dimensions (e.g., "w:1920 x h:1080"). Nullable.
 * @property bool $is_published Indicates if the EGI is publicly visible. Default false. Nullable. Cast to boolean.
 * @property bool $mint Indicates minting status (Post-MVP). Default false. Nullable. Cast to boolean.
 * @property bool $rebind Indicates rebind status (Post-MVP). Default true. Nullable. Cast to boolean.
 * @property string|null $file_crypt Encrypted original filename. Nullable.
 * @property string|null $file_hash MD5 or SHA hash of the file content. Nullable.
 * @property string|null $file_IPFS IPFS hash/path (Post-MVP). Nullable.
 * @property string|null $file_mime File MIME type (e.g., 'image/jpeg'). Nullable.
 * @property string $status Current status ('draft', 'published', 'archived', etc.). Default 'draft'.
 * @property bool $is_public Alias or alternative visibility flag (confirm usage). Default true. Cast to boolean.
 * @property int|null $updated_by Foreign key to 'users' table (who last updated). Nullable.
 * @property \Illuminate\Support\Carbon|null $created_at Timestamp of creation.
 * @property \Illuminate\Support\Carbon|null $updated_at Timestamp of last update.
 * @property \Illuminate\Support\Carbon|null $deleted_at Timestamp for soft delete.
 *
 * @property-read Collection $collection The collection this EGI belongs to.
 * @property-read User|null $user The user who uploaded/created this EGI record.
 * @property-read User|null $owner The current owner of this EGI.
 * @property-read \Illuminate\Database\Eloquent\Collection|EgiAudit[] $audits Audit trail for this EGI.
 *
 * @method static \Database\Factories\EgiFactory factory($count = null, $state = [])
 */
class Egi extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable soft deletes

    /**
     * The table associated with the model.
     * Explicitly defined for clarity.
     *
     * @var string
     */
    protected $table = 'egis';

    /**
     * The attributes that are mass assignable.
     * These fields can be set using `Egi::create([...])` or `$egi->fill([...])`.
     * Includes all fields likely to be set during creation or update via forms/handlers.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_id',
        'key_file',
        'token_EGI',
        'jsonMetadata',
        'user_id', // User performing the action (e.g., upload)
        'auction_id',
        'owner_id', // Current owner
        'drop_id',
        'upload_id',
        'creator', // Original creator identifier/wallet
        'owner_wallet', // Current owner wallet
        'drop_title',
        'title',
        'description',
        'extension',
        'media',
        'type',
        'bind',
        'paired',
        'price',
        'floorDropPrice',
        'position',
        'creation_date',
        'size',
        'dimension',
        'is_published',
        'mint',
        'rebind',
        'file_crypt',
        'file_hash',
        'file_IPFS',
        'file_mime',
        'status',
        'is_public',
        'updated_by',
        // Note: 'id', 'created_at', 'updated_at', 'deleted_at' are typically not fillable
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures data integrity and correct handling (e.g., booleans, dates, numbers).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jsonMetadata'   => 'array',        // Cast JSON string to PHP array
        'media'          => 'boolean',      // Cast 'media' to boolean
        'bind'           => 'integer',      // Cast 'bind' to integer (as per migration)
        'paired'         => 'integer',      // Cast 'paired' to integer (as per migration)
        'price'          => 'decimal:2',    // Cast 'price' to float/decimal with 2 places
        'floorDropPrice' => 'decimal:2',    // Cast 'floorDropPrice' to float/decimal with 2 places
        'position'       => 'integer',      // Cast 'position' to integer
        'creation_date'  => 'date',         // Cast 'creation_date' to Carbon date object (YYYY-MM-DD)
        'is_published'   => 'boolean',      // Cast 'is_published' to boolean
        'mint'           => 'boolean',      // Cast 'mint' to boolean
        'rebind'         => 'boolean',      // Cast 'rebind' to boolean
        'is_public'      => 'boolean',      // Cast 'is_public' to boolean
        'created_at'     => 'datetime',     // Standard timestamp casting
        'updated_at'     => 'datetime',     // Standard timestamp casting
        'deleted_at'     => 'datetime',     // Required for SoftDeletes
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * 🔗 Defines the relationship: An EGI belongs to one Collection.
     *
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI record was created/uploaded by one User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // Assuming 'user_id' is the foreign key linking to the user who uploaded/created the record
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI is currently owned by one User.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        // Assuming 'owner_id' is the foreign key linking to the current owner user
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI can have many Audit records.
     * (Based on the 'egi_audits' migration)
     *
     * @return HasMany
     */
    public function audits(): HasMany
    {
        return $this->hasMany(EgiAudit::class, 'egi_id');
    }

    /**
     * @Oracode Polymorphic relationship for likes
     * 🎯 Purpose: Enable users to like EGIs
     * 🧱 Core Logic: Polymorphic many-to-many via likes table
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the reservations associated with the EGI.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'egi_id', 'id');
    }

    /**
     * Relazione con i certificati di prenotazione
     * Ordinamento: strong prima di weak, poi per offer_amount_eur decrescente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservationCertificates()
    {
        return $this->hasMany(EgiReservationCertificate::class, 'egi_id')
                    ->orderByRaw("CASE
                        WHEN reservation_type = 'strong' THEN 0
                        WHEN reservation_type = 'weak' THEN 1
                        ELSE 2
                    END")
                    ->orderBy('offer_amount_eur', 'desc')
                    ->orderBy('created_at', 'desc'); // Tie-breaker per stesso prezzo
    }


    // Add other relationships as needed (e.g., with Auction, Drop models later)

}
