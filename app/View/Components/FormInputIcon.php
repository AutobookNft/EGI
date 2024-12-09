<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormInputIcon extends Component
{
    public function __construct(
        public string $type = 'text',
        public string $label = '',
        public string $model = '',
        public string $id = '',
        public bool $required = false,
        public string $class = '',
        public string $placeholder = '',
        public string $icon = '',
        public string $iconPosition = 'right', // 'left' o 'right'
        public string $value = ''
    ) {}

    public function render()
    {
        return view('components.form-input-icon');
    }
} 
