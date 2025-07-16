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
     * Show form to create new biography
     *
     * @return View
     */
    public function create(): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: create form accessed', [
            'user_id' => auth()->id()
        ]);

        dd('create');

        try {
            $user = auth()->user();

            return view('biography.create', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: create form failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_CREATE_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Show form to edit existing biography
     *
     * @param Biography $biography
     * @return View
     */
    public function edit(Biography $biography): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: edit form accessed', [
            'user_id' => auth()->id(),
            'biography_id' => $biography->id
        ]);

        try {
            $user = auth()->user();

            // Check ownership
            if ($biography->user_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questa biografia');
            }

            return view('biography.edit', [
                'biography' => $biography,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: edit form failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('BIOGRAPHY_EDIT_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
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

    /**
     * Store a new biography
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $existing = \App\Models\Biography::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->route('biography.edit', $existing->id)
                ->with('error', 'Hai già una biografia. Puoi solo modificarla.');
        }
        $this->logger->info('Biography web: store biography', [
            'user_id' => auth()->id()
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:single,chapters',
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:4096',
            ]);

            $user = auth()->user();
            $media = [];
            if ($request->hasFile('featured_image')) {
                $media['featured_image'] = $request->file('featured_image');
            }

            $data = array_merge($validated, ['media' => $media]);

            $biography = $this->biographyService->createOrUpdate($data, $user);

            $this->logger->info('Biography web: biography created successfully', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ]);

            return redirect()->route('biography.edit', $biography)
                ->with('success', 'Biografia creata con successo!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography web: store validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $this->logger->error('Biography web: store failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withError('Errore durante la creazione della biografia: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update an existing biography
     *
     * @param Request $request
     * @param Biography $biography
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Biography $biography): \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: update biography', [
            'user_id' => auth()->id(),
            'biography_id' => $biography->id
        ]);

        try {
            $user = auth()->user();

            // Check ownership
            if ($biography->user_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questa biografia');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:single,chapters',
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:4096',
            ]);

            $media = [];
            if ($request->hasFile('featured_image')) {
                $media['featured_image'] = $request->file('featured_image');
            }

            $data = array_merge($validated, ['media' => $media]);

            $this->biographyService->createOrUpdate($data, $user, $biography->id);

            $this->logger->info('Biography web: biography updated successfully', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id
            ]);

            return redirect()->route('biography.edit', $biography)
                ->with('success', 'Biografia aggiornata con successo!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography web: update validation failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'errors' => $e->errors()
            ]);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $this->logger->error('Biography web: update failed', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
                'error' => $e->getMessage()
            ]);

            return back()->withError('Errore durante l\'aggiornamento della biografia: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Set user avatar from biography image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAvatar(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->logger->info('Biography web: set avatar', [
            'user_id' => auth()->id(),
            'image_url' => $request->input('image_url')
        ]);

        try {
            $request->validate([
                'image_url' => 'required|string|url',
            ]);

            $user = auth()->user();
            $user->avatar_url = $request->input('image_url');
            $user->save();

            $this->logger->info('Biography web: avatar updated successfully', [
                'user_id' => auth()->id(),
                'avatar_url' => $user->avatar_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar aggiornato con successo',
                'avatar_url' => $user->avatar_url
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography web: set avatar validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: set avatar failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento dell\'avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload media files for biography using Spatie Media Library
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedia(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->logger->info('Biography web: upload media with Spatie', [
            'user_id' => auth()->id(),
            'collection' => $request->input('collection'),
            'biography_id' => $request->input('biography_id')
        ]);

        try {
            $request->validate([
                'collection' => 'required|in:featured_image,main_gallery',
                'file' => 'required|image|mimes:jpeg,png,webp,jpg|max:2048', // 2MB max
                'biography_id' => 'nullable|exists:biographies,id',
            ]);

            $user = auth()->user();
            $file = $request->file('file');
            $collection = $request->input('collection');
            $biographyId = $request->input('biography_id');

            // Se non esiste una biografia, creane una bozza
            if (!$biographyId) {
                $biography = Biography::create([
                    'user_id' => $user->id,
                    'title' => 'Bozza - ' . now()->format('d/m/Y H:i'),
                    'type' => 'single',
                    'content' => '',
                    'is_public' => false,
                    'is_completed' => false,
                    'settings' => []
                ]);
                $biographyId = $biography->id;
            } else {
                $biography = Biography::findOrFail($biographyId);

                // Verifica ownership
                if ($biography->user_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Non autorizzato'
                    ], 403);
                }
            }

            // Salva il file usando Spatie Media Library
            $media = $biography->addMedia($file)
                ->toMediaCollection($collection);

            $mediaInfo = [
                'id' => $media->id,
                'collection_name' => $media->collection_name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => $media->getUrl(),
                'uploaded_at' => $media->created_at->toISOString(),
                'biography_id' => $biographyId
            ];

            $this->logger->info('Biography web: media uploaded successfully', [
                'user_id' => auth()->id(),
                'media_id' => $media->id,
                'biography_id' => $biographyId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File caricato con successo',
                'media' => $mediaInfo
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

    /**
     * Delete biography
     *
     * @param Biography $biography
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Biography $biography): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 401);
        }

        $this->logger->info('Biography web: destroy biography', [
            'user_id' => $user->id,
            'biography_id' => $biography->id,
            'biography_title' => $biography->title
        ]);

        try {
            // Check ownership
            if ($biography->user_id !== $user->id) {
                $this->logger->warning('Biography web: unauthorized delete attempt', [
                    'user_id' => $user->id,
                    'biography_id' => $biography->id,
                    'owner_id' => $biography->user_id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Non hai i permessi per eliminare questa biografia'
                ], 403);
            }

            // Delete using service
            $this->biographyService->delete($biography->id, $user);

            $this->logger->info('Biography web: biography deleted successfully', [
                'user_id' => $user->id,
                'biography_id' => $biography->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biografia eliminata con successo'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: destroy failed', [
                'user_id' => $user->id,
                'biography_id' => $biography->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove media from biography
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMedia(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->logger->info('Biography web: remove media', [
            'user_id' => auth()->id(),
            'media_id' => $request->input('media_id')
        ]);

        try {
            $request->validate([
                'media_id' => 'required|exists:media,id',
            ]);

            $user = auth()->user();
            $mediaId = $request->input('media_id');

            // Trova il media e verifica ownership tramite la biografia
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
            $biography = $media->model;

            if (!$biography instanceof Biography || $biography->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }

            // Elimina il media
            $media->delete();

            $this->logger->info('Biography web: media removed successfully', [
                'user_id' => auth()->id(),
                'media_id' => $mediaId,
                'biography_id' => $biography->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Immagine rimossa con successo'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Biography web: remove media validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Biography web: remove media failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la rimozione: ' . $e->getMessage()
            ], 500);
        }
    }
}
