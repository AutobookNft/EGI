<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function setAutoOpen(Request $request)
    {
        $request->validate(['auto_open' => 'boolean']);
        session(['natan_assistant_auto_open' => $request->input('auto_open')]);
        return response()->json(['success' => true]);
    }
}
