<?php

namespace App\Services\Menu;

class MenuGroup
{
    public string $name;
    public ?string $icon;
    public array $items;

    public function __construct(string $name, ?string $icon = null, array $items = [])
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->items = $items;
    }

    /**
     * Aggiunge un item al menu.
     *
     * @param MenuItem $item
     */
    public function addItem(MenuItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Verifica se il menu ha item visibili.
     *
     * @return bool
     */
    public function hasVisibleItems(): bool
    {
        return !empty($this->items);
    }
}
