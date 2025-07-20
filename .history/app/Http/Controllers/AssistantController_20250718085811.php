<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function setAutoOpen(Request $request)
    {
        \Log::info('Assistant auto-open POST', [
            'input' => $request->all(),
            'session_before' => session()->all(),
        ]);
        $request->validate(['auto_open' => 'boolean']);
        // Forza il valore booleano anche se arriva come stringa
        $autoOpen = filter_var($request->input('auto_open'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        session(['natan_assistant_auto_open' => $autoOpen]);
        \Log::info('Assistant auto-open POST after', [
            'auto_open' => $autoOpen,
            'session_after' => session()->all(),
        ]);
        return response()->json(['success' => true, 'auto_open' => $autoOpen, 'session' => session()->all()]);
    }
}
