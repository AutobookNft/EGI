<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChapterRequest;
use App\Http\Requests\ReorderRequest;
use App\Http\Resources\BiographyChapterResource;
use App\Models\Biography;
use App\Services\BiographyService;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\Request;

/**
 * @Oracode Controller: Biography Chapter API Management (API-First)
 * 🎯 Purpose: RESTful API endpoints for biography chapter CRUD operations
 * 🧱 Core Logic: Delegates to BiographyService, handles HTTP concerns
 * 🛡️ Security: Sanctum authentication, request validation
 * 📡 API: Consistent JSON responses with proper error handling
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyChapterController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private BiographyService $biographyService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        BiographyService $biographyService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->biographyService = $biographyService;
    }

    /**
     * Create chapter for biography
     *
     * @param ChapterRequest $request
     * @param int $biographyId
     * @return JsonResponse
     */
    public function store(ChapterRequest $request, int $biographyId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: store called', [
            'user_id' => auth()->id(),
            'biography_id' => $biographyId,
            'chapter_title' => $request->input('title')
        ]);

        try {
            $user = auth()->user();
            $chapter = $this->biographyService->createChapter(
                $biographyId,
                $request->validated(),
                $user
            );

            return response()->json([
                'success' => true,
                'data' => new BiographyChapterResource($chapter),
                'message' => 'Capitolo creato con successo'
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: store failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_CREATE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Update chapter
     *
     * @param ChapterRequest $request
     * @param int $biographyId
     * @param int $chapterId
     * @return JsonResponse
     */
    public function update(ChapterRequest $request, int $biographyId, int $chapterId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: update called', [
            'user_id' => auth()->id(),
            'biography_id' => $biographyId,
            'chapter_id' => $chapterId
        ]);

        try {
            $user = auth()->user();
            $chapter = $this->biographyService->updateChapter(
                $biographyId,
                $chapterId,
                $request->validated(),
                $user
            );

            return response()->json([
                'success' => true,
                'data' => new BiographyChapterResource($chapter),
                'message' => 'Capitolo aggiornato con successo'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: update failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_UPDATE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete chapter
     *
     * @param int $biographyId
     * @param int $chapterId
     * @return JsonResponse
     */
    public function destroy(int $biographyId, int $chapterId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: destroy called', [
            'user_id' => auth()->id(),
            'biography_id' => $biographyId,
            'chapter_id' => $chapterId
        ]);

        try {
            $user = auth()->user();
            $this->biographyService->deleteChapter($biographyId, $chapterId, $user);

            return response()->json([
                'success' => true,
                'message' => 'Capitolo eliminato con successo'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: destroy failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_DELETE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Reorder chapters
     *
     * @param ReorderRequest $request
     * @param int $biographyId
     * @return JsonResponse
     */
    public function reorder(ReorderRequest $request, int $biographyId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: reorder called', [
            'user_id' => auth()->id(),
            'biography_id' => $biographyId,
            'chapters_count' => count($request->input('chapters', []))
        ]);

        try {
            $user = auth()->user();
            $this->biographyService->reorderChapters(
                $biographyId,
                $request->input('chapters'),
                $user
            );

            return response()->json([
                'success' => true,
                'message' => 'Ordine dei capitoli aggiornato con successo'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: reorder failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CHAPTER_REORDER_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biographyId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Upload immagini per un capitolo (multi-upload)
     *
     * @param Request $request
     * @param int $chapterId
     * @return JsonResponse
     */
    public function uploadMedia(Request $request, int $chapterId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: upload media', [
            'user_id' => auth()->id(),
            'chapter_id' => $chapterId
        ]);

        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,webp|max:4096',
            ]);

            $user = auth()->user();
            $chapter = \App\Models\BiographyChapter::findOrFail($chapterId);
            $biography = $chapter->biography;

            // Ownership check
            if ($biography->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }

            $mediaItems = [];
            foreach ($request->file('images') as $file) {
                $media = $chapter->addMedia($file)
                    ->toMediaCollection('chapter_images');
                $mediaItems[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                ];
            }

            $this->logger->info('Biography Chapter API: media uploaded', [
                'user_id' => $user->id,
                'chapter_id' => $chapterId,
                'media_count' => count($mediaItems)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Immagini caricate con successo',
                'media' => $mediaItems
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography Chapter API: upload media validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: upload media failed', [
                'user_id' => auth()->id(),
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rimuovi una immagine da un capitolo
     *
     * @param Request $request
     * @param int $chapterId
     * @return JsonResponse
     */
    public function removeMedia(Request $request, int $chapterId): JsonResponse
    {
        $this->logger->info('Biography Chapter API: remove media', [
            'user_id' => auth()->id(),
            'chapter_id' => $chapterId,
            'media_id' => $request->input('media_id')
        ]);

        try {
            $request->validate([
                'media_id' => 'required|exists:media,id',
            ]);

            $user = auth()->user();
            $chapter = \App\Models\BiographyChapter::findOrFail($chapterId);
            $biography = $chapter->biography;

            // Ownership check
            if ($biography->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }

            $media = $chapter->media()->where('id', $request->input('media_id'))->firstOrFail();
            $media->delete();

            $this->logger->info('Biography Chapter API: media removed', [
                'user_id' => $user->id,
                'chapter_id' => $chapterId,
                'media_id' => $media->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Immagine rimossa con successo'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography Chapter API: remove media validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Biography Chapter API: remove media failed', [
                'user_id' => auth()->id(),
                'chapter_id' => $chapterId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la rimozione: ' . $e->getMessage()
            ], 500);
        }
    }
}
