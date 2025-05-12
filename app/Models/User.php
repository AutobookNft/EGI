<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasTeamRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'last_name',
        'email',
        'icon_style',
        'password',
        'current_collection_id',
        'language',
        'wallet',
        'wallet_balance',
        'consent',
        'bio_title',
        'bio_story',
        'title',
        'job_role',
        'username',
        'usertype',
        'street',
        'city',
        'region',
        'state',
        'zip',
        'home_phone',
        'cell_phone',
        'work_phone',
        'site_url',
        'facebook',
        'social_x',
        'tiktok',
        'instagram',
        'snapchat',
        'twitch',
        'linkedin',
        'discord',
        'telegram',
        'other',
        'birth_date',
        'fiscal_code',
        'tax_id_number',
        'doc_typo',
        'doc_num',
        'doc_issue_date',
        'doc_expired_date',
        'doc_issue_from',
        'doc_photo_path_f',
        'doc_photo_path_r',
        'org_name',
        'org_email',
        'org_street',
        'org_city',
        'org_region',
        'org_state',
        'org_zip',
        'org_site_url',
        'annotation',
        'org_phone_1',
        'org_phone_2',
        'org_phone_3',
        'rea',
        'org_fiscal_code',
        'org_vat_number',
        'profile_photo_path',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'doc_issue_date' => 'date',
        'doc_expired_date' => 'date',
        'wallet_balance' => 'decimal:4',
        'consent' => 'boolean',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'icon_style',
    ];

    public function getIconStyleAttribute(): string
    {

        Log::channel('florenceegi')->info('User:getIconStyleAttribute', [
            'icon_style' => $this->attributes['icon_style'] ?? config('icons.styles.default'),
        ]);

        // Ritorna l'icon_style dall'attributo o un valore di default
        return $this->attributes['icon_style'] ?? config('icons.styles.default');
    }

     /**
     * Get the collections created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedCollections(): HasMany
    {
        return $this->hasMany(Collection::class, 'creator_id');
    }

    // In app/Models/User.php
    public function getCurrentCollectionDetails()
    {
        if (!$this->current_collection_id) {
            return [
                'current_collection_id' => null,
                'current_collection_name' => null,
                'can_edit_current_collection' => false,
            ];
        }
        
        $collection = $this->currentCollection;
        return [
            'current_collection_id' => $collection->id,
            'current_collection_name' => $collection->collection_name,
            'can_edit_current_collection' => $this->can('manage_collection', $collection),
        ];
    }

    /**
     * Get the collections the user collaborates on.
     *
     * This relationship retrieves collections where the user is listed as a collaborator
     * in the 'collection_user' pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collaborations(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
                    ->withPivot('role') // Include il campo 'role' dalla tabella pivot
                    ->withTimestamps(); // Include created_at e updated_at dalla tabella pivot
    }

    /**
     * Get the user's current active collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentCollection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'current_collection_id');
    }


    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function customNotifications()
    {
        return $this->morphMany(CustomDatabaseNotification::class, 'notifiable');
    }

    public function walletChangeProposer()
    {
        return $this->hasMany(NotificationPayloadWallet::class, 'proposer_id');
    }

    public function walletChangeReceiver()
    {
        return $this->hasMany(NotificationPayloadWallet::class, 'receiver_id');
    }

    public function currentCollectionBySession()
    {
        $id = session('current_collection_id')
            ?? $this->current_collection_id;

        Log::channel('florenceegi')->info('User:currentCollection', [
            'current_collection_id' => $id,
        ]);

        return \App\Models\Collection::find($id);
    }

}
