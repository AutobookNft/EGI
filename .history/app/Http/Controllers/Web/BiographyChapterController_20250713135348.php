<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BiographyChapterController extends Controller
{
    public function store(Request $request, $biography): JsonResponse
    {
        return response()->json(['success' => true, 'action' => 'store', 'biography' => $biography, 'data' => $request->all()]);
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
