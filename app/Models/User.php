<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gdpr\ConsentStatus;
use App\Enums\Gdpr\DataExportStatus;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Enums\Gdpr\GdprRequestStatus;
use App\Enums\Gdpr\GdprRequestType;
use App\Models\Egi;
use App\Traits\HasTeamRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Traits\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia {
    use HasApiTokens;
    use HasRoles;
    use InteractsWithMedia;

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
        'name',
        'nick_name',
        'last_name',
        'email',
        'preferred_currency',
        'avatar_url',
        'password',
        'usertype',
        'current_collection_id',
        'consent_summary',
        'consents_updated_at',
        'processing_limitations',
        'limitations_updated_at',
        'has_pending_gdpr_requests',
        'last_gdpr_request_at',
        'gdpr_compliant',
        'gdpr_status_updated_at',
        'data_retention_until',
        'retention_reason',
        'privacy_settings',
        'preferred_communication_method',
        'last_activity_logged_at',
        'total_gdpr_requests',
        'profile_photo_path',
        'created_via',
        'language',
        'wallet',
        'wallet_balance',
        'personal_secret',
        'is_weak_auth',
        'icon_style',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wallet_balance' => 'decimal:4',
        'consent' => 'boolean',
        'consent_summary' => 'array',
        'processing_limitations' => 'array',
        'privacy_settings' => 'array',
        'consents_updated_at' => 'datetime',
        'limitations_updated_at' => 'datetime',
        'last_gdpr_request_at' => 'datetime',
        'gdpr_status_updated_at' => 'datetime',
        'data_retention_until' => 'datetime',
        'last_activity_logged_at' => 'datetime',
        'has_pending_gdpr_requests' => 'boolean',
        'gdpr_compliant' => 'boolean',
        'total_gdpr_requests' => 'integer',
        'is_weak_auth' => 'boolean',
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

    public function getIconStyleAttribute(): string {

        Log::channel('florenceegi')->info('User:getIconStyleAttribute', [
            'icon_style' => $this->attributes['icon_style'] ?? config('icons.styles.default'),
        ]);

        // Ritorna l'icon_style dall'attributo o un valore di default
        return $this->attributes['icon_style'] ?? config('icons.styles.default');
    }

    /**
     * Get the display name (nickname if present, otherwise name).
     *
     * @return string
     */
    public function getNameAttribute(): string {
        return $this->attributes['nick_name'] ?? $this->attributes['name'];
    }

    /**
     * Get the legal name (always the raw name field).
     *
     * @return string
     */
    public function getLegalNameAttribute(): string {
        return $this->attributes['name'];
    }

    /**
     * Get the collections created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedCollections(): HasMany {
        return $this->hasMany(Collection::class, 'creator_id');
    }

    /**
     * Get the EGIs created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdEgis(): HasMany {
        return $this->hasMany(Egi::class, 'user_id');
    }

    /**
     * Alias for ownedCollections for consistency
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdCollections(): HasMany {
        return $this->ownedCollections();
    }

    // In app/Models/User.php
    public function getCurrentCollectionDetails() {
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
     * Get the current collection name
     *
     * @return string|null
     */
    public function getCurrentCollectionName(): ?string {
        if (!$this->current_collection_id) {
            return null;
        }

        $collection = $this->currentCollection;
        return $collection ? $collection->collection_name : null;
    }

    /**
     * Get the current collection EGI count
     *
     * @return int
     */
    public function getCurrentCollectionEgiCount(): int {
        if (!$this->current_collection_id) {
            return 0;
        }

        $collection = $this->currentCollection;
        return $collection ? $collection->egis()->count() : 0;
    }

    /**
     * Get the collections the user collaborates on.
     *
     * This relationship retrieves collections where the user is listed as a collaborator
     * in the 'collection_user' pivot table, excluding collections where the user is the owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collaborations(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot(['role', 'is_owner']) // Include both role and is_owner from pivot table
            ->wherePivot('is_owner', '!=', 1) // Exclude collections where user is owner
            ->withTimestamps(); // Include created_at e updated_at dalla tabella pivot
    }

    /**
     * Get all collections accessible to the user (owned + collaborations).
     * This is a unified relationship that includes both owned and collaborated collections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collections(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->wherePivot('status', '!=', 'removed') // Exclude removed collaborations
            ->wherePivotNull('removed_at'); // Only active relationships
    }

    /**
     * Get all collections where user is the owner.
     * This relationship uses the pivot table's is_owner flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ownedCollectionsViaPivot(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->wherePivot('is_owner', true)
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Get collections where user has specific roles.
     *
     * @param array|string $roles
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collectionsWithRole($roles): BelongsToMany {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->whereIn('collection_user.role', $roles)
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Get collections where user can edit (owner or editor role).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function editableCollections(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->where(function ($query) {
                $query->wherePivot('is_owner', true)
                    ->orWhereIn('collection_user.role', ['admin', 'editor']);
            })
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Check if user has access to a specific collection.
     *
     * @param int $collectionId
     * @return bool
     */
    public function hasAccessToCollection(int $collectionId): bool {
        return $this->collections()
            ->where('collection_id', $collectionId)
            ->exists();
    }

    /**
     * Get user's role in a specific collection.
     *
     * @param int $collectionId
     * @return string|null
     */
    public function getRoleInCollection(int $collectionId): ?string {
        $pivot = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        if (!$pivot) {
            return null;
        }

        // If owner, return 'owner', otherwise return the role
        return $pivot->pivot->is_owner ? 'owner' : $pivot->pivot->role;
    }

    /**
     * Check if user can edit a specific collection.
     *
     * @param int $collectionId
     * @return bool
     */
    public function canEditCollectionById(int $collectionId): bool {
        $pivot = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        if (!$pivot) {
            return false;
        }

        return $pivot->pivot->is_owner ||
            in_array($pivot->pivot->role, ['admin', 'editor']);
    }

    /**
     * Join a collection with specified role.
     *
     * @param int $collectionId
     * @param string $role
     * @param bool $isOwner
     * @param array $metadata
     * @return bool
     */
    public function joinCollection(int $collectionId, string $role = 'viewer', bool $isOwner = false, array $metadata = []): bool {
        try {
            $this->collections()->attach($collectionId, [
                'role' => $role,
                'is_owner' => $isOwner,
                'status' => 'active',
                'joined_at' => now(),
                'metadata' => $metadata ? json_encode($metadata) : null,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Leave a collection (soft removal).
     *
     * @param int $collectionId
     * @return bool
     */
    public function leaveCollection(int $collectionId): bool {
        try {
            $this->collections()->updateExistingPivot($collectionId, [
                'status' => 'removed',
                'removed_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update user's role in a collection.
     *
     * @param int $collectionId
     * @param string $newRole
     * @return bool
     */
    public function updateRoleInCollection(int $collectionId, string $newRole): bool {
        try {
            $this->collections()->updateExistingPivot($collectionId, [
                'role' => $newRole,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the user's current active collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentCollection(): BelongsTo {
        return $this->belongsTo(Collection::class, 'current_collection_id');
    }

    /**
     * Get the user's personal data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function personalData(): HasOne {
        return $this->hasOne(UserPersonalData::class);
    }

    public function wallets() {
        return $this->hasMany(Wallet::class);
    }

    public function customNotifications() {
        return $this->morphMany(CustomDatabaseNotification::class, 'notifiable');
    }

    public function walletChangeProposer() {
        return $this->hasMany(NotificationPayloadWallet::class, 'proposer_id');
    }

    public function walletChangeReceiver() {
        return $this->hasMany(NotificationPayloadWallet::class, 'receiver_id');
    }

    public function currentCollectionBySession() {
        $id = session('current_collection_id')
            ?? $this->current_collection_id;

        Log::channel('florenceegi')->info('User:currentCollection', [
            'current_collection_id' => $id,
        ]);

        return \App\Models\Collection::find($id);
    }

    // Nel modello User
    public function canEditCollection(Collection $collection): bool {
        // È creator o owner
        if ($collection->creator_id === $this->id || $collection->owner_id === $this->id) {
            return true;
        }

        // O ha un ruolo specifico nella pivot
        $pivot = $this->collaborations()
            ->where('collection_id', $collection->id)
            ->first();

        return $pivot && in_array($pivot->pivot->role, ['editor', 'admin']);
    }

    public function getRouteKeyName(): string {
        return 'id';
    }

    public function getRouteKey(): string {
        return $this->getAttribute($this->getRouteKeyName());
    }

    public function getRouteKeyNameForCollection(): string {
        return 'collection_id';
    }

    public function getRouteKeyForCollection(): string {
        return $this->getAttribute($this->getRouteKeyNameForCollection());
    }


    /**
     * @Oracode User Model GDPR Extensions
     * 🎯 Purpose: Add GDPR relationships to existing User model
     * 🧱 Core Logic: Extend User model with all GDPR-related relationships
     * 📡 API: Relationships for GDPR data management
     * 🛡️ GDPR: Complete data subject relationship mapping
     *
     * ADD THESE METHODS TO THE EXISTING App\Models\User CLASS
     *
     * @package App\Models
     * @version 1.0
     */

    /**
     * Get all the consent records for the user.
     * This represents the user's consent history log.
     *
     * @return HasMany
     */
    public function consents(): HasMany {
        return $this->hasMany(UserConsent::class, 'user_id');
    }

    /**
     * Get the full forensic audit log for the user's consents.
     *
     * Recupera la cronologia di audit completa e dettagliata, come registrata
     * nella tabella `consent_histories`. Utile per scopi di compliance e legali.
     *
     * @return HasMany
     */
    public function consentAuditLog(): HasMany {
        return $this->hasMany(ConsentHistory::class, 'user_id');
    }

    /**
     * Get current active consents for this user.
     *
     * @return HasMany
     */
    public function activeConsents(): HasMany {
        return $this->hasMany(UserConsent::class)
            ->where('status', ConsentStatus::ACTIVE->value)
            ->whereNull('withdrawn_at');
    }


    /**
     * Get all GDPR requests made by this user.
     *
     * @return HasMany
     */
    public function gdprRequests(): HasMany {
        return $this->hasMany(GdprRequest::class);
    }

    /**
     * Get pending GDPR requests for this user.
     *
     * @return HasMany
     */
    public function pendingGdprRequests(): HasMany {
        return $this->hasMany(GdprRequest::class)
            ->whereIn('status', ['pending', 'in_progress', 'verification_required']);
    }

    /**
     * Get user activity logs for this user.
     *
     * @return HasMany
     */
    public function activities(): HasMany {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get login history for this user from user activities.
     *
     * @return HasMany
     */
    public function loginHistory(): HasMany {
        return $this->hasMany(UserActivity::class)
            ->where('category', GdprActivityCategory::AUTHENTICATION_LOGIN->value);
    }

    /**
     * Payment distributions received by this user
     * @return HasMany
     */
    public function paymentDistributions(): HasMany {
        return $this->hasMany(PaymentDistribution::class);
    }

    /**
     * Get security events related to this user.
     *
     * @return HasMany
     */
    public function securityEvents(): HasMany {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * Get high-risk security events for this user.
     *
     * @return HasMany
     */
    public function highRiskSecurityEvents(): HasMany {
        return $this->hasMany(SecurityEvent::class)
            ->highRisk()
            ->unresolved();
    }

    /**
     * Get data exports requested by this user.
     *
     * @return HasMany
     */
    public function dataExports(): HasMany {
        return $this->hasMany(DataExport::class);
    }

    /**
     * Get available data exports for download.
     *
     * @return HasMany
     */
    public function availableDataExports(): HasMany {
        return $this->hasMany(DataExport::class)
            ->where('status', DataExportStatus::COMPLETED->value)
            ->where('expires_at', '>', now());
    }

    /**
     * Get breach reports submitted by this user.
     *
     * @return HasMany
     */
    public function breachReports(): HasMany {
        return $this->hasMany(BreachReport::class);
    }

    /**
     * Get open breach reports from this user.
     *
     * @return HasMany
     */
    public function openBreachReports(): HasMany {
        return $this->hasMany(BreachReport::class)->open();
    }

    /**
     * Get GDPR audit logs where this user is the data subject.
     *
     * @return HasMany
     */
    public function gdprAuditLogs(): HasMany {
        return $this->hasMany(GdprAuditLog::class, 'data_subject_id');
    }

    /**
     * Get GDPR audit logs where this user performed the action.
     *
     * @return HasMany
     */
    public function performedAuditLogs(): HasMany {
        return $this->hasMany(GdprAuditLog::class, 'user_id');
    }

    /**
     * Get messages sent to DPO by this user.
     *
     * @return HasMany
     */
    public function dpoMessages(): HasMany {
        return $this->hasMany(DpoMessage::class);
    }

    /**
     * Get open DPO messages from this user.
     *
     * @return HasMany
     */
    public function openDpoMessages(): HasMany {
        return $this->hasMany(DpoMessage::class)->open();
    }

    /**
     * Get privacy policies created by this user (if admin).
     *
     * @return HasMany
     */
    public function createdPrivacyPolicies(): HasMany {
        return $this->hasMany(PrivacyPolicy::class, 'created_by');
    }

    /**
     * Get privacy policies approved by this user (if legal reviewer).
     *
     * @return HasMany
     */
    public function approvedPrivacyPolicies(): HasMany {
        return $this->hasMany(PrivacyPolicy::class, 'approved_by');
    }

    /**
     * Get breach reports assigned to this user for investigation.
     *
     * @return HasMany
     */
    public function assignedBreachReports(): HasMany {
        return $this->hasMany(BreachReport::class, 'assigned_to');
    }

    // ====================================================================================
    // ADD THESE GDPR-SPECIFIC HELPER METHODS TO THE User CLASS
    // ====================================================================================

    /**
     * Check if user has given consent for specific purpose.
     *
     * @param string $purpose Consent purpose
     * @return bool
     */
    public function hasActiveConsent(string $purpose): bool {
        return $this->activeConsents()
            ->where('consent_type', $purpose)
            ->exists();
    }

    /**
     * Get user's current consent status for specific purpose.
     *
     * @param string $purpose Consent purpose
     * @return string|null Status or null if no consent given
     */
    public function getConsentStatus(string $purpose): ?string {
        $consent = $this->consents()
            ->where('consent_type', $purpose)
            ->orderBy('created_at', 'desc')
            ->first();

        return $consent?->status;
    }

    /**
     * Check if user has any pending GDPR requests.
     *
     * @return bool
     */
    public function hasPendingGdprRequests(): bool {
        return $this->pendingGdprRequests()->exists();
    }

    /**
     * Check if user has requested account deletion.
     *
     * @return bool
     */
    public function hasRequestedDeletion(): bool {
        return $this->gdprRequests()
            ->where('request_type', GdprRequestType::ERASURE->value)
            ->whereIn('status', [
                GdprRequestStatus::PENDING->value,
                GdprRequestStatus::IN_PROGRESS->value,
                GdprRequestStatus::VERIFICATION_REQUIRED->value
            ])
            ->exists();
    }

    /**
     * Check if user has recent security incidents.
     *
     * @param int $hours Hours to look back (default 24)
     * @return bool
     */
    public function hasRecentSecurityIncidents(int $hours = 24): bool {
        return $this->securityEvents()
            ->where('created_at', '>=', now()->subHours($hours))
            ->highRisk()
            ->exists();
    }

    /**
     * Get user's GDPR compliance score (0-100).
     *
     * @return int
     */
    public function getGdprComplianceScore(): int {
        $score = 100;

        // Deduct points for missing consents
        $requiredConsents = ['marketing', 'analytics', 'cookies'];
        foreach ($requiredConsents as $purpose) {
            if (!$this->hasActiveConsent($purpose)) {
                $score -= 10;
            }
        }

        // Deduct points for unresolved security events
        $unresolvedEvents = $this->securityEvents()->unresolved()->count();
        $score -= min($unresolvedEvents * 5, 30);

        // Deduct points for overdue GDPR requests
        $overdueRequests = $this->gdprRequests()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'rejected'])
            ->count();
        $score -= min($overdueRequests * 15, 45);

        return max(0, $score);
    }

    /**
     * Get summary of user's GDPR data for dashboard.
     *
     * @return array
     */
    public function getGdprSummary(): array {
        return [
            'active_consents' => $this->activeConsents()->count(),
            'pending_requests' => $this->pendingGdprRequests()->count(),
            'completed_requests' => $this->gdprRequests()
                ->where('status', 'completed')
                ->count(),
            'available_exports' => $this->availableDataExports()->count(),
            'open_breach_reports' => $this->openBreachReports()->count(),
            'recent_security_events' => $this->securityEvents()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'compliance_score' => $this->getGdprComplianceScore(),
            'last_activity' => $this->activities()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at,
            'account_created' => $this->created_at,
            'last_consent_update' => $this->consents()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at,
        ];
    }

    /**
     * Check if user can request data export (rate limiting).
     *
     * @return bool
     */
    public function canRequestDataExport(): bool {
        // Allow one export per 30 days
        $recentExport = $this->dataExports()
            ->where('created_at', '>=', now()->subDays(30))
            ->first();

        return is_null($recentExport);
    }

    /**
     * Check if user can submit breach report (rate limiting).
     *
     * @return bool
     */
    public function canSubmitBreachReport(): bool {
        // Allow max 5 reports per day
        $todayReports = $this->breachReports()
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        return $todayReports < 5;
    }

    /**
     * Get user's preferred communication language for GDPR notices.
     *
     * @return string
     */
    public function getGdprLanguage(): string {
        return $this->gdpr_language ?? $this->language ?? 'en';
    }

    /**
     * Check if user has opted out of GDPR email notifications.
     *
     * @return bool
     */
    public function hasOptedOutGdprNotifications(): bool {
        return $this->gdpr_notifications_disabled ?? false;
    }

    /**
     * Mark user for GDPR data review (for compliance audits).
     *
     * @param string $reason Review reason
     * @return bool
     */
    public function markForGdprReview(string $reason): bool {
        $this->gdpr_review_required = true;
        $this->gdpr_review_reason = $reason;
        $this->gdpr_review_date = now();
        return $this->save();
    }

    /**
     * Clear GDPR review flag.
     *
     * @return bool
     */
    public function clearGdprReview(): bool {
        $this->gdpr_review_required = false;
        $this->gdpr_review_reason = null;
        $this->gdpr_review_completed_at = now();
        return $this->save();
    }

    // ====================================================================================
    // ADD THESE SCOPES TO THE User CLASS
    // ====================================================================================

    /**
     * Scope for users requiring GDPR review.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresGdprReview($query) {
        return $query->where('gdpr_review_required', true);
    }

    /**
     * Scope for users with pending GDPR requests.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPendingGdprRequests($query) {
        return $query->whereHas('gdprRequests', function ($q) {
            $q->whereIn('status', array_map(
                fn($status) => $status->value,
                array_filter(GdprRequestStatus::cases(), fn($status) => $status->isActive())
            ));
        });
    }

    /**
     * Get active processing restrictions.
     */
    public function activeProcessingRestrictions(): HasMany {
        return $this->processingRestrictions()
            ->where('is_active', true)
            ->whereNull('lifted_at');
    }

    /**
     * Check if user has active consent for specific purpose.
     *
     * @param string $purpose
     * @return bool
     */
    public function hasConsentFor(string $purpose): bool {
        return $this->consents()
            ->where('consent_type', $purpose)
            ->where('status', ConsentStatus::ACTIVE->value)
            ->whereNull('withdrawn_at')
            ->exists();
    }

    /**
     * Scope for users with recent security incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $hours Hours to look back
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRecentSecurityIncidents($query, int $hours = 24) {
        return $query->whereHas('securityEvents', function ($q) use ($hours) {
            $q->where('created_at', '>=', now()->subHours($hours))
                ->highRisk();
        });
    }

    /**
     * Scope for users eligible for data export.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEligibleForDataExport($query) {
        return $query->whereDoesntHave('dataExports', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        });
    }

    /**
     * @Oracode Relationship: User Biographies
     * 🔗 Purpose: One-to-many relationship with user biographies
     * 📊 Ordering: Most recent biographies first
     * 🔍 Usage: $user->biographies()->get()
     */
    public function biographies(): HasMany {
        return $this->hasMany(Biography::class)
            ->orderBy('updated_at', 'desc');
    }

    /**
     * @Oracode Relationship: Active Biography
     * 🔗 Purpose: Get user's primary/most recent biography
     * 🎯 Logic: Most recently updated biography (for quick access)
     * 🔍 Usage: $user->activeBiography
     */
    public function activeBiography(): HasOne {
        return $this->hasOne(Biography::class)
            ->latestOfMany('updated_at');
    }

    /**
     * @Oracode Relationship: Public Biographies
     * 🔗 Purpose: Only public biographies for profile display
     * 🛡️ Privacy: Respects user privacy settings
     * 🔍 Usage: $user->publicBiographies()->get()
     */
    public function publicBiographies(): HasMany {
        return $this->biographies()
            ->where('is_public', true);
    }

    /**
     * @Oracode Relationship: Completed Biographies
     * 🔗 Purpose: Filter biographies marked as completed
     * 📊 Quality: Show only finished biographies
     * 🔍 Usage: $user->completedBiographies()->get()
     */
    public function completedBiographies(): HasMany {
        return $this->biographies()
            ->where('is_completed', true);
    }

    /**
     * @Oracode Method: Has Biography
     * 🎯 Purpose: Quick check if user has any biography
     * 📤 Returns: Boolean indicating biography existence
     * 🔍 Usage: if ($user->hasBiography()) { ... }
     */
    public function hasBiography(): bool {
        return $this->biographies()->exists();
    }

    /**
     * @Oracode Method: Has Public Biography
     * 🎯 Purpose: Check if user has at least one public biography
     * 📤 Returns: Boolean for profile display logic
     * 🔍 Usage: if ($user->hasPublicBiography()) { ... }
     */
    public function hasPublicBiography(): bool {
        return $this->publicBiographies()->exists();
    }

    /**
     * @Oracode Method: Get Primary Biography
     * 🎯 Purpose: Get the main biography for display
     * 📊 Logic: Public > Completed > Most Recent
     * 📤 Returns: Biography model or null
     */
    public function getPrimaryBiography(): ?Biography {
        // Try public first
        $public = $this->publicBiographies()->first();
        if ($public) {
            return $public;
        }

        // Then completed
        $completed = $this->completedBiographies()->first();
        if ($completed) {
            return $completed;
        }

        // Finally most recent
        return $this->biographies()->first();
    }

    /**
     * @Oracode Method: Get Biography Summary
     * 🎯 Purpose: Generate user biography summary for profiles
     * 📤 Returns: Array with biography stats and info
     */
    public function getBiographySummary(): array {
        $primary = $this->getPrimaryBiography();

        return [
            'has_biography' => $this->hasBiography(),
            'has_public' => $this->hasPublicBiography(),
            'total_count' => $this->biographies()->count(),
            'public_count' => $this->publicBiographies()->count(),
            'primary_biography' => $primary,
            'primary_preview' => $primary?->content_preview,
            'estimated_reading_time' => $primary?->getEstimatedReadingTime(),
        ];
    }

    /**
     * @Oracode Spatie: Media Collections Configuration
     * 🎯 Purpose: Define media collections for user profile images
     * 🖼️ Collections: profile_images for multiple profile photos, current_profile for active one
     */
    public function registerMediaCollections(): void {
        $userProfile = $this->addMediaCollection('profile_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $userProfile->singleFile = false;
        $userProfile->collectionSizeLimit = null;

        $this->addMediaCollection('current_profile')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile(true);
    }

    /**
     * @Oracode Spatie: Media Conversions for Performance
     * 🎯 Purpose: Auto-generate optimized image versions for profile photos
     * ⚡ Performance: Thumbnail, avatar, and web-optimized versions
     */
    public function registerMediaConversions(?Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->optimize();

        $this->addMediaConversion('avatar')
            ->fit(300, 300)
            ->sharpen(10)
            ->optimize();

        $this->addMediaConversion('web')
            ->fit(800, 600)
            ->optimize();
    }

    /**
     * @Oracode Method: Get Current Profile Image
     * 🎯 Purpose: Get the currently active profile image using profile_photo_path
     * 📤 Returns: Media model or null
     */
    public function getCurrentProfileImage(): ?Media {
        if (!$this->profile_photo_path) {
            return null;
        }

        // Find media by file_name (which is stored in profile_photo_path)
        return Media::where('model_type', User::class)
            ->where('model_id', $this->id)
            ->where('collection_name', 'profile_images')
            ->where('file_name', $this->profile_photo_path)
            ->first();
    }

    /**
     * @Oracode Method: Get All Profile Images
     * 🎯 Purpose: Get all uploaded profile images
     * 📤 Returns: Collection of Media models
     */
    public function getAllProfileImages() {
        return $this->getMedia('profile_images');
    }

    /**
     * @Oracode Method: Set Current Profile Image
     * 🎯 Purpose: Set a specific image as the current profile photo using profile_photo_path
     * 📤 Returns: Boolean success status
     */
    public function setCurrentProfileImage(Media $media): bool {
        // Update the profile_photo_path field with the media file_name
        $this->update([
            'profile_photo_path' => $media->file_name
        ]);

        return true;
    }

    /**
     * @Oracode Method: Get Profile Photo URL (Override)
     * 🎯 Purpose: Override default profile photo URL to respect privacy based on usertype
     * 📤 Returns: URL string for current profile image or privacy-compliant default
     * 🛡️ Privacy: Only commissioners show real photos, others get generated avatars
     */
    public function getProfilePhotoUrlAttribute(): string {
        // Solo i commissioner possono mostrare la loro foto reale
        if ($this->usertype === 'commissioner') {
            $currentImage = $this->getCurrentProfileImage();

            if ($currentImage) {
                return $currentImage->getUrl('thumb');
            }
        }

        // Per tutti gli altri o se il commissioner non ha foto, usa avatar generato
        return $this->defaultProfilePhotoUrl();
    }

    /**
     * @Oracode Method: Get Default Profile Photo URL
     * 🎯 Purpose: Get DiceBear generated avatar URL based on usertype
     * 📤 Returns: URL string for default avatar
     * 🛡️ Privacy: Uses wallet for non-commissioners, name for commissioners
     */
    public function defaultProfilePhotoUrl(): string {
        // Per i commissioner, usa il nome se disponibile
        if ($this->usertype === 'commissioner' && $this->name) {
            $seed = urlencode($this->name);
        } else {
            // Per tutti gli altri o commissioner senza nome, usa wallet o fallback
            $seed = urlencode($this->wallet ?? $this->name ?? 'Anonymous');
        }

        return "https://api.dicebear.com/7.x/bottts/png?seed={$seed}&backgroundColor=transparent&size=512";
    }

    // ======================== COLLECTOR RELATIONSHIPS ========================

    /**
     * @Oracode Collector: Get all reservations made by this user
     * 🎯 Purpose: Track all EGI reservations/purchases by collector
     * 📤 Returns: HasMany relationship to Reservation model
     */
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    /**
     * @Oracode Collector: Get active reservations
     * 🎯 Purpose: Current active reservations by collector
     * 📤 Returns: HasMany relationship filtered for active reservations
     */
    public function activeReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->where('status', 'active')
            ->where('is_current', true);
    }

    /**
     * @Oracode Collector: Get completed/paid reservations
     * 🎯 Purpose: Successfully completed purchases by collector
     * 📤 Returns: HasMany relationship filtered for completed reservations
     */
    public function completedReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->where('status', 'completed');
    }

    /**
     * @Oracode Collector: Get valid reservations (active or completed)
     * 🎯 Purpose: All valid reservations including active bookings and completed purchases
     * 📤 Returns: HasMany relationship filtered for valid reservations
     */
    public function validReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->whereIn('status', ['active', 'completed']);
    }

    /**
     * @Oracode Collector: Get owned EGIs
     * 🎯 Purpose: EGIs currently owned by this collector
     * 📤 Returns: HasMany relationship to EGI model via owner_id
     */
    public function ownedEgis(): HasMany {
        return $this->hasMany(Egi::class, 'owner_id');
    }

    /**
     * @Oracode Collector: Get published owned EGIs
     * 🎯 Purpose: Only public EGIs owned by collector for portfolio display
     * 📤 Returns: HasMany relationship filtered for published EGIs
     */
    public function publicOwnedEgis(): HasMany {
        return $this->hasMany(Egi::class, 'owner_id')
            ->where('is_published', true);
    }

    /**
     * @Oracode Collector: Get purchased EGIs via completed reservations
     * 🎯 Purpose: EGIs acquired through purchase transactions (valid reservations)
     * 📤 Returns: BelongsToMany relationship via reservations table
     * 🚀 FIX: Only returns EGIs with WINNING (current) reservations
     */
    public function purchasedEgis(): BelongsToMany {
        return $this->belongsToMany(Egi::class, 'reservations', 'user_id', 'egi_id')
            ->wherePivotIn('status', ['active', 'completed'])
            ->wherePivot('is_current', true)
            ->whereNull('reservations.superseded_by_id')
            ->withPivot(['offer_amount_fiat', 'fiat_currency', 'offer_amount_algo', 'exchange_rate', 'created_at', 'id', 'status'])
            ->withTimestamps();
    }

    /**
     * @Oracode Collector: Get published purchased EGIs
     * 🎯 Purpose: Only public EGIs purchased by collector for portfolio display
     * 📤 Returns: BelongsToMany relationship filtered for published purchased EGIs
     */
    public function publicPurchasedEgis(): BelongsToMany {
        return $this->purchasedEgis()
            ->where('egis.is_published', true);
    }

    /**
     * @Oracode Collector: Get EGI collection groups for purchased EGIs
     * 🎯 Purpose: Group purchased EGIs by collection for portfolio organization
     * 📤 Returns: Collection of collections with purchased EGIs
     */
    public function getCollectorCollectionsAttribute() {
        // Get collection IDs from purchased EGIs
        $collectionIds = $this->purchasedEgis()
            ->where('egis.is_published', true)
            ->pluck('collection_id')
            ->unique();

        return Collection::whereIn('id', $collectionIds)
            ->with(['egis' => function ($query) {
                // Only show EGIs that this collector has purchased
                $query->whereHas('reservations', function ($subQuery) {
                    $subQuery->where('user_id', $this->id)
                        ->whereIn('status', ['active', 'completed']);
                })->where('is_published', true);
            }])
            ->get();
    }

    /**
     * @Oracode Collector: Get reservation certificates
     * 🎯 Purpose: All reservation certificates for owned/reserved EGIs
     * 📤 Returns: Collection of certificates through reservations
     */
    public function reservationCertificates() {
        return $this->hasManyThrough(
            EgiReservationCertificate::class,
            Reservation::class,
            'user_id', // Foreign key on reservations table
            'reservation_id', // Foreign key on certificates table
            'id', // Local key on users table
            'id' // Local key on reservations table
        );
    }

    /**
     * @Oracode Collector: Get collectors stats for portfolio
     * 🎯 Purpose: Calculate collector statistics for home page display (based on purchases)
     * 📤 Returns: Array with collector stats
     */
    public function getCollectorStats(): array {
        $purchasedEgis = $this->purchasedEgis()->count();
        $activeReservations = $this->activeReservations()->count();
        $completedPurchases = $this->completedReservations()->count();
        $totalSpent = $this->validReservations()->sum('offer_amount_fiat');

        // Get unique creators supported through purchases
        $creatorsSupported = $this->purchasedEgis()
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->distinct('collections.creator_id')
            ->count('collections.creator_id');

        // Calculate EPP impact based on purchases
        $eppImpact = $purchasedEgis * 0.15; // Mock calculation

        // Count unique collections purchased from
        $totalCollections = $this->purchasedEgis()
            ->distinct('collection_id')
            ->count('collection_id');

        return [
            'total_egis' => $purchasedEgis,
            'total_collections' => $totalCollections,
            'active_reservations' => $activeReservations,
            'completed_purchases' => $completedPurchases,
            'total_spent_eur' => $totalSpent,
            'creators_supported' => $creatorsSupported,
            'epp_impact' => round($eppImpact, 2),
            'animate' => max($purchasedEgis, $completedPurchases) > 5
        ];
    }

    /**
     * @Oracode Collector: Check if user is collector
     * 🎯 Purpose: Quick check for collector role/permissions (must have valid reservations)
     * 📤 Returns: Boolean if user has collector capabilities
     */
    public function isCollector(): bool {
        return $this->hasRole('collector') ||
            $this->validReservations()->exists();
    }
}
