<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Log;

class RepoIcon extends Component
{
    public ?string $iconHtml = null;

    public function __construct(
        public string $name,
        public ?string $class = null
    ) {
        $repository = app(IconRepository::class);
        Log::channel('florenceegi')->info('RepoIcon: icon name: ' . $name);
        $this->iconHtml = $repository->getIcon($name, 'elegant', $class);
    }

    public function render()
    {
        return view('components.repo-icon');
    }
}
