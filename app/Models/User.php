<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasTeamRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use Notifiable;


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
        'user_id',
        'name',
        'last_name',
        'email',
        'icon_style',
        'password',
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
        'current_team_id',
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
    ];

    public function getIconStyleAttribute(): string
    {
        // Ritorna l'icon_style dall'attributo o un valore di default
        return $this->attributes['icon_style'] ?? config('icons.default');
    }


    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function customNotifications()
    {
        return $this->hasMany(CustomDatabaseNotification::class, 'notifiable_id')
                    ->where('notifiable_type', self::class);
    }

    public function walletChangeProposer()
    {
        return $this->hasMany(WalletChangeApprovalModel::class, 'proposer_id');
    }

    public function walletChangeReceiver()
    {
        return $this->hasMany(WalletChangeApprovalModel::class, 'receiver_id');
    }

}
