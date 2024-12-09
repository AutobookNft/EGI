<?php

namespace App\View\Components;

use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;
use App\Repositories\IconRepository;

class ImageUploader extends Component
{
    public ?string $iconHtml = null;

    public function __construct(
        public string $model,
        public string $id,
        public string $label,
        public $image ='',
        public string $removeMethod = 'removeImage',
        public ?string $icon = null, // Nome dell'icona da recuperare
        public ?string $iconClass = null // Classe personalizzata per l'icona
    ) {

        // Log::channel('florenceegi')->info( 'Class: ImageUploader. Method: Construct(). Action: iconClass: (' . $iconClass .  ') name: (' . $icon .')');
        // Log::channel('florenceegi')->info( 'Class: ImageUploader. Method: Construct(). Action: model: (' . $model .  ')');
        // Log::channel('florenceegi')->info( 'Class: ImageUploader. Method: Construct(). Action: image: (' . $image .  ')');

        // Recupera l'icona dal repository
        if ($icon) {

            $repository = app(IconRepository::class);
            $this->iconHtml = $repository->getIcon($icon, 'elegant', $iconClass);
        }
    }

    public function render()
    {

        return view('components.image-uploader');
    }
}
