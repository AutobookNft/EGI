<?php

namespace App\Services\Menu;

use Illuminate\Support\Facades\File;

class MenuScanner
{
    protected string $itemsPath = 'app/Services/Menu/Items';

    /**
     * @return array<string, MenuItem[]>
     *
     * Restituisce un array associativo con chiave = contesto, valore = array di MenuItem
     */
    public function getMenuGroups(): array
    {
        // Supponiamo che le classi menu abbiano dei naming convention tipo: Admin*, Dashboard*, Collections*, ...
        // O che magari tu abbia un metodo più intelligente per capire il contesto.
        // Qui ipotizziamo di fare i raggruppamenti in base alle route: per esempio route 'admin.xxx' => 'admin'

        // Per semplicità, immaginiamo che il metodo seguente esista o lo implementi tu:
        // loadAllMenuItems(): ritorna un array di istanze MenuItem caricate dinamicamente
        $allItems = $this->loadAllMenuItems();

        $groups = [];
        foreach ($allItems as $item) {
            // Estraiamo il contesto dalla route
            $context = explode('.', $item->route)[0] ?? 'dashboard';
            $groups[$context][] = $item;
        }

        return $groups;
    }

    protected function loadAllMenuItems(): array
    {
        $items = [];
        // Carica tutte le classi in app/Services/Menu/Items
        $files = File::files(base_path($this->itemsPath));
        foreach ($files as $file) {
            $className = 'App\\Services\\Menu\\Items\\' . $file->getFilenameWithoutExtension();
            if (class_exists($className)) {
                $instance = new $className();
                if ($instance instanceof MenuItem) {
                    $items[] = $instance;
                }
            }
        }
        return $items;
    }
}
