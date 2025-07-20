<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @Oracode Service: Media Upload Handler
 * 🎯 Purpose: Centralize and handle all media uploads via Spatie MediaLibrary.
 * 🧱 Core Logic: Takes a model and a file, returns the created Media object.
 * ⚡ Performance: Delegates conversion to the queue system.
 */
class MediaUploadService
{
    /**
     * Gestisce l'aggiunta di un media a un modello Eloquent.
     *
     * @param HasMedia|Model $model L'istanza del modello (es. BiographyChapter).
     * @param UploadedFile $file Il file caricato dall'utente.
     * @param string $collectionName Il nome della collezione Spatie.
     *
     * @return Media L'oggetto Media appena creato.
     */
    public function handleUpload(HasMedia $model, UploadedFile $file, string $collectionName): Media
    {
        // Questa è la nostra logica pulita, isolata e funzionante.
        // Si basa sulla v11 di Spatie e sulle best practice.
        return $model
            ->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection($collectionName);
    }
}
