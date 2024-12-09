<?php

declare(strict_types=1);

namespace App\Livewire\Welcome;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class AuthForms extends Component
{
    public bool $showLogin = true;

    public function toggleForm(): void
    {
        $this->showLogin = !$this->showLogin;
    }

    public function render(): View
    {
        return view('livewire.welcome.auth-forms');
    }
}
