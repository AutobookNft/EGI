<?php

namespace App\Services\Menu;

/**
 * @Oracode Menu Group class
 * 🎯 Purpose: Groups related menu items with i18n support
 *
 * @package App\Services\Menu
 * @version 2.0
 */
class MenuGroup
{
    public string $name;
    public ?string $icon;
    public array $items;

    /**
     * Constructor with translation support
     *
     * @param string $translatedName Already translated menu group name
     * @param string|null $iconKey Icon key in IconRepository
     * @param array $items Array of MenuItem objects
     */
    public function __construct(string $translatedName, ?string $iconKey = null, array $items = [])
    {
        $this->name = $translatedName;
        $this->icon = $iconKey;  // Sarà solo la chiave, il rendering avverrà nella vista
        $this->items = $items;
    }

    /**
     * Adds an item to the menu group
     *
     * @param MenuItem $item Menu item to add
     */
    public function addItem(MenuItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Checks if the menu has visible items
     *
     * @return bool True if there are items
     */
    public function hasVisibleItems(): bool
    {
        return !empty($this->items);
    }
}
