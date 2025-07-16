<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Models\User;
use App\Services\BiographyService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Biography Web Interface (FlorenceEGI Brand Compliant)
 * 🎯 Purpose: Web interface for biography management and viewing
 * 🧱 Core Logic: User-friendly biography CRUD with rich editor integration
 * 🛡️ Security: User ownership validation and public access control
 * 🎨 Brand: FlorenceEGI design system integration
 *
 * @package App\Http\Controllers\Web
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography Web Integration)
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
     * Biography management interface
     *
     * @return View
     */
    public function manage(): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: manage interface accessed', [
            'user_id' => auth()->id()
        ]);

        try {
            $user = auth()->user();
            $biographies = $this->biographyService->listForUser($user->id);

            return view('biography.manage', [
                'biographies' => $biographies,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: manage interface failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_MANAGE_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * View own biography
     *
     * @return View
     */
    public function viewOwn(): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: view own biography', [
            'user_id' => auth()->id()
        ]);

        try {
            $user = auth()->user();
            $biography = $this->biographyService->getUserPrimaryBiography($user->id);

            return view('biography.view', [
                'biography' => $biography,
                'user' => $user,
                'isOwn' => true
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: view own biography failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_VIEW_OWN_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Show public biography
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: show public biography', [
            'user_slug' => $user->slug,
            'viewer_id' => auth()->id()
        ]);

        try {
            $biography = $this->biographyService->getUserPrimaryBiography($user->id);

            // Check if biography is public or viewer is owner
            if (!$biography || (!$biography->is_public && auth()->id() !== $user->id)) {
                abort(404, 'Biografia non trovata o non accessibile');
            }

            return view('biography.show', [
                'biography' => $biography,
                'user' => $user,
                'isOwn' => auth()->id() === $user->id
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: show public biography failed', [
                'user_slug' => $user->slug,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_SHOW_FAILED', [
                'user_slug' => $user->slug,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    public function store(Request $request)
    {
        // $this->authorize('manage_bio_profile');

        $validated = $request->validate([
            'id' => 'nullable|exists:biographies,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:single,chapters',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:4096',
        ]);

        $user = auth()->user();
        $media = [];
        if ($request->hasFile('featured_image')) {
            $media['featured_image'] = $request->file('featured_image');
        }

        $data = array_merge($validated, ['media' => $media]);

        $this->biographyService->createOrUpdate($data, $user, $request->input('id'));

        return redirect()->route('biography.manage')->with('success', __('biography.biography_saved_successfully'));
    }

    /**
     * Upload media files for biography
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedia(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->logger->info('Biography web: upload media', [
            'user_id' => auth()->id(),
            'type' => $request->input('type')
        ]);

        try {
            $request->validate([
                'type' => 'required|in:featured,gallery',
                'file' => 'required|image|mimes:jpeg,png,webp,jpg|max:2048', // 2MB max
            ]);

            $file = $request->file('file');
            $type = $request->input('type');

            // Generate unique filename
            $filename = time() . '_' . $type . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('biography-media', $filename, 'public');

            // Get file info
            $fileInfo = [
                'filename' => $filename,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'size' => $file->getSize(),
                'type' => $type,
                'mime' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];

            $this->logger->info('Biography web: media uploaded successfully', [
                'user_id' => auth()->id(),
                'file_info' => $fileInfo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File caricato con successo',
                'file' => $fileInfo
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography web: media upload validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: media upload failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'upload: ' . $e->getMessage()
            ], 500);
        }
    }
}
