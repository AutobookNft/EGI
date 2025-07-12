<?php

namespace App\Livewire;

use App\Models\Biography;
use App\Models\BiographyChapter;
use App\Services\BiographyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * @Oracode Livewire: Biography Editor Component
 * 🎯 Purpose: Dynamic biography editing with real-time validation
 * 🧱 Core Logic: Create/edit biographies and chapters with media support
 * 🛡️ Security: User ownership validation and proper file handling
 * 📡 Integration: Works with BiographyService for business logic
 *
 * @package App\Livewire
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography Editor)
 * @date 2025-01-07
 */
class BiographyEditor extends Component
{
    use WithFileUploads;

    // Biography properties
    public $biographyId;
    public $title = '';
    public $type = 'single';
    public $content = '';
    public $excerpt = '';
    public $isPublic = false;
    public $isCompleted = false;
    public $settings = [];

    // Chapter properties
    public $chapters = [];
    public $currentChapter = null;
    public $showChapterForm = false;

    // Media properties
    public $featuredImage;
    public $galleryImages = [];

    // UI state
    public $isLoading = false;
    public $isEditing = false;
    public $activeTab = 'basic';
    public $showPreview = false;

    // Services
    private BiographyService $biographyService;

    protected $rules = [
        'title' => 'required|string|max:255',
        'type' => 'required|in:single,chapters',
        'content' => 'required_if:type,single|string',
        'excerpt' => 'nullable|string|max:500',
        'isPublic' => 'boolean',
        'isCompleted' => 'boolean',
        'featuredImage' => 'nullable|image|max:2048',
        'galleryImages.*' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'title.required' => 'Il titolo è obbligatorio',
        'title.max' => 'Il titolo non può superare i 255 caratteri',
        'content.required_if' => 'Il contenuto è obbligatorio per biografie singole',
        'excerpt.max' => 'L\'estratto non può superare i 500 caratteri',
        'featuredImage.image' => 'Il file deve essere un\'immagine',
        'featuredImage.max' => 'L\'immagine non può superare i 2MB',
        'galleryImages.*.image' => 'Tutti i file devono essere immagini',
        'galleryImages.*.max' => 'Le immagini non possono superare i 2MB',
    ];

    public function boot(BiographyService $biographyService)
    {
        $this->biographyService = $biographyService;
    }

    public function mount()
    {
        $this->settings = [
            'theme' => 'default',
            'show_timeline' => true,
            'allow_comments' => false,
        ];
    }

    protected $listeners = [
        'loadBiography' => 'loadBiography',
        'resetForm' => 'resetForm',
        'addChapter' => 'addChapter',
        'editChapter' => 'editChapter',
        'deleteChapter' => 'deleteChapter',
        'updateTrixContent' => 'updateTrixContent',
        'updateChapterTrixContent' => 'updateChapterTrixContent',
    ];

    public function loadBiography($biographyId)
    {
        try {
            $biography = $this->biographyService->fetch($biographyId);

            // Check ownership
            if ($biography->user_id !== Auth::id()) {
                $this->addError('general', 'Non autorizzato ad accedere a questa biografia');
                return;
            }

            $this->biographyId = $biography->id;
            $this->title = $biography->title;
            $this->type = $biography->type;
            $this->content = $biography->content ?? '';
            $this->excerpt = $biography->excerpt ?? '';
            $this->isPublic = $biography->is_public;
            $this->isCompleted = $biography->is_completed;
            $this->settings = $biography->settings ?? [];
            $this->isEditing = true;

            // Load chapters if type is chapters
            if ($biography->type === 'chapters') {
                $this->chapters = $biography->chapters->toArray();
            }

            $this->activeTab = 'basic';
        } catch (\Exception $e) {
            $this->addError('general', 'Errore nel caricamento della biografia: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'biographyId',
            'title',
            'type',
            'content',
            'excerpt',
            'isPublic',
            'isCompleted',
            'chapters',
            'currentChapter',
            'featuredImage',
            'galleryImages',
            'isEditing',
            'activeTab'
        ]);

        $this->settings = [
            'theme' => 'default',
            'show_timeline' => true,
            'allow_comments' => false,
        ];

        $this->resetValidation();
    }

    public function updatedType()
    {
        // Clear content if switching to chapters
        if ($this->type === 'chapters') {
            $this->content = '';
        }

        // Clear chapters if switching to single
        if ($this->type === 'single') {
            $this->chapters = [];
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function save()
    {
        $this->isLoading = true;
        $this->resetValidation();

        try {
            // Validate based on type
            if ($this->type === 'single') {
                $this->validate([
                    'title' => 'required|string|max:255',
                    'content' => 'required|string',
                    'excerpt' => 'nullable|string|max:500',
                ]);
            } else {
                $this->validate([
                    'title' => 'required|string|max:255',
                    'excerpt' => 'nullable|string|max:500',
                ]);
            }

            $data = [
                'title' => $this->title,
                'type' => $this->type,
                'content' => $this->content,
                'excerpt' => $this->excerpt,
                'is_public' => $this->isPublic,
                'is_completed' => $this->isCompleted,
                'settings' => $this->settings,
            ];

            // Handle media uploads
            if ($this->featuredImage) {
                $data['media']['featured_image'] = $this->featuredImage;
            }

            if (!empty($this->galleryImages)) {
                $data['media']['gallery'] = $this->galleryImages;
            }

            $biography = $this->biographyService->createOrUpdate(
                $data,
                Auth::user(),
                $this->biographyId
            );

            $this->biographyId = $biography->id;
            $this->isEditing = true;

            // Handle chapters for new biographies
            if ($this->type === 'chapters' && !empty($this->chapters)) {
                foreach ($this->chapters as $index => $chapterData) {
                    // Only create chapters that are temporary (not saved yet)
                    if (str_starts_with($chapterData['id'], 'temp_')) {
                        $chapterCreateData = [
                            'title' => $chapterData['title'],
                            'content' => $chapterData['content'],
                            'date_from' => $chapterData['date_from'] ?? null,
                            'date_to' => $chapterData['date_to'] ?? null,
                            'is_ongoing' => $chapterData['is_ongoing'] ?? false,
                            'is_published' => $chapterData['is_published'] ?? false,
                            'chapter_type' => $chapterData['chapter_type'] ?? 'standard',
                            'sort_order' => $chapterData['sort_order'] ?? ($index + 1),
                        ];

                        $this->biographyService->createChapter(
                            $this->biographyId,
                            $chapterCreateData,
                            Auth::user()
                        );
                    }
                }

                // Reload chapters from database
                $biography = $this->biographyService->fetch($this->biographyId);
                $this->chapters = $biography->chapters->toArray();
            }

            $this->dispatch('biographySaved', [
                'message' => $this->isEditing ? 'Biografia aggiornata con successo' : 'Biografia creata con successo',
                'biography' => $biography
            ]);

            // Reset file uploads
            $this->featuredImage = null;
            $this->galleryImages = [];

            session()->flash('success', $this->isEditing ? 'Biografia aggiornata con successo' : 'Biografia creata con successo');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            $this->addError('general', 'Errore durante il salvataggio: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function addChapter()
    {
        $this->currentChapter = [
            'id' => null,
            'title' => '',
            'content' => '',
            'date_from' => '',
            'date_to' => '',
            'is_ongoing' => false,
            'sort_order' => count($this->chapters) + 1,
            'is_published' => false,
        ];

        $this->showChapterForm = true;
    }

    public function editChapter($chapterIndex)
    {
        if (isset($this->chapters[$chapterIndex])) {
            $this->currentChapter = $this->chapters[$chapterIndex];
            $this->showChapterForm = true;

            // Dispatch event to update Trix editor content
            $this->dispatch('chapter-trix-content-updated', [
                'content' => $this->currentChapter['content'] ?? ''
            ]);
        }
    }

    public function saveChapter()
    {
        $this->validate([
            'currentChapter.title' => 'required|string|max:255',
            'currentChapter.content' => 'required|string',
            'currentChapter.date_from' => 'nullable|date',
            'currentChapter.date_to' => 'nullable|date|after_or_equal:currentChapter.date_from',
        ]);

        try {
            if ($this->biographyId) {
                // If biography exists, save chapter via service
                $chapterData = [
                    'title' => $this->currentChapter['title'],
                    'content' => $this->currentChapter['content'],
                    'date_from' => $this->currentChapter['date_from'] ?? null,
                    'date_to' => $this->currentChapter['date_to'] ?? null,
                    'is_ongoing' => $this->currentChapter['is_ongoing'] ?? false,
                    'is_published' => $this->currentChapter['is_published'] ?? false,
                    'chapter_type' => $this->currentChapter['chapter_type'] ?? 'standard',
                    'sort_order' => $this->currentChapter['sort_order'] ?? (count($this->chapters) + 1),
                ];

                if ($this->currentChapter['id'] && !str_starts_with($this->currentChapter['id'], 'temp_')) {
                    // Update existing chapter
                    $chapter = $this->biographyService->updateChapter(
                        $this->biographyId,
                        $this->currentChapter['id'],
                        $chapterData,
                        Auth::user()
                    );
                } else {
                    // Create new chapter
                    $chapter = $this->biographyService->createChapter(
                        $this->biographyId,
                        $chapterData,
                        Auth::user()
                    );
                }

                // Reload chapters from database
                $biography = $this->biographyService->fetch($this->biographyId);
                $this->chapters = $biography->chapters->toArray();

                session()->flash('success', 'Capitolo salvato con successo');
            } else {
                // For new biographies, store in local array
                if ($this->currentChapter['id'] && !str_starts_with($this->currentChapter['id'], 'temp_')) {
                    // Update existing chapter
                    $index = array_search($this->currentChapter['id'], array_column($this->chapters, 'id'));
                    if ($index !== false) {
                        $this->chapters[$index] = $this->currentChapter;
                    }
                } else {
                    // Add new chapter
                    $this->currentChapter['id'] = 'temp_' . time();
                    $this->chapters[] = $this->currentChapter;
                }
            }

            $this->showChapterForm = false;
            $this->currentChapter = null;
        } catch (\Exception $e) {
            $this->addError('general', 'Errore nel salvataggio del capitolo: ' . $e->getMessage());
        }
    }

    public function deleteChapter($chapterIndex)
    {
        if (isset($this->chapters[$chapterIndex])) {
            try {
                $chapterData = $this->chapters[$chapterIndex];

                if ($this->biographyId && isset($chapterData['id']) && !str_starts_with($chapterData['id'], 'temp_')) {
                    // Delete from database via service
                    $this->biographyService->deleteChapter(
                        $this->biographyId,
                        $chapterData['id'],
                        Auth::user()
                    );

                    // Reload chapters from database
                    $biography = $this->biographyService->fetch($this->biographyId);
                    $this->chapters = $biography->chapters->toArray();

                    session()->flash('success', 'Capitolo eliminato con successo');
                } else {
                    // Delete from local array
                    unset($this->chapters[$chapterIndex]);
                    $this->chapters = array_values($this->chapters); // Reindex array
                }
            } catch (\Exception $e) {
                $this->addError('general', 'Errore nell\'eliminazione del capitolo: ' . $e->getMessage());
            }
        }
    }

    public function cancelChapterEdit()
    {
        $this->showChapterForm = false;
        $this->currentChapter = null;
        $this->resetValidation();
    }

    public function reorderChapters($newOrder)
    {
        try {
            if ($this->biographyId) {
                // Reorder via service
                $this->biographyService->reorderChapters(
                    $this->biographyId,
                    $newOrder,
                    Auth::user()
                );

                // Reload chapters from database
                $biography = $this->biographyService->fetch($this->biographyId);
                $this->chapters = $biography->chapters->toArray();

                session()->flash('success', 'Capitoli riordinati con successo');
            } else {
                // Reorder local array
                $reorderedChapters = [];
                foreach ($newOrder as $index => $chapterId) {
                    $chapter = collect($this->chapters)->firstWhere('id', $chapterId);
                    if ($chapter) {
                        $chapter['sort_order'] = $index + 1;
                        $reorderedChapters[] = $chapter;
                    }
                }
                $this->chapters = $reorderedChapters;
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Errore nel riordinamento dei capitoli: ' . $e->getMessage());
        }
    }

    public function updateTrixContent($content)
    {
        $this->content = $content;
    }

    public function updateChapterTrixContent($content)
    {
        if ($this->currentChapter) {
            $this->currentChapter['content'] = $content;
        }
    }

    public function updatedFeaturedImage()
    {
        // This method is called when featuredImage is updated
        if ($this->featuredImage) {
            try {
                $this->validate([
                    'featuredImage' => 'image|max:2048', // 2MB max
                ]);

                // Clear any previous errors
                $this->resetErrorBag('featuredImage');

                // Dispatch event to frontend to confirm upload completed
                $this->dispatch('uploadCompleted', [
                    'type' => 'featuredImage',
                    'message' => 'Immagine caricata con successo!'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->featuredImage = null;
                $this->addError('featuredImage', $e->validator->errors()->first('featuredImage'));

                $this->dispatch('uploadError', [
                    'type' => 'featuredImage',
                    'message' => $e->validator->errors()->first('featuredImage')
                ]);
            }
        }
    }

    public function updatedGalleryImages()
    {
        // This method is called when galleryImages is updated
        if (!empty($this->galleryImages)) {
            try {
                $this->validate([
                    'galleryImages.*' => 'image|max:2048', // 2MB max per image
                ]);

                // Don't flash message immediately, it causes reload loops
                $this->resetErrorBag('galleryImages');
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->galleryImages = [];
                $this->addError('galleryImages', $e->validator->errors()->first('galleryImages.*'));
            }
        }
    }

    public function removeFeaturedImage()
    {
        $this->featuredImage = null;
        session()->flash('success', 'Immagine rimossa con successo!');
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->galleryImages[$index])) {
            unset($this->galleryImages[$index]);
            $this->galleryImages = array_values($this->galleryImages); // Reindex
            session()->flash('success', 'Immagine rimossa con successo!');
        }
    }

    public function render()
    {
        return view('livewire.biography-editor');
    }
}
