<?php

namespace App\View\Components;

use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

class FormInput extends Component
{
    public ?string $iconHtml = null;

    public function __construct(
        public string $type,
        public string $label,
        public string $model,
        public string $id,
        public bool $required = false,
        public ?string $class = '',
        public ?string $placeholder = '',
        public ?string $icon = null, // Nome dell'icona da recuperare
        public ?string $iconClass = null, // Classe personalizzata per l'icona
        public string $datatip,
        public string $style = 'primary',
    ) {
        // Validate style
        $validStyles = ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
        if (! in_array($style, $validStyles)) {
            throw new \InvalidArgumentException('Style must be one of: '.implode(', ', $validStyles));
        }

        // Log::channel('florenceegi')->info( 'FormInput: iconClass: (' . $iconClass .  ') name: (' . $icon .')');

        // Recupera l'icona dal repository
        if ($icon) {
            $repository = app(IconRepository::class);
            $this->iconHtml = $repository->getIcon($icon, 'elegant', $iconClass);

        }
    }

    public function render()
    {
        return view('components.form-input');
    }
}
