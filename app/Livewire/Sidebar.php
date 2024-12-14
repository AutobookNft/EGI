<?php

namespace App\Livewire;

use App\Models\BarContextMenu;
use App\Repositories\IconRepository;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use App\Models\BarContext;
use Illuminate\Support\Facades\Log;

class Sidebar extends Component
{
    public $context;
    public $menus;

    public function mount()
    {
        // Ottieni il nome della rotta corrente
        $currentRouteName = Route::currentRouteName();

        // Estrai il contesto dal nome della rotta (es: 'collections.index' -> 'collections')
        $this->context = explode('.', $currentRouteName)[0];

        Log::info($this->context);

    }

    public function render()
    {


        // Query per ottenere il contesto con i summary e i relativi menu
        $context = BarContext::with([
            'summaries' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'summaries.menus' => function ($query) {
                $query->orderBy('position', 'asc');
            }
        ])
        ->where('context', $this->context)
        ->first();

        Log::info('context: '.json_encode($context));



       // Se il contesto esiste, trasforma i dati nel formato desiderato
       if ($context) {
            $this->menus = $context->summaries->map(function ($summary) {

                $icon = $summary->icon ?? '';
                $iconClass = '';

                // Recupero l'icona per il menu sommario
                if ($icon) {
                    $repository = app(IconRepository::class);
                    $iconHtml = $repository->getIcon($icon, 'elegant', $iconClass);
                }

                return [
                    'name' => __('side_nav_bar.title.'.$summary->summary),
                    'permission' => $summary->permission,
                    'summary_icon' => $iconHtml ?? '',
                    'summary_route' => $summary->route ?? 'dashboard',
                    'icon' => $iconHtml ?? '',
                    'summary_head' => $summary->head,
                    'submenu' => $summary->menus->map(function ($menu) {

                        // Recupero l'icona per il menu
                        $iconClass = '';
                        $icon = $menu->icon ?? '';

                        if ($icon) {
                            $repository = app(IconRepository::class);
                            $iconHtml = $repository->getIcon($icon, 'elegant', $iconClass);
                        }

                        Log::info('Icona: '. $iconHtml);

                        return [
                            'name' =>__('side_nav_bar.'.$menu->name),
                            'route' => $menu->route,
                            'permission' => $menu->permission,
                            'head' => $menu->head,
                            'icon' => $iconHtml,
                        ];
                    })->toArray(),
                ];
            })->toArray();
        }

        // Log::info(json_encode($this->menus));

        // foreach($this->menus as $menu){
        //     Log::info(json_encode('icon:'.$menu['summary_icon']));
        //     foreach($menu['submenu'] as $submenu){
        //         Log::info(json_encode($submenu['name']));
        //     }
        // }

        return view('livewire.sidebar');
    }
}
