<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DropController extends Controller
{
    public function index()
    {


        // Questa riga semplicemente restituisce la vista 'home.blade.php'
        // Replicando il comportamento di Route::view, ma all'interno del pipeline di un Controller.
        return view('home');

        // $colori = new ColoriBase('blu', 'verde', 'giallo', 'rosso', 'viola');
        // $colori->setGiallo('Ikea');
        // $colore = $colori->getGiallo();

        // return $colore;
    }
}
