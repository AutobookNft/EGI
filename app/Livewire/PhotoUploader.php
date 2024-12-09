<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

class PhotoUploader extends Component
{
    use WithFileUploads;

    public $photo;

    public function updatedPhoto()
    {
        Log::info('Foto caricata:', ['photo' => $this->photo]);
        Log::info('File caricato:', ['name' => $this->photo->getClientOriginalName()]);
        Log::info('Percorso temporaneo:', ['path' => $this->photo->getRealPath()]);
        Log::info('Temporary URL:', ['url' => $this->photo->temporaryUrl()]);


        if ($this->photo) {
            Log::info('Temporary URL:', ['url' => $this->photo->temporaryUrl()]);
        }
    }

    public function getTemporaryUrl()
    {
        try {
            return $this->photo->temporaryUrl();
        } catch (\Exception $e) {
            Log::error('Errore nel generare temporaryUrl:', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function save()
    {
        // Valida il file
        $this->validate([
            'photo' => 'image|max:2048', // Max 2MB
        ]);

        // Salva il file nel disco configurato (di default: "storage/app/livewire-tmp")
        $path = $this->photo->store('photos', 'public');

        Log::info('File salvato in:', ['path' => $path]);

        // Mostra un messaggio di successo (opzionale)
        session()->flash('message', 'Foto caricata con successo: ' . $path);
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.photo-uploader');
    }
}
