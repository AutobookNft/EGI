<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use App\Services\Menu\ContextMenus;
use App\Services\Menu\MenuConditionEvaluator;
use App\Repositories\IconRepository;

class Sidebar extends Component
{
    public $menus = [];
    public $contextTitle = '';
    protected $iconRepo;

    public function mount()
    {
        $evaluator = new MenuConditionEvaluator();
        $this->iconRepo = new IconRepository();

        // Determina il contesto dalla rotta corrente
        $currentRouteName = Route::currentRouteName();
        $context = explode('.', $currentRouteName)[0] ?? 'dashboard';

        // Imposta il titolo del contesto
        $this->contextTitle = ucfirst($context);

        // Ottieni i menu per il contesto corrente
        $allMenus = ContextMenus::getMenusForContext($context);

        // Filtra i menu in base ai permessi dell'utente
        foreach ($allMenus as $menu) {
            $filteredItems = array_filter($menu->items, function ($item) use ($evaluator) {
                return $evaluator->shouldDisplay($item);
            });

            if (!empty($filteredItems)) {
                // Converti il MenuGroup in un array associativo
                $menuArray = [
                    'name' => $menu->name,
                    'icon' => $menu->icon,
                    'items' => [],
                ];

                foreach ($filteredItems as $item) {
                    $menuArray['items'][] = [
                        'name' => $item->name,
                        'route' => $item->route,
                        'icon' => $this->iconRepo->getDefaultIcon($item->icon),
                        'permission' => $item->permission,
                        'children' => $item->children,
                    ];
                }

                $this->menus[] = $menuArray;
            }
        }
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
