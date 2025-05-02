<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class DropController extends Controller
{
    public function index()
    {
       // Collezioni in evidenza (3 items)
       $collections = Collection::where('is_published', true)
       ->take(3)
       ->get();

        // Ultime gallerie (8 items)
        // $recent = Collection::orderBy('created_at', 'desc')
        //     ->take(8)
        //     ->get();

        return view('home', compact('collections'));
    }
}
