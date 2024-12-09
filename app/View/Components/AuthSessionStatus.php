<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthSessionStatus extends Component
{
    public function __construct(
        public ?string $status = null
    ) {}

    public function render(): View
    {
        return view('components.auth-session-status');
    }
}
