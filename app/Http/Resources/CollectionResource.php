<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Trasforma la risorsa in un array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'team_id' => $this->team_id,
            'type' => $this->type,
            'is_published' => $this->is_published,
            'collection_name' => $this->collection_name,
            'position' => $this->position,
            'EGI_number' => $this->EGI_number,
            'floor_price' => $this->floor_price,
            'description' => $this->description,
            'url_collection_site' => $this->url_collection_site,
            'path_image_banner' => $this->path_image_banner,
            'path_image_card' => $this->path_image_card,
            'path_image_avatar' => $this->path_image_avatar,
        ];
    }
}
