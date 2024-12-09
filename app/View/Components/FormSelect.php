<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormSelect extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public string $model,
        public string $id,
        public bool $required = false,
        public string $class = '',
        public string $style = 'primary',
        public string $maxWidth = 'xs'
    ) {
        // Validate style
        $validStyles = ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
        if (!in_array($style, $validStyles)) {
            throw new \InvalidArgumentException("Style must be one of: " . implode(', ', $validStyles));
        }

        // Validate maxWidth
        $validWidths = ['xs', 'sm', 'md', 'lg', 'xl'];
        if (!in_array($maxWidth, $validWidths)) {
            throw new \InvalidArgumentException("MaxWidth must be one of: " . implode(', ', $validWidths));
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.form-select');
    }
} 
