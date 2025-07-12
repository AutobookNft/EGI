<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnderConstructionController extends Controller
{
    public function show($key)
    {
        return view('info.under-construction', ['key' => $key]);
    }
}
