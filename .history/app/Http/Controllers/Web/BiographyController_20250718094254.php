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
 * 🧱 Core Logic: User-friendly biography CRUD with unified create/edit view
 * 🛡️ Security: User ownership validation and public access control
 * 🎨 Brand: FlorenceEGI design system integration
 *
 * @package App\Http\Controllers\Web
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI - Unified Create/Edit Views)
 * @date 2025-07-17
 * @purpose Unified create/edit interface for biography management
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
            return $this->errorManager->handle('BIOGRAPHY_MANAGE_FAILED', [
                'user_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Show unified form to create new biography
     *
     * @return View
     */
    public function create(): View | \Illuminate\Http\RedirectResponse
    {
        $this->logger->info('Biography web: create form accessed', [
            'user_id' => auth()->id()
        ]);

        try {
            $user = auth()->user();

            // Check if user already has a biography
            $existing = Biography::where('user_id', $user->id)->first();
            if ($existing) {
                $this->logger->info('Biography web: user already has biography, redirecting to edit', [
                    'user_id' => $user->id,
                    'existing_biography_id' => $existing->id
                ]);

                return redirect()->route('biography.edit', $existing->id)
                    ->with('info', 'Hai già una biografia. Puoi modificarla qui.');
            }

            return view('biography.edit', [
                'isEdit' => false,
                'biography' => null,
                'chapters' => collect(),
                'biographyMedia' => collect(), // Collection vuota per create
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BIOGRAPHY_CREATE_FAILED', [
                'user_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Show unified form to edit existing biography
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

        $this->logger->info('DEBUG: Biography ID and Media', [
            'biography_id' => $biography->id,
            'media_count' => $biography->media()->count(),
            'main_gallery_count' => $biography->getMedia('main_gallery')->count(),
            'all_media' => $biography->media()->get()->toArray()
        ]);

        try {
            $user = auth()->user();

            // Check ownership
            if ($biography->user_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questa biografia');
            }

            // Carica i capitoli con le loro immagini
            $chapters = $biography->chapters()->orderBy('sort_order', 'asc')->get()->map(function ($chapter) {
                $chapter->media = $chapter->getMedia('chapter_images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'name' => $media->name,
                    ];
                });
                return $chapter;
            });

            // Carica le immagini della biografia principale
            $biographyMedia = $biography->media()
                ->where('collection_name', 'main_gallery')
                ->get()
                ->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                        'file_name' => $media->file_name,
                        'name' => $media->name,
                        'collection_name' => $media->collection_name,
                    ];
                });

            // dd($biographyMedia);

            return view('biography.edit', [
                'isEdit' => true,
                'biography' => $biography,
                'chapters' => $chapters,
                'biographyMedia' => $biographyMedia,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BIOGRAPHY_EDIT_FAILED', [
                'user_id' => auth()->id(),
                'biography_id' => $biography->id,
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
            return $this->errorManager->handle('BIOGRAPHY_VIEW_OWN_FAILED', [
                'user_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Show public biography
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View | \Illuminate\Http\RedirectResponse | \Illuminate\Http\Response
    {
        $this->logger->info('Biography web: show public biography', [
            'user_slug' => $user->slug,
            'viewer_id' => auth()->id()
        ]);

        try {
            $biography = $this->biographyService->getUserPrimaryBiography($user->id);

            // Check if biography is public or viewer is owner
            if (!$biography || (!$biography->is_public && auth()->id() !== $user->id)) {
                return response()->view('biography.show', [
                    'biography' => null,
                    'user' => $user,
                    'isOwn' => auth()->id() === $user->id
                ], 404);
            }

            return view('biography.show', [
                'biography' => $biography,
                'user' => $user,
                'isOwn' => auth()->id() === $user->id
            ]);
        } catch (\Exception $e) {
            $response = $this->errorManager->handle('BIOGRAPHY_SHOW_FAILED', [
                'user_slug' => $user->id,
            ], $e);
            if ($response === null) {
                // Fallback UltraErrorManager con errore generico
                return $this->errorManager->handle('GENERIC_INTERNAL_ERROR', [
                    'user_slug' => $user->slug,
                    'original_exception' => $e->getMessage(),
                ], $e) ?? response('Errore interno', 500);
            }
            return $response;
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

        // Check if user already has biography
        $existing = Biography::where('user_id', $user->id)->first();
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
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:10240', // 10MB max',
            ]);

            $this->logger->debug('Content before save:', [
                'raw_content' => $request->input('content'),
                'validated_content' => $validated['content'],
                'content_length' => strlen($validated['content'])
            ]);

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
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'is_public' => 'boolean',
                'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:4096',
            ]);

            $allowedTags = '<p><br><strong><em><u><strike><ul><ol><li><blockquote><a><h1><h2><h3><h4><h5><h6>';
            $validated['content'] = strip_tags($validated['content'], $allowedTags);

            $this->logger->debug('Content before save:', [
                'raw_content' => $request->input('content'),
                'validated_content' => $validated['content'],
                'content_length' => strlen($validated['content'])
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
        try {
            $request->validate([
                'collection' => 'required|in:featured_image,main_gallery',
                'file' => 'required|image|mimes:jpeg,png,webp,jpg|max:10240', // 10MB max
                'biography_id' => 'nullable|exists:biographies,id',
            ]);

            $user = auth()->user();
            $file = $request->file('file');
            $collection = $request->input('collection');
            $biographyId = $request->input('biography_id');

            // Se non esiste una biografia, creane una bozza
            if (!$biographyId) {
                $this->logger->info('Biography web: CREATING', [
                    'user_id' => $user->id
                ]);
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
                $this->logger->info('Biography web: FETCHING', [
                    'user_id' => $user->id,
                    'biography_id' => $biographyId
                ]);

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
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la rimozione: ' . $e->getMessage()
            ], 500);
        }
    }
}
