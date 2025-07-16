<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Biography;
use App\Models\BiographyChapter;
use Illuminate\Support\Facades\Auth;

class BiographyChapterController extends Controller
{
    public function store(Request $request, $biography): JsonResponse
    {
        $user = Auth::user();
        $bio = Biography::findOrFail($biography);
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
        return response()->json(['success' => true, 'action' => 'update', 'biography' => $biography, 'chapter' => $chapter, 'data' => $request->all()]);
    }

    public function destroy(Request $request, $biography, $chapter): JsonResponse
    {
        return response()->json(['success' => true, 'action' => 'destroy', 'biography' => $biography, 'chapter' => $chapter]);
    }

    public function show(Request $request, $biography, $chapter): JsonResponse
    {
        return response()->json(['success' => true, 'action' => 'show', 'biography' => $biography, 'chapter' => $chapter]);
    }

    public function uploadMedia(Request $request, $chapter): JsonResponse
    {
        $user = Auth::user();
        $chapterModel = BiographyChapter::findOrFail($chapter);
        $biography = $chapterModel->biography;
        if ($biography->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'file|mimes:jpeg,png,webp,gif|max:10240', // 10MB
        ]);
        foreach ($request->file('images', []) as $image) {
            $chapterModel->addMedia($image)->toMediaCollection('chapter_images');
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
