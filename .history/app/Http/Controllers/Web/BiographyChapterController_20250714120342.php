<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Biography;
use App\Models\BiographyChapter;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class BiographyChapterController extends Controller
{


    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with complete dependency injection.
     *
     * @Oracode Principle: Partnership Graduata - AI suggests, human directs, together we orchestrate
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,

    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

    }

    public function store(Request $request, $biography): JsonResponse
    {
        $user = Auth::user();
        $bio = Biography::findOrFail($biography);

        $this->logger->info('BiographyChapterController: store', [
            'user_id' => $user->id,
            'biography_id' => $biography,
            'request' => $request->all(),
            'log_category' => 'BIOGRAPHY_CHAPTER_STORE'
        ]);

        if ($bio->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        $chapter = new BiographyChapter($validated);
        $chapter->biography_id = $bio->id;
        $chapter->save();
        return response()->json(['success' => true, 'data' => $chapter]);
    }

    public function update(Request $request, $biography, $chapter): JsonResponse
    {
        $user = Auth::user();
        $bio = Biography::findOrFail($biography);
        $chapterModel = BiographyChapter::findOrFail($chapter);

        if ($bio->user_id !== $user->id || $chapterModel->biography_id !== $bio->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $chapterModel->update($validated);

        return response()->json([
            'success' => true,
            'data' => $chapterModel,
            'message' => 'Capitolo aggiornato con successo'
        ]);
    }

    public function destroy(Request $request, $biography, $chapter): JsonResponse
    {
        $user = Auth::user();
        $bio = Biography::findOrFail($biography);
        $chapterModel = BiographyChapter::findOrFail($chapter);

        if ($bio->user_id !== $user->id || $chapterModel->biography_id !== $bio->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $chapterModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Capitolo eliminato con successo'
        ]);
    }

    public function show(Request $request, $biography, $chapter): JsonResponse
    {
        $user = Auth::user();
        $bio = Biography::findOrFail($biography);
        $chapterModel = BiographyChapter::findOrFail($chapter);

        if ($bio->user_id !== $user->id || $chapterModel->biography_id !== $bio->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $gallery = $chapterModel->getMedia('chapter_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $chapterModel->id,
                'title' => $chapterModel->title,
                'content' => $chapterModel->content,
                'date_from' => $chapterModel->date_from,
                'date_to' => $chapterModel->date_to,
                'gallery' => $gallery
            ]
        ]);
    }

    public function uploadMedia(Request $request, $chapter): JsonResponse
    {
        $user = Auth::user();
        $chapterModel = BiographyChapter::findOrFail($chapter);

        if ($chapterModel->biography->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'file|mimes:jpeg,png,webp,gif|max:10240',
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('images') as $index => $image) {
            if (!$image || !$image->isValid()) {
                $errors[] = "File {$index} non valido";
                continue;
            }

            try {
                $media = $chapterModel
                    ->addMedia($image->getPathname())
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('chapter_images');

                $uploadedFiles[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                ];
            } catch (\Exception $e) {
                $errors[] = "File {$index}: " . $e->getMessage();
                \Log::error("Errore upload file {$index}", ['exception' => $e]);
            }
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'message' => implode(', ', $errors)]);
        }

        return response()->json(['success' => true, 'gallery' => $uploadedFiles]);
    }

    public function removeMedia(Request $request, $chapter): JsonResponse
    {
        $user = Auth::user();
        $chapterModel = BiographyChapter::findOrFail($chapter);
        $biography = $chapterModel->biography;
        if ($biography->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        $request->validate([
            'media_id' => 'required|integer',
        ]);
        $media = $chapterModel->media()->where('id', $request->media_id)->first();
        if ($media) {
            $media->delete();
        }
        $gallery = $chapterModel->getMedia('chapter_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
            ];
        });
        return response()->json(['success' => true, 'gallery' => $gallery]);
    }
}
