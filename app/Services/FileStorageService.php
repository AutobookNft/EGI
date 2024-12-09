<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    use WithFileUploads; // Necessario per sfruttare le funzionalità di Livewire

    /**
     * Salva un file Livewire in una posizione specifica.
     *
     * @param \Livewire\TemporaryUploadedFile $file
     * @param string $path
     * @param string $filename
     * @param string $disk
     * @return string
     * @throws Exception
     */
    public function saveFile($file, string $path, ?string $filename=null, string $disk = 'public'): string
    {
        try {
            // Usa storeAs per salvare il file
            if ($filename){
                $savedPath = $file->storeAs($path, $filename, $disk);
            }else{
                $savedPath = $file->store($path, $disk);
            }
            Log::channel('florenceegi')->info('File salvato:', ['path' => $savedPath]);

            // Verifica se il file esiste usando il disco passato
            if (! Storage::disk($disk)->exists("$path/$filename")) {
                Log::channel('florenceegi')->error('File non trovato dopo storeAs.', ['path' => "$path/$filename"]);
                throw new Exception('Errore durante il salvataggio del file.');
            }

            return $savedPath; // Restituisce il percorso relativo
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore nel salvataggio del file:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

}
