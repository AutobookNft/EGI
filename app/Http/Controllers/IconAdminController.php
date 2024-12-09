<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Icon;

class IconAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:manage_icons');
    }

    public function index()
    {
        $icons = Icon::all();
        return view('admin.icons.index', compact('icons'));
    }

    public function edit(Icon $icon)
    {
        return view('admin.icons.edit', compact('icon'));
    }

    public function update(Request $request, Icon $icon)
    {
        $request->validate([
            'html' => 'required|string',
        ]);

        $icon->update([
            'html' => $request->svg,
        ]);

        return redirect()->route('admin.icons.index')->with('status', 'Icon updated successfully!');
    }
}
