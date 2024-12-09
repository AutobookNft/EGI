<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SuggestionIcon extends Component
{
    public string $tooltip;
    public string $iconColor;

    public function __construct(string $tooltip = 'Suggestion', string $iconColor = '#5f6368')
    {
        $this->tooltip = $tooltip;
        $this->iconColor = $iconColor;
    }

    public function render()
    {
        return view('components.suggestion-icon');
    }
}
