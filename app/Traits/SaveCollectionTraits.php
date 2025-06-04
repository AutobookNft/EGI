<?php

namespace App\Traits;

use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPermissionTrait;

trait SaveCollectionTraits
{

    use HasPermissionTrait;

    /**
     * Salva una nuova collezione nel database.
     */

    public function save($collectionId)
    {
        try {
            // Esegui la validazione basata sui decorator
            $this->validate();

            // Recupera la collection
            $collection = Collection::findOrFail($collectionId);

            Log::channel('florenceegi')->info('SaveCollectionTraits: save', ['collection' => $collection]);

            // Verifica il permesso "update_collection"
            $this->hasPermission($collection, 'update_collection');

            // Aggiorna la collection con i dati validati
            $collection->update([
                'collection_name' => $this->collection_name,
                'type' => $this->type,
                'position' => $this->position,
                'EGI_number' => $this->EGI_number,
                'floor_price' => $this->floor_price,
                'description' => $this->description,
                'is_published' => $this->is_published,
                'url_collection_site' => $this->url_collection_site,
            ]);

            // Ricarica i dati aggiornati dal database
            $this->collection = Collection::find($collectionId);

            // Flash del messaggio di successo
            session()->flash('message', 'Collezione aggiornata con successo!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gestisci il caso in cui la collection non viene trovata
            Log::channel('florenceegi')->error('Collection not found: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Errore',
                'text' => 'La collezione specificata non esiste.',
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Gestisci i problemi di autorizzazione
            Log::channel('florenceegi')->warning('Autorizzazione fallita: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Autorizzazione Negata',
                'text' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            // Gestisci altre eccezioni generiche
            Log::channel('florenceegi')->error('Errore durante l\'aggiornamento della collezione: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Errore Inaspettato',
                'text' => 'Si è verificato un errore durante l\'aggiornamento della collezione. Riprova più tardi.',
            ]);
        }
    }
}
