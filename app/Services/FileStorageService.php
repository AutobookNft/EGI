<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\Collection;

class FileStorageService
{
    use WithFileUploads; // Necessario per sfruttare le funzionalità di Livewire

    /**
     * Salva un file Livewire in una posizione specifica e aggiorna il percorso nel database.
     *
     * @param \Livewire\TemporaryUploadedFile $file
     * @param string $path
     * @param string $filename
     * @param string $disk
     * @param int $collectionId
     * @param string $imageType
     * @return string
     * @throws Exception
     */
    public function saveFile($file, string $path, ?string $filename = null, string $disk = 'public', int $collectionId, string $imageType): string
    {
        try {
            // Usa storeAs per salvare il file
            if ($filename) {
                $savedPath = $file->storeAs($path, $filename, $disk);
            } else {
                $savedPath = $file->store($path, $disk);
            }

            Log::channel('florenceegi')->info('File salvato:', ['path' => $savedPath]);

            // Verifica se il file esiste usando il disco passato
            if (!Storage::disk($disk)->exists($savedPath)) {
                Log::channel('florenceegi')->error('File non trovato dopo storeAs.', ['path' => $savedPath]);
                throw new Exception('Errore durante il salvataggio del file.');
            }

            // Aggiorna il percorso nel database
            $this->updateCollectionImagePath($collectionId, $savedPath, $imageType);

            return $savedPath; // Restituisce il percorso relativo
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore nel salvataggio del file:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Aggiorna il percorso dell'immagine nella tabella collections.
     *
     * @param int $collectionId
     * @param string $savedPath
     * @param string $imageType
     * @throws Exception
     */
    protected function updateCollectionImagePath(int $collectionId, string $savedPath, string $imageType): void
    {
        $collection = Collection::findOrFail($collectionId);

        switch ($imageType) {
            case 'banner':
                $collection->path_image_banner = $savedPath;
                break;
            case 'card':
                $collection->path_image_card = $savedPath;
                break;
            case 'avatar':
                $collection->path_image_avatar = $savedPath;
                break;
            case 'EGI':
                $collection->path_image_EGI = $savedPath;
                break;
            default:
                throw new Exception("Tipo di immagine non supportato: $imageType");
        }

        $collection->save();

        Log::channel('florenceegi')->info('Percorso immagine aggiornato nel database.', [
            'collection_id' => $collectionId,
            'image_type' => $imageType,
            'path' => $savedPath,
        ]);
    }
}

