<?php

namespace App\Services\Menu;

/**
 * @Oracode Menu Item class
 * 🎯 Purpose: Base class for all menu items with i18n support
 *
 * @package App\Services\Menu
 * @version 2.0
 */
class MenuItem
{
    public string $name;
    public string $translationKey;
    public string $route;
    public ?string $icon;
    public ?string $permission;
    /** @var MenuItem[]|null */
    public ?array $children;

    /**
     * Constructor with translation support
     *
     * @param string $translationKey The translation key for the menu item name
     * @param string $route The route name for this menu item
     * @param string|null $icon The icon key for this menu item
     * @param string|null $permission The permission required to see this menu item
     * @param array|null $children Child menu items, if any
     */
    public function __construct(
        string $translationKey,
        string $route,
        ?string $icon = null,
        ?string $permission = null,
        ?array $children = null
    ) {
        $this->translationKey = $translationKey;
        $this->name = __($translationKey); // Traduzione immediata
        $this->route = $route;
        $this->icon = $icon;
        $this->permission = $permission;
        $this->children = $children;
    }

    /**
     * Checks if this menu item has children
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }
}
