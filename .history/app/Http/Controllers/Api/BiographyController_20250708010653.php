<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiographyRequest;
use App\Http\Resources\BiographyResource;
use App\Models\Biography;
use App\Services\BiographyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Biography API Management (API-First)
 * 🎯 Purpose: RESTful API endpoints for biography CRUD operations
 * 🧱 Core Logic: Delegates to BiographyService, handles HTTP concerns
 * 🛡️ Security: Sanctum authentication, request validation
 * 📡 API: Consistent JSON responses with proper error handling
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyController extends Controller
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
     * Create or update biography
     *
     * @param BiographyRequest $request
     * @return JsonResponse
     */
    public function save(BiographyRequest $request): JsonResponse
    {
        $this->logger->info('Biography API: save called', [
            'user_id' => auth()->id(),
            'type' => $request->input('type'),
            'is_public' => $request->input('is_public', false)
        ]);

        try {
            $user = auth()->user();
            $biographyId = $request->input('id'); // For updates

            $biography = $this->biographyService->createOrUpdate(
                $request->validated(),
                $user,
                $biographyId
            );

            return response()->json([
                'success' => true,
                'data' => new BiographyResource($biography),
                'message' => $biographyId ? 'Biografia aggiornata con successo' : 'Biografia creata con successo'
            ], $biographyId ? 200 : 201);

        } catch (\Exception $e) {
            $this->logger->error('Biography API: save failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_SAVE_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Fetch single biography
     *
     * @param int $id
     * @return JsonResponse
     */
    public function fetch(int $id): JsonResponse
    {
        $this->logger->info('Biography API: fetch called', [
            'user_id' => auth()->id(),
            'biography_id' => $id
        ]);

        try {
            $biography = $this->biographyService->fetch($id);
            $user = auth()->user();

            // Check access permissions
            if ($biography->user_id !== $user->id && !$biography->is_public) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biografia non trovata o non accessibile'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new BiographyResource($biography)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography API: fetch failed', [
                'user_id' => auth()->id(),
                'biography_id' => $id,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_FETCH_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete biography
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->logger->info('Biography API: delete called', [
            'user_id' => auth()->id(),
            'biography_id' => $id
        ]);

        try {
            $user = auth()->user();
            $this->biographyService->delete($id, $user);

            return response()->json([
                'success' => true,
                'message' => 'Biografia eliminata con successo'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography API: delete failed', [
                'user_id' => auth()->id(),
                'biography_id' => $id,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_DELETE_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * List biographies for user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $this->logger->info('Biography API: list called', [
            'user_id' => auth()->id(),
            'filters' => $request->only(['type', 'is_public', 'is_completed'])
        ]);

        try {
            $user = auth()->user();
            $filters = $request->only(['type', 'is_public', 'is_completed']);

            $biographies = $this->biographyService->listForUser($user->id, $filters);

            return response()->json([
                'success' => true,
                'data' => BiographyResource::collection($biographies),
                'meta' => [
                    'total' => $biographies->count(),
                    'filters' => $filters
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Biography API: list failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_LIST_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
