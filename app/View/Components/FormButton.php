<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class FormButton extends Component
{
    public function __construct(
        public string $type = 'button',
        public ?string $style = 'primary',
        public ?string $size = null,
        public ?string $class = '',
    ) {}

    public function render()
    {
        return view('components.form-button');
    }
} 
