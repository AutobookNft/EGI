<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsentTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Qui decidiamo la "forma" esatta dei dati da restituire.
        // Stiamo selezionando solo i campi che ci interessano.
        return [
            'slug' => $this->slug,
            'name' => $this->getLocalizedName(), // Usiamo il metodo helper per la traduzione
            'description' => $this->getLocalizedDescription(),
            'is_required' => $this->is_required,
            'is_active' => $this->is_active,
            'default_state' => $this->default_state ?? false,
            // Nota: tutti gli altri 30 campi del modello vengono ignorati.
        ];
    }
}
