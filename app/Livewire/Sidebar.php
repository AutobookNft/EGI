<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use App\Services\Menu\ContextMenus;
use App\Services\Menu\MenuConditionEvaluator;
use App\Repositories\IconRepository;
use App\Services\Menu\Items\OpenCollectionMenu;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Sidebar Component - OS1 Enhanced
 * 🎯 Purpose: Context-aware navigation with modal action support
 *
 * @seo-purpose Primary navigation for FlorenceEGI dashboard and contexts
 * @accessibility-trait Full ARIA navigation and modal trigger support
 *
 * @version 3.0 - OS1 Modal Integration
 */
class Sidebar extends Component
{
    public $menus = [];
    public $contextTitle = '';
    protected $iconRepo;

    /**
     * Mount component with OS1 enhanced menu processing
     *
     * @oracular-purpose Validates menu items for both route and modal action consistency
     */
    public function mount()
    {
        $evaluator = new MenuConditionEvaluator();
        $this->iconRepo = app(\App\Repositories\IconRepository::class);

        // Determina il contesto dalla rotta corrente
        $currentRouteName = Route::currentRouteName();
        $context = explode('.', $currentRouteName)[0] ?? 'dashboard';

        // Imposta il titolo del contesto
        $this->contextTitle = __('menu.' . $context);

        // Ottieni i menu per il contesto corrente
        $allMenus = ContextMenus::getMenusForContext($context);
        Log::channel('upload')->debug('Sidebar component mounted: $allMenus initialized', ['menus' => $allMenus]);

        // Filtra i menu in base ai permessi dell'utente
        foreach ($allMenus as $menu) {
            $filteredItems = array_filter($menu->items, function ($item) use ($evaluator) {
                return $evaluator->shouldDisplay($item);
            });

            if (!empty($filteredItems)) {
                // Converti il MenuGroup in un array associativo
                $menuArray = [
                    'name' => $menu->name,
                    'icon' => $menu->icon ? $this->iconRepo->getDefaultIcon($menu->icon) : null,
                    'permission' => $menu->permission ?? null,
                    'items' => [],
                ];

                foreach ($filteredItems as $item) {
                    $menuItemArray = [
                        'name' => $item->name,
                        'route' => $item->route,
                        'icon' => $item->icon ? $this->iconRepo->getDefaultIcon($item->icon) : null,
                        'permission' => $item->permission ?? null,
                        'children' => $item->children ?? [],
                        // OS1 Enhancement: Modal action support
                        'is_modal_action' => $item->isModalAction ?? false,
                        'modal_action' => $item->modalAction ?? null,
                        'href' => $item->getHref(),
                        'html_attributes' => $item->getHtmlAttributes(),
                    ];

                    $menuArray['items'][] = $menuItemArray;

                    Log::channel('upload')->debug('Menu item processed', [
                        'name' => $item->name,
                        'permission' => $item->permission,
                        'is_modal' => $menuItemArray['is_modal_action'],
                        'modal_action' => $menuItemArray['modal_action']
                    ]);
                }

                $this->menus[] = $menuArray;
            }
        }
    }

    /**
     * Render the sidebar component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.sidebar');
    }
}
