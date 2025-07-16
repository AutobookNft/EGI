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
}
