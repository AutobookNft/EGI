<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService; // 🔥 NUOVA INTEGRAZIONE
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @Oracode Controller for Biography Management with GDPR Compliance
 * 🎯 Purpose: Handles CRUD operations for user biographies with Ultra ecosystem + GDPR audit
 * 🧱 Core Logic: Biography creation, editing, media management with full audit trail
 * 🛡️ GDPR: Complete audit logging, user ownership validation, privacy-aware operations
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI MVP Biography + GDPR)
 * @date 2025-07-03
 *
 * @signature [BiographyController::v1.1] florence-biography-gdpr-compliant
 */
class BiographyController extends Controller
{
    /**
     * @Oracode Ultra Dependencies + GDPR Audit
     */
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService; // 🔥 GDPR AUDIT INTEGRATION

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService // 🔥 DEPENDENCY INJECTION
    )
    {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * @Oracode Create New Biography with GDPR Audit
     * 🎯 Purpose: Create biography with validation, auto-setup and complete audit trail
     * 🧱 Core Logic: Type validation, user ownership, default settings + GDPR logging
     * 🛡️ Security: Input validation, user association, audit logging for data collection
     */
    public function store(Request $request): JsonResponse
    {
        $this->logger->info('Biography creation attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_CREATE_ATTEMPT',
            'user_id' => auth()->user()->id,
            'biography_type' => $request->get('type'),
            'is_public' => $request->get('is_public', false)
        ]);

        try {
            $validated = $request->validate([
                'type' => 'required|in:single,chapters',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string|required_if:type,single',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'is_completed' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            $user = auth()->user();

            // 🔥 GDPR CHECK: Verify consent for public biographies
            if (($validated['is_public'] ?? false) && !$this->hasValidPublicBiographyConsent($user)) {
                $this->logger->warning('Biography creation blocked - missing public consent', [
                    'type' => 'BIOGRAPHY_CONSENT_REQUIRED',
                    'user_id' => $user->id,
                    'requested_public' => true
                ]);

                return $this->errorManager->handle('BIOGRAPHY_PUBLIC_CONSENT_REQUIRED', [
                    'user_id' => $user->id,
                    'consent_required' => 'public_biography_sharing'
                ], new \InvalidArgumentException('Public biography consent required'));
            }

            // Create biography with user association
            $biography = $user->biographies()->create($validated);

            // 🔥 GDPR AUDIT LOGGING - Data Collection
            $this->auditService->logGdprAction(
                $user,
                'biography_created',
                [
                    'biography_id' => $biography->id,
                    'biography_type' => $biography->type,
                    'is_public' => $biography->is_public,
                    'title_length' => strlen($biography->title),
                    'content_length' => strlen($biography->content ?? ''),
                    'processing_purpose' => 'user_biography_creation'
                ],
                'consent' // Legal basis: user explicitly created content
            );

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_created',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'biography_type' => $biography->type,
                    'is_public' => $biography->is_public
                ],
                GdprActivityCategory::CONTENT_CREATION
            );

            $this->logger->info('Biography created successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_CREATE_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'biography_type' => $biography->type,
                'title' => $biography->title,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $biography,
                'message' => __('biography.biography_created_successfully'),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_creation'
                ]
            ], 201);

        } catch(\Illuminate\Validation\ValidationException $e) {
            // 🔥 GDPR AUDIT - Failed attempt
            $this->auditService->logUserAction(
                auth()->user(),
                'biography_creation_failed',
                [
                    'reason' => 'validation_error',
                    'validation_errors' => array_keys($e->errors()),
                    'attempted_type' => $request->get('type')
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return $this->errorManager->handle('BIOGRAPHY_VALIDATION_FAILED', [
                'user_id' => auth()->id(),
                'validation_errors' => $e->errors(),
                'attempted_type' => $request->get('type')
            ], $e);

        } catch (\Exception $e) {
            $this->logger->error('Biography creation failed with GDPR context', [
                'type' => 'BIOGRAPHY_CREATE_FAILED',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'gdpr_context' => 'data_collection_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CREATE_FAILED', [
                'user_id' => auth()->id(),
                'attempted_data' => $request->only(['type', 'title', 'is_public'])
            ], $e);
        }
    }

    /**
     * @Oracode Update Biography with GDPR Audit
     * 🎯 Purpose: Update biography with ownership validation and complete audit trail
     * 🧱 Core Logic: Owner-only updates with type change validation + GDPR logging
     * 🛡️ Security: Ownership check, business rules validation, data modification audit
     */
    public function update(Request $request, Biography $biography): JsonResponse
    {
        $this->logger->info('Biography update attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_UPDATE_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized access attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_biography_access',
                    [
                        'biography_id' => $biography->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'update'
                    ],
                    'medium'
                );

                return $this->errorManager->handle('BIOGRAPHY_UPDATE_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'owner_id' => $biography->user_id
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Store original values for GDPR audit
            $originalValues = $biography->only(['type', 'title', 'content', 'excerpt', 'is_public', 'is_completed']);

            $validated = $request->validate([
                'type' => 'sometimes|in:single,chapters',
                'title' => 'sometimes|string|max:255',
                'content' => 'nullable|string',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'is_completed' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            // 🔥 GDPR CHECK: Verify consent for making biography public
            if (isset($validated['is_public']) && $validated['is_public'] && !$biography->is_public) {
                if (!$this->hasValidPublicBiographyConsent($user)) {
                    return $this->errorManager->handle('BIOGRAPHY_PUBLIC_CONSENT_REQUIRED', [
                        'user_id' => $user->id,
                        'biography_id' => $biography->id,
                        'action' => 'make_public'
                    ], new \InvalidArgumentException('Public biography consent required'));
                }
            }

            // Handle type change validation
            if (isset($validated['type']) && $validated['type'] !== $biography->type) {
                if ($validated['type'] === 'single' && $biography->chapters()->exists()) {
                    return $this->errorManager->handle('BIOGRAPHY_TYPE_CHANGE_INVALID', [
                        'user_id' => $user->id,
                        'biography_id' => $biography->id,
                        'from_type' => $biography->type,
                        'to_type' => $validated['type'],
                        'chapters_count' => $biography->chapters()->count()
                    ], new \InvalidArgumentException('Cannot change to single type with existing chapters'));
                }
            }

            // Update biography
            $biography->update($validated);
            $newValues = $biography->fresh()->only(['type', 'title', 'content', 'excerpt', 'is_public', 'is_completed']);

            // 🔥 GDPR AUDIT LOGGING - Data Modification
            $this->auditService->logGdprAction(
                $user,
                'biography_updated',
                [
                    'biography_id' => $biography->id,
                    'changes' => array_diff_assoc($newValues, $originalValues),
                    'updated_fields' => array_keys($validated),
                    'privacy_change' => ($originalValues['is_public'] !== $newValues['is_public']),
                    'processing_purpose' => 'user_biography_modification'
                ],
                'consent'
            );

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_updated',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'updated_fields' => array_keys($validated),
                    'privacy_level_changed' => ($originalValues['is_public'] !== $newValues['is_public'])
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            $this->logger->info('Biography updated successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_UPDATE_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'updated_fields' => array_keys($validated),
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $biography->fresh(),
                'message' => __('biography.biography_updated_successfully'),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_modification',
                    'changes_tracked' => true
                ]
            ]);

        } catch(\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle('BIOGRAPHY_VALIDATION_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'validation_errors' => $e->errors()
            ], $e);

        } catch (\Exception $e) {
            $this->logger->error('Biography update failed with GDPR context', [
                'type' => 'BIOGRAPHY_UPDATE_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'data_modification_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_UPDATE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ], $e);
        }
    }

    /**
     * @Oracode Delete Biography with GDPR Audit
     * 🎯 Purpose: Safely delete biography with cascade handling and complete audit trail
     * 🧱 Core Logic: Ownership validation, cascade deletion info + GDPR logging
     * 🛡️ Security: Owner-only deletion with confirmation, data erasure audit
     */
    public function destroy(Biography $biography): JsonResponse
    {
        $this->logger->info('Biography deletion attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_DELETE_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'is_owner' => $biography->user_id === auth()->id(),
            'chapters_count' => $biography->chapters()->count()
        ]);

        try {
            $user = auth()->user();

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized deletion attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_biography_deletion',
                    [
                        'biography_id' => $biography->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'delete'
                    ],
                    'high' // Higher severity for deletion attempts
                );

                return $this->errorManager->handle('BIOGRAPHY_DELETE_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'owner_id' => $biography->user_id
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Store info for audit and response
            $biographyTitle = $biography->title;
            $chaptersCount = $biography->chapters()->count();
            $mediaCount = $biography->getMedia()->count();
            $biographyData = $biography->only(['id', 'type', 'title', 'is_public', 'created_at']);

            // 🔥 GDPR AUDIT LOGGING - Before deletion
            $this->auditService->logGdprAction(
                $user,
                'biography_deleted',
                [
                    'biography_data' => $biographyData,
                    'chapters_count' => $chaptersCount,
                    'media_count' => $mediaCount,
                    'deletion_reason' => 'user_request',
                    'cascade_deletion' => true,
                    'processing_purpose' => 'user_biography_deletion'
                ],
                'consent'
            );

            // Delete (cascade will handle chapters and media)
            $biography->delete();

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_deleted',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biographyData['id'],
                    'title' => $biographyTitle,
                    'chapters_deleted' => $chaptersCount,
                    'media_deleted' => $mediaCount
                ],
                GdprActivityCategory::DATA_DELETION
            );

            $this->logger->info('Biography deleted successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_DELETE_SUCCESS',
                'user_id' => $user->id,
                'biography_title' => $biographyTitle,
                'chapters_deleted' => $chaptersCount,
                'media_deleted' => $mediaCount,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => __('biography.biography_deleted_successfully', [
                    'title' => $biographyTitle,
                    'chapters_count' => $chaptersCount
                ]),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_deletion',
                    'cascade_deletion_tracked' => true,
                    'media_cleanup_included' => true
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography deletion failed with GDPR context', [
                'type' => 'BIOGRAPHY_DELETE_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'data_deletion_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_DELETE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ], $e);
        }
    }

    // ===================================================================
    // 🔥 GDPR HELPER METHODS
    // ===================================================================

    /**
     * @Oracode Helper: Check Public Biography Consent
     * 🎯 Purpose: Verify user has valid consent for public biography sharing
     * 🛡️ GDPR: Validates consent before allowing public biography creation/update
     */
    private function hasValidPublicBiographyConsent(User $user): bool
    {
        // Integration with existing GDPR consent system
        return $user->hasActiveConsent('public_biography_sharing') ||
               $user->hasActiveConsent('allow_personal_data_processing');
    }
}