<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Models\BiographyChapter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @Oracode Controller for Biography Chapter Management with GDPR Compliance
 * 🎯 Purpose: Handles CRUD operations for biography chapters with timeline support + GDPR audit
 * 🧱 Core Logic: Chapter creation, ordering, date management within biographies + audit trail
 * 🛡️ GDPR: Complete audit logging, nested ownership validation, privacy-aware operations
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI MVP Biography Chapter + GDPR)
 * @date 2025-07-03
 *
 * @signature [BiographyChapterController::v1.1] florence-chapter-gdpr-compliant
 */
class BiographyChapterController extends Controller
{
    /**
     * @Oracode Ultra Dependencies + GDPR Audit
     */
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    )
    {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * @Oracode Create New Chapter with GDPR Audit
     * 🎯 Purpose: Add chapter to biography with timeline validation and complete audit trail
     * 🧱 Core Logic: Type validation, auto-ordering, date validation + GDPR logging
     * 🛡️ Security: Biography ownership and type compatibility check + audit
     */
    public function store(Request $request, Biography $biography): JsonResponse
    {
        $this->logger->info('Biography chapter creation attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_CHAPTER_CREATE_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'biography_type' => $biography->type,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized access attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_chapter_creation_attempt',
                    [
                        'biography_id' => $biography->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'create_chapter'
                    ],
                    'medium'
                );

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'owner_id' => $biography->user_id,
                    'operation' => 'create'
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Biography must be chapters type
            if ($biography->type !== 'chapters') {
                $this->logger->warning('Invalid chapter creation on single-type biography', [
                    'type' => 'BIOGRAPHY_CHAPTER_VALIDATION_FAILED',
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'biography_type' => $biography->type
                ]);

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_VALIDATION_FAILED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'biography_type' => $biography->type,
                    'validation_reason' => 'wrong_biography_type'
                ], new \InvalidArgumentException('Cannot add chapters to single-type biography'));
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'is_ongoing' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
                'is_published' => 'boolean',
                'chapter_type' => 'string|in:standard,milestone,achievement',
                'formatting_data' => 'nullable|array',
            ]);

            // Validate date logic
            if ($validated['is_ongoing'] ?? false) {
                $validated['date_to'] = null;
            }

            // Create chapter
            $chapter = $biography->chapters()->create($validated);

            // 🔥 GDPR AUDIT LOGGING - Content Creation
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_created',
                [
                    'chapter_id' => $chapter->id,
                    'biography_id' => $biography->id,
                    'chapter_type' => $chapter->chapter_type,
                    'title_length' => strlen($chapter->title),
                    'content_length' => strlen($chapter->content),
                    'is_published' => $chapter->is_published,
                    'date_range' => [
                        'from' => $chapter->date_from?->toDateString(),
                        'to' => $chapter->date_to?->toDateString(),
                        'is_ongoing' => $chapter->is_ongoing
                    ],
                    'processing_purpose' => 'user_biography_chapter_creation'
                ],
                'consent'
            );

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_chapter_created',
                [
                    'entity_type' => 'BiographyChapter',
                    'entity_id' => $chapter->id,
                    'parent_entity_type' => 'Biography',
                    'parent_entity_id' => $biography->id,
                    'chapter_type' => $chapter->chapter_type,
                    'is_published' => $chapter->is_published
                ],
                GdprActivityCategory::CONTENT_CREATION
            );

            $this->logger->info('Biography chapter created successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_CHAPTER_CREATE_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'chapter_title' => $chapter->title,
                'sort_order' => $chapter->sort_order,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $chapter->load(['biography:id,title']),
                'message' => __('biography.chapter_created_successfully'),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_chapter_creation',
                    'parent_biography_tracked' => true
                ]
            ], 201);

        } catch(\Illuminate\Validation\ValidationException $e) {
            // 🔥 GDPR AUDIT - Failed attempt
            $this->auditService->logUserAction(
                auth()->user(),
                'biography_chapter_creation_failed',
                [
                    'biography_id' => $biography->id,
                    'reason' => 'validation_error',
                    'validation_errors' => array_keys($e->errors()),
                    'attempted_type' => $request->get('chapter_type', 'standard')
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_VALIDATION_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'validation_errors' => $e->errors()
            ], $e);

        } catch (\Exception $e) {
            $this->logger->error('Biography chapter creation failed with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_CREATE_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'content_creation_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_CREATE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'attempted_data' => $request->only(['title', 'chapter_type', 'date_from'])
            ], $e);
        }
    }

    /**
     * @Oracode Update Chapter with GDPR Audit
     * 🎯 Purpose: Update chapter with timeline validation and complete audit trail
     * 🧱 Core Logic: Date validation, ownership through parent biography + GDPR logging
     * 🛡️ Security: Nested ownership validation and data consistency + audit
     */
    public function update(Request $request, Biography $biography, BiographyChapter $chapter): JsonResponse
    {
        $this->logger->info('Biography chapter update attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_CHAPTER_UPDATE_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'chapter_id' => $chapter->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Verify chapter belongs to biography
            if ($chapter->biography_id !== $biography->id) {
                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'chapter_id' => $chapter->id,
                    'access_reason' => 'chapter_not_in_biography'
                ], new \InvalidArgumentException('Chapter does not belong to biography'));
            }

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized access attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_chapter_update_attempt',
                    [
                        'biography_id' => $biography->id,
                        'chapter_id' => $chapter->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'update_chapter'
                    ],
                    'medium'
                );

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'chapter_id' => $chapter->id,
                    'owner_id' => $biography->user_id,
                    'operation' => 'update'
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Store original values for GDPR audit
            $originalValues = $chapter->only([
                'title', 'content', 'date_from', 'date_to', 'is_ongoing',
                'is_published', 'chapter_type', 'sort_order'
            ]);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'is_ongoing' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
                'is_published' => 'boolean',
                'chapter_type' => 'string|in:standard,milestone,achievement',
                'formatting_data' => 'nullable|array',
            ]);

            // Handle ongoing logic
            if (($validated['is_ongoing'] ?? false) && isset($validated['date_to'])) {
                $validated['date_to'] = null;
            }

            // Update chapter
            $chapter->update($validated);
            $newValues = $chapter->fresh()->only([
                'title', 'content', 'date_from', 'date_to', 'is_ongoing',
                'is_published', 'chapter_type', 'sort_order'
            ]);

            // 🔥 GDPR AUDIT LOGGING - Content Modification
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_updated',
                [
                    'chapter_id' => $chapter->id,
                    'biography_id' => $biography->id,
                    'changes' => array_diff_assoc($newValues, $originalValues),
                    'updated_fields' => array_keys($validated),
                    'publication_change' => ($originalValues['is_published'] !== $newValues['is_published']),
                    'timeline_change' => (
                        $originalValues['date_from'] !== $newValues['date_from'] ||
                        $originalValues['date_to'] !== $newValues['date_to']
                    ),
                    'processing_purpose' => 'user_biography_chapter_modification'
                ],
                'consent'
            );

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_chapter_updated',
                [
                    'entity_type' => 'BiographyChapter',
                    'entity_id' => $chapter->id,
                    'parent_entity_type' => 'Biography',
                    'parent_entity_id' => $biography->id,
                    'updated_fields' => array_keys($validated),
                    'publication_status_changed' => ($originalValues['is_published'] !== $newValues['is_published'])
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            $this->logger->info('Biography chapter updated successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_CHAPTER_UPDATE_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'updated_fields' => array_keys($validated),
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $chapter->fresh()->load(['biography:id,title']),
                'message' => __('biography.chapter_updated_successfully'),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_chapter_modification',
                    'changes_tracked' => true,
                    'timeline_integrity_maintained' => true
                ]
            ]);

        } catch(\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_VALIDATION_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'validation_errors' => $e->errors()
            ], $e);

        } catch (\Exception $e) {
            $this->logger->error('Biography chapter update failed with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_UPDATE_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'content_modification_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_UPDATE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id
            ], $e);
        }
    }

    /**
     * @Oracode Delete Chapter with GDPR Audit
     * 🎯 Purpose: Safely delete chapter with cascade handling and complete audit trail
     * 🧱 Core Logic: Ownership validation through parent biography + GDPR logging
     * 🛡️ Security: Nested authorization with automatic cleanup + audit
     */
    public function destroy(Biography $biography, BiographyChapter $chapter): JsonResponse
    {
        $this->logger->info('Biography chapter deletion attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_CHAPTER_DELETE_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'chapter_id' => $chapter->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Verify chapter belongs to biography
            if ($chapter->biography_id !== $biography->id) {
                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'chapter_id' => $chapter->id,
                    'access_reason' => 'chapter_not_in_biography'
                ], new \InvalidArgumentException('Chapter does not belong to biography'));
            }

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized deletion attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_chapter_deletion_attempt',
                    [
                        'biography_id' => $biography->id,
                        'chapter_id' => $chapter->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'delete_chapter'
                    ],
                    'high'
                );

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'chapter_id' => $chapter->id,
                    'owner_id' => $biography->user_id,
                    'operation' => 'delete'
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Store info for audit and response
            $chapterTitle = $chapter->title;
            $mediaCount = $chapter->getMedia()->count();
            $chapterData = $chapter->only([
                'id', 'title', 'chapter_type', 'is_published', 'date_from', 'date_to', 'created_at'
            ]);

            // 🔥 GDPR AUDIT LOGGING - Before deletion
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_deleted',
                [
                    'chapter_data' => $chapterData,
                    'biography_id' => $biography->id,
                    'media_count' => $mediaCount,
                    'deletion_reason' => 'user_request',
                    'cascade_deletion' => true,
                    'processing_purpose' => 'user_biography_chapter_deletion'
                ],
                'consent'
            );

            // Delete chapter (cascade will handle media)
            $chapter->delete();

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_chapter_deleted',
                [
                    'entity_type' => 'BiographyChapter',
                    'entity_id' => $chapterData['id'],
                    'parent_entity_type' => 'Biography',
                    'parent_entity_id' => $biography->id,
                    'title' => $chapterTitle,
                    'media_deleted' => $mediaCount
                ],
                GdprActivityCategory::DATA_DELETION
            );

            $this->logger->info('Biography chapter deleted successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_CHAPTER_DELETE_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapter_title' => $chapterTitle,
                'media_deleted' => $mediaCount,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => __('biography.chapter_deleted_successfully', ['title' => $chapterTitle]),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_chapter_deletion',
                    'cascade_deletion_tracked' => true,
                    'media_cleanup_included' => true
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography chapter deletion failed with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_DELETE_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'content_deletion_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_DELETE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id
            ], $e);
        }
    }

    /**
     * @Oracode Reorder Chapters with GDPR Audit
     * 🎯 Purpose: Update chapter ordering within biography with complete audit trail
     * 🧱 Core Logic: Batch update with transaction safety + GDPR logging
     * 🛡️ Security: Ownership validation and data consistency + audit
     */
    public function reorder(Request $request, Biography $biography): JsonResponse
    {
        $this->logger->info('Biography chapters reorder attempt with GDPR audit', [
            'type' => 'BIOGRAPHY_CHAPTER_REORDER_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Ownership check
            if ($biography->user_id !== $user->id) {
                // 🔥 GDPR AUDIT - Unauthorized reorder attempt
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_chapter_reorder_attempt',
                    [
                        'biography_id' => $biography->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'reorder_chapters'
                    ],
                    'low'
                );

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'operation' => 'reorder'
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            $validated = $request->validate([
                'chapters' => 'required|array',
                'chapters.*.id' => 'required|integer|exists:biography_chapters,id',
                'chapters.*.sort_order' => 'required|integer|min:0'
            ]);

            // Store previous order for audit
            $previousOrder = [];
            $newOrder = [];

            DB::transaction(function () use ($biography, $validated, &$previousOrder, &$newOrder) {
                foreach ($validated['chapters'] as $chapterData) {
                    $chapter = BiographyChapter::where('id', $chapterData['id'])
                        ->where('biography_id', $biography->id)
                        ->first();

                    if ($chapter) {
                        $previousOrder[$chapter->id] = $chapter->sort_order;
                        $chapter->update(['sort_order' => $chapterData['sort_order']]);
                        $newOrder[$chapter->id] = $chapterData['sort_order'];
                    }
                }
            });

            // 🔥 GDPR AUDIT LOGGING - Content Organization
            $this->auditService->logGdprAction(
                $user,
                'biography_chapters_reordered',
                [
                    'biography_id' => $biography->id,
                    'chapters_affected' => count($validated['chapters']),
                    'order_changes' => [
                        'previous' => $previousOrder,
                        'new' => $newOrder
                    ],
                    'processing_purpose' => 'user_biography_chapter_organization'
                ],
                'consent'
            );

            // 🔥 USER ACTIVITY LOGGING
            $this->auditService->logUserAction(
                $user,
                'biography_chapters_reordered',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'operation_type' => 'chapter_reorder',
                    'chapters_count' => count($validated['chapters'])
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            $this->logger->info('Biography chapters reordered successfully with GDPR audit', [
                'type' => 'BIOGRAPHY_CHAPTER_REORDER_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapters_count' => count($validated['chapters']),
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => __('biography.chapters_reordered_successfully'),
                'gdpr_compliance' => [
                    'action_logged' => true,
                    'legal_basis' => 'consent',
                    'processing_purpose' => 'user_biography_chapter_organization',
                    'order_changes_tracked' => true
                ]
            ]);

        } catch(\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_VALIDATION_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'validation_errors' => $e->errors()
            ], $e);

        } catch (\Exception $e) {
            $this->logger->error('Biography chapters reorder failed with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_REORDER_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'content_organization_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_REORDER_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ], $e);
        }
    }

    // ===================================================================
    // 🔥 EXISTING METHODS (index, show) - Mantengono la logica originale
    // ===================================================================

    /**
     * @Oracode List Biography Chapters (Original + Enhanced Logging)
     */
    public function index(Request $request, Biography $biography): JsonResponse
    {
        $this->logger->info('Biography chapters listing requested with GDPR context', [
            'type' => 'BIOGRAPHY_CHAPTER_INDEX_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Check biography access (own OR public)
            if ($biography->user_id !== $user->id && !$biography->is_public) {
                // 🔥 GDPR AUDIT - Access denied
                $this->auditService->logSecurityEvent(
                    $user,
                    'unauthorized_chapter_listing_attempt',
                    [
                        'biography_id' => $biography->id,
                        'owner_id' => $biography->user_id,
                        'attempted_action' => 'list_chapters'
                    ],
                    'low'
                );

                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'access_reason' => 'not_owner_and_not_public'
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            $query = $biography->chapters();

            // If not owner, show only published chapters
            if ($biography->user_id !== $user->id) {
                $query->where('is_published', true);
            }

            // Apply ordering
            $orderBy = $request->get('order_by', 'timeline');
            switch ($orderBy) {
                case 'date':
                    $query->orderBy('date_from', 'asc');
                    break;
                case 'manual':
                    $query->orderBy('sort_order', 'asc');
                    break;
                default:
                    $query->orderBy('sort_order', 'asc')->orderBy('date_from', 'asc');
            }

            $chapters = $query->get();

            // 🔥 USER ACTIVITY LOGGING - Data Access
            $this->auditService->logUserAction(
                $user,
                'biography_chapters_viewed',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'chapters_count' => $chapters->count(),
                    'access_type' => $biography->user_id === $user->id ? 'owner' : 'public'
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            $this->logger->info('Biography chapters retrieved successfully with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_INDEX_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapters_count' => $chapters->count(),
                'order_by' => $orderBy,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $chapters,
                'meta' => [
                    'total_chapters' => $chapters->count(),
                    'published_chapters' => $chapters->where('is_published', true)->count(),
                    'order_by' => $orderBy
                ],
                'message' => __('biography.chapters_retrieved_successfully')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve biography chapters with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_INDEX_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'data_access_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_INDEX_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ], $e);
        }
    }

    /**
     * @Oracode Show Single Chapter (Original + Enhanced Logging)
     */
    public function show(Request $request, Biography $biography, BiographyChapter $chapter): JsonResponse
    {
        $this->logger->info('Biography chapter access attempt with GDPR context', [
            'type' => 'BIOGRAPHY_CHAPTER_SHOW_ATTEMPT',
            'user_id' => auth()->id(),
            'biography_id' => $biography->id,
            'chapter_id' => $chapter->id,
            'is_owner' => $biography->user_id === auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Verify chapter belongs to biography
            if ($chapter->biography_id !== $biography->id) {
                return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'chapter_id' => $chapter->id,
                    'access_reason' => 'chapter_not_in_biography'
                ], new \InvalidArgumentException('Chapter does not belong to biography'));
            }

            // Check access permissions
            if ($biography->user_id !== $user->id) {
                if (!$biography->is_public || !$chapter->is_published) {
                    // 🔥 GDPR AUDIT - Access denied
                    $this->auditService->logSecurityEvent(
                        $user,
                        'unauthorized_chapter_access_attempt',
                        [
                            'biography_id' => $biography->id,
                            'chapter_id' => $chapter->id,
                            'owner_id' => $biography->user_id,
                            'attempted_action' => 'view_chapter'
                        ],
                        'low'
                    );

                    return $this->errorManager->handle('BIOGRAPHY_CHAPTER_ACCESS_DENIED', [
                        'user_id' => $user->id,
                        'biography_id' => $biography->id,
                        'chapter_id' => $chapter->id,
                        'access_reason' => 'not_published_or_not_public'
                    ], new \Illuminate\Auth\Access\AuthorizationException());
                }
            }

            // 🔥 USER ACTIVITY LOGGING - Data Access
            $this->auditService->logUserAction(
                $user,
                'biography_chapter_viewed',
                [
                    'entity_type' => 'BiographyChapter',
                    'entity_id' => $chapter->id,
                    'parent_entity_type' => 'Biography',
                    'parent_entity_id' => $biography->id,
                    'access_type' => $biography->user_id === $user->id ? 'owner' : 'public'
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            $this->logger->info('Biography chapter accessed successfully with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_SHOW_SUCCESS',
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'gdpr_logged' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $chapter->load(['biography:id,title,type']),
                'message' => __('biography.chapter_retrieved_successfully')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography chapter access failed with GDPR context', [
                'type' => 'BIOGRAPHY_CHAPTER_SHOW_FAILED',
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id,
                'error' => $e->getMessage(),
                'gdpr_context' => 'data_access_failed'
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_SHOW_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'chapter_id' => $chapter->id
            ], $e);
        }
    }
}
