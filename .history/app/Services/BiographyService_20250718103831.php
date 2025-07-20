<?php

namespace App\Services;

use App\Models\Biography;
use App\Models\BiographyChapter;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Exception;
use InvalidArgumentException;

/**
 * @Oracode Service: Biography Management (API-First)
 * 🎯 Purpose: Centralized business logic for biography CRUD operations
 * 🧱 Core Logic: Handles biography lifecycle, chapters, media, and business validations
 * 🛡️ GDPR: Complete audit logging for all biography operations
 * 📡 API: Designed for RESTful API consumption with proper error handling
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Create or update biography with media handling
     *
     * @param array $data Biography data
     * @param User $user Owner user
     * @param int|null $biographyId For updates
     * @return Biography
     * @throws Exception
     */
    public function createOrUpdate(array $data, User $user, ?int $biographyId = null): Biography
    {
        $this->logger->info('BiographyService: createOrUpdate called', [
            'user_id' => $user->id,
            'biography_id' => $biographyId,
            'type' => $data['type'] ?? null,
            'is_public' => $data['is_public'] ?? false
        ]);

        try {
            DB::beginTransaction();

            // Validate business rules
            $this->validateBiographyBusinessRules($data, $user, $biographyId);

            if ($biographyId) {
                // Update existing biography
                $biography = $this->fetch($biographyId);

                // Ownership check
                if ($biography->user_id !== $user->id) {
                    throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
                }

                // Handle type change validation
                // if (isset($data['type']) && $data['type'] !== $biography->type) {
                //     $this->validateTypeChange($biography, $data['type']);
                // }

                $biography->update($data);
                $action = 'biography_updated';
            } else {
                // Create new biography
                $data['user_id'] = $user->id;
                $biography = Biography::create($data);
                $action = 'biography_created';
            }

            // Handle media uploads if present
            if (isset($data['media'])) {
                $this->handleMediaUpload($biography, $data['media']);
            }

            // // GDPR audit logging
            // $this->auditService->logGdprAction(
            //     $user,
            //     $action,
            //     [
            //         'biography_id' => $biography->id,
            //         'biography_type' => $biography->type,
            //         'is_public' => $biography->is_public,
            //         'title_length' => strlen($biography->title),
            //         'content_length' => strlen($biography->content ?? ''),
            //         'processing_purpose' => 'user_biography_management'
            //     ],
            //     'consent'
            // );

            $this->auditService->logUserAction(
                $user,
                $action,
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biography->id,
                    'biography_type' => $biography->type,
                    'is_public' => $biography->is_public
                ],
                GdprActivityCategory::CONTENT_CREATION
            );

            DB::commit();

            $this->logger->info('BiographyService: createOrUpdate successful', [
                'biography_id' => $biography->id,
                'action' => $action
            ]);

            return $biography->fresh(['chapters', 'media']);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger->error('BiographyService: createOrUpdate failed', [
                'user_id' => $user->id,
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Fetch biography with full relations
     *
     * @param int $id Biography ID
     * @return Biography
     * @throws Exception
     */
    public function fetch(int $id): Biography
    {
        $biography = Biography::withFullRelations()->find($id);

        if (!$biography) {
            throw new InvalidArgumentException('Biography not found');
        }

        return $biography;
    }

    /**
     * Delete biography with cascade handling
     *
     * @param int $id Biography ID
     * @param User $user Owner user
     * @return void
     * @throws Exception
     */
    public function delete(int $id, User $user): void
    {
        $this->logger->info('BiographyService: delete called', [
            'user_id' => $user->id,
            'biography_id' => $id
        ]);

        try {
            $biography = $this->fetch($id);

            // Ownership check
            if ($biography->user_id !== $user->id) {
                throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
            }

            // Store info for audit
            $biographyData = $biography->only(['id', 'type', 'title', 'is_public', 'created_at']);
            $chaptersCount = $biography->chapters()->count();
            $mediaCount = $biography->getMedia()->count();

            // GDPR audit logging before deletion
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

            $this->auditService->logUserAction(
                $user,
                'biography_deleted',
                [
                    'entity_type' => 'Biography',
                    'entity_id' => $biographyData['id'],
                    'title' => $biographyData['title'],
                    'chapters_deleted' => $chaptersCount,
                    'media_deleted' => $mediaCount
                ],
                GdprActivityCategory::GDPR_ACTIONS
            );

            $this->logger->info('BiographyService: delete successful', [
                'biography_id' => $id,
                'chapters_deleted' => $chaptersCount,
                'media_deleted' => $mediaCount
            ]);
        } catch (Exception $e) {
            $this->logger->error('BiographyService: delete failed', [
                'user_id' => $user->id,
                'biography_id' => $id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * List biographies for user
     *
     * @param int $userId User ID
     * @param array $filters Optional filters
     * @return Collection
     */
    public function listForUser(int $userId, array $filters = []): Collection
    {
        $query = Biography::withFullRelations()->where('user_id', $userId);

        // Apply filters
        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['is_completed'])) {
            $query->where('is_completed', $filters['is_completed']);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get user's primary biography (first created or marked as primary)
     *
     * @param int $userId
     * @return Biography|null
     */
    public function getUserPrimaryBiography(int $userId): ?Biography
    {
        $this->logger->info('BiographyService: getting primary biography for user', [
            'user_id' => $userId
        ]);

        try {
            return Biography::where('user_id', $userId)
                ->with(['chapters' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('date_from');
                }, 'media', 'user'])
                ->orderBy('created_at', 'asc')
                ->first();
        } catch (Exception $e) {
            $this->logger->error('BiographyService: failed to get primary biography for user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create chapter for biography
     *
     * @param int $biographyId Biography ID
     * @param array $data Chapter data
     * @param User $user Owner user
     * @return BiographyChapter
     * @throws Exception
     */
    public function createChapter(int $biographyId, array $data, User $user): BiographyChapter
    {
        $this->logger->info('BiographyService: createChapter called', [
            'user_id' => $user->id,
            'biography_id' => $biographyId,
            'chapter_title' => $data['title'] ?? null
        ]);

        try {
            DB::beginTransaction();

            $biography = $this->fetch($biographyId);

            // Ownership check
            if ($biography->user_id !== $user->id) {
                throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
            }

            // Biography must be chapters type
            if ($biography->type !== 'chapters') {
                throw new InvalidArgumentException('Chapters can only be added to biographies of type "chapters"');
            }

            // Validate chapter business rules
            $this->validateChapterBusinessRules($data, $biography);

            // Auto-set sort_order if not provided
            if (!isset($data['sort_order'])) {
                $maxOrder = $biography->chapters()->max('sort_order') ?? 0;
                $data['sort_order'] = $maxOrder + 1;
            }

            $data['biography_id'] = $biographyId;
            $chapter = BiographyChapter::create($data);

            // Handle media uploads if present
            if (isset($data['media'])) {
                $this->handleChapterMediaUpload($chapter, $data['media']);
            }

            // GDPR audit logging
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_created',
                [
                    'biography_id' => $biographyId,
                    'chapter_id' => $chapter->id,
                    'chapter_title' => $chapter->title,
                    'chapter_type' => $chapter->chapter_type,
                    'is_published' => $chapter->is_published,
                    'processing_purpose' => 'user_biography_content_creation'
                ],
                'consent'
            );

            DB::commit();

            $this->logger->info('BiographyService: createChapter successful', [
                'biography_id' => $biographyId,
                'chapter_id' => $chapter->id
            ]);

            return $chapter->fresh(['biography']);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger->error('BiographyService: createChapter failed', [
                'user_id' => $user->id,
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Update chapter
     *
     * @param int $biographyId Biography ID
     * @param int $chapterId Chapter ID
     * @param array $data Chapter data
     * @param User $user Owner user
     * @return BiographyChapter
     * @throws Exception
     */
    public function updateChapter(int $biographyId, int $chapterId, array $data, User $user): BiographyChapter
    {
        $this->logger->info('BiographyService: updateChapter called', [
            'user_id' => $user->id,
            'biography_id' => $biographyId,
            'chapter_id' => $chapterId
        ]);

        try {
            DB::beginTransaction();

            $biography = $this->fetch($biographyId);

            // Ownership check
            if ($biography->user_id !== $user->id) {
                throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
            }

            $chapter = $biography->chapters()->find($chapterId);
            if (!$chapter) {
                throw new InvalidArgumentException('Chapter not found in biography');
            }

            // Store original values for audit
            $originalValues = $chapter->only([
                'title',
                'content',
                'date_from',
                'date_to',
                'is_ongoing',
                'is_published',
                'chapter_type',
                'sort_order'
            ]);

            // Validate chapter business rules
            $this->validateChapterBusinessRules($data, $biography, $chapterId);

            // Handle ongoing logic
            if (($data['is_ongoing'] ?? false) && isset($data['date_to'])) {
                $data['date_to'] = null;
            }

            $chapter->update($data);

            // Handle media uploads if present
            if (isset($data['media'])) {
                $this->handleChapterMediaUpload($chapter, $data['media']);
            }

            // GDPR audit logging
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_updated',
                [
                    'biography_id' => $biographyId,
                    'chapter_id' => $chapterId,
                    'changes' => array_diff_assoc($chapter->fresh()->only([
                        'title',
                        'content',
                        'date_from',
                        'date_to',
                        'is_ongoing',
                        'is_published',
                        'chapter_type',
                        'sort_order'
                    ]), $originalValues),
                    'processing_purpose' => 'user_biography_content_modification'
                ],
                'consent'
            );

            DB::commit();

            $this->logger->info('BiographyService: updateChapter successful', [
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId
            ]);

            return $chapter->fresh(['biography']);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger->error('BiographyService: updateChapter failed', [
                'user_id' => $user->id,
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Delete chapter
     *
     * @param int $biographyId Biography ID
     * @param int $chapterId Chapter ID
     * @param User $user Owner user
     * @return void
     * @throws Exception
     */
    public function deleteChapter(int $biographyId, int $chapterId, User $user): void
    {
        $this->logger->info('BiographyService: deleteChapter called', [
            'user_id' => $user->id,
            'biography_id' => $biographyId,
            'chapter_id' => $chapterId
        ]);

        try {
            $biography = $this->fetch($biographyId);

            // Ownership check
            if ($biography->user_id !== $user->id) {
                throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
            }

            $chapter = $biography->chapters()->find($chapterId);
            if (!$chapter) {
                throw new InvalidArgumentException('Chapter not found in biography');
            }

            // Store info for audit
            $chapterData = $chapter->only(['id', 'title', 'chapter_type', 'is_published', 'created_at']);
            $mediaCount = $chapter->getMedia()->count();

            // GDPR audit logging before deletion
            $this->auditService->logGdprAction(
                $user,
                'biography_chapter_deleted',
                [
                    'biography_id' => $biographyId,
                    'chapter_data' => $chapterData,
                    'media_count' => $mediaCount,
                    'deletion_reason' => 'user_request',
                    'processing_purpose' => 'user_biography_content_deletion'
                ],
                'consent'
            );

            // Delete chapter (cascade will handle media)
            $chapter->delete();

            $this->logger->info('BiographyService: deleteChapter successful', [
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'media_deleted' => $mediaCount
            ]);
        } catch (Exception $e) {
            $this->logger->error('BiographyService: deleteChapter failed', [
                'user_id' => $user->id,
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Reorder chapters
     *
     * @param int $biographyId Biography ID
     * @param array $order Array of chapter IDs in desired order
     * @param User $user Owner user
     * @return void
     * @throws Exception
     */
    public function reorderChapters(int $biographyId, array $order, User $user): void
    {
        $this->logger->info('BiographyService: reorderChapters called', [
            'user_id' => $user->id,
            'biography_id' => $biographyId,
            'order_count' => count($order)
        ]);

        try {
            DB::beginTransaction();

            $biography = $this->fetch($biographyId);

            // Ownership check
            if ($biography->user_id !== $user->id) {
                throw new InvalidArgumentException('Unauthorized: Biography does not belong to user');
            }

            // Validate all chapters belong to this biography
            $chapterIds = $biography->chapters()->pluck('id')->toArray();
            $invalidIds = array_diff($order, $chapterIds);

            if (!empty($invalidIds)) {
                throw new InvalidArgumentException('Invalid chapter IDs provided for reordering');
            }

            // Store previous order for audit
            $previousOrder = $biography->chapters()
                ->orderBy('sort_order')
                ->pluck('id')
                ->toArray();

            // Update sort_order for each chapter
            foreach ($order as $index => $chapterId) {
                $biography->chapters()
                    ->where('id', $chapterId)
                    ->update(['sort_order' => $index + 1]);
            }

            // GDPR audit logging
            $this->auditService->logGdprAction(
                $user,
                'biography_chapters_reordered',
                [
                    'biography_id' => $biographyId,
                    'previous_order' => $previousOrder,
                    'new_order' => $order,
                    'processing_purpose' => 'user_biography_content_organization'
                ],
                'consent'
            );

            DB::commit();

            $this->logger->info('BiographyService: reorderChapters successful', [
                'biography_id' => $biographyId,
                'chapters_reordered' => count($order)
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $this->logger->error('BiographyService: reorderChapters failed', [
                'user_id' => $user->id,
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    // ===================================================================
    // PRIVATE HELPER METHODS
    // ===================================================================

    /**
     * Validate biography business rules
     */
    private function validateBiographyBusinessRules(array $data, User $user, ?int $biographyId = null): void
    {
        // Type validation
        if (isset($data['type']) && !in_array($data['type'], ['single', 'chapters'])) {
            throw new InvalidArgumentException('Invalid biography type. Must be "single" or "chapters"');
        }

        // Content validation for single type
        if (($data['type'] ?? null) === 'single' && empty($data['content'])) {
            throw new InvalidArgumentException('Content is required for single-type biographies');
        }

        // Public consent check
        if (($data['is_public'] ?? false) && !$this->hasValidPublicBiographyConsent($user)) {
            throw new InvalidArgumentException('Public biography consent required');
        }
    }

    /**
     * Validate chapter business rules
     */
    private function validateChapterBusinessRules(array $data, Biography $biography, ?int $chapterId = null): void
    {
        // Date validation
        if (isset($data['date_from']) && isset($data['date_to'])) {
            if ($data['date_to'] < $data['date_from']) {
                throw new InvalidArgumentException('End date must be after or equal to start date');
            }
        }

        // Ongoing logic validation
        if (($data['is_ongoing'] ?? false) && isset($data['date_to'])) {
            throw new InvalidArgumentException('Ongoing chapters cannot have an end date');
        }

        // Chapter type validation
        if (isset($data['chapter_type']) && !in_array($data['chapter_type'], ['standard', 'milestone', 'achievement'])) {
            throw new InvalidArgumentException('Invalid chapter type');
        }
    }

    /**
     * Validate type change
     */
    private function validateTypeChange(Biography $biography, string $newType): void
    {
        if ($newType === 'single' && $biography->chapters()->exists()) {
            throw new InvalidArgumentException('Cannot change to single type with existing chapters');
        }
    }

    /**
     * Handle media upload for biography
     */
    private function handleMediaUpload(Biography $biography, array $mediaData): void
    {
        if (
            isset($mediaData['featured_image']) &&
            $mediaData['featured_image'] instanceof \Illuminate\Http\UploadedFile &&
            $mediaData['featured_image']->isValid()
        ) {
            $biography->clearMediaCollection('featured_image');
            $biography->addMedia($mediaData['featured_image'])
                ->toMediaCollection('featured_image');
        }
        // In futuro: gestione gallery immagini
        // if (isset($mediaData['gallery'])) { ... }
    }

    /**
     * Handle media upload for chapter
     */
    private function handleChapterMediaUpload(BiographyChapter $chapter, array $mediaData): void
    {
        // Implementation depends on your media upload strategy
        // This is a placeholder for the actual media handling logic
        if (isset($mediaData['chapter_images'])) {
            // Handle chapter images upload
        }
    }

    /**
     * Check public biography consent
     */
    private function hasValidPublicBiographyConsent(User $user): bool
    {
        // Integration with existing GDPR consent system
        return $user->hasActiveConsent('public_biography_sharing') ||
            $user->hasActiveConsent('allow_personal_data_processing');
    }
}
