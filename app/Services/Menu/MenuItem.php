<?php

namespace App\Services\Menu;

/**
 * @Oracode Menu Item class - OS1 Enhanced
 * 🎯 Purpose: Base class for all menu items with i18n and modal action support
 *
 * @seo-purpose Menu system foundation for FlorenceEGI navigation
 * @accessibility-trait Supports ARIA navigation patterns
 *
 * @package App\Services\Menu
 * @version 3.0 - OS1 Enhanced
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

    // OS1 Enhancement: Modal action support
    public ?string $modalAction;
    public bool $isModalAction;

    /**
     * Constructor with translation and modal action support
     *
     * @param string $translationKey The translation key for the menu item name
     * @param string $route The route name for this menu item (or '#' for modal actions)
     * @param string|null $icon The icon key for this menu item
     * @param string|null $permission The permission required to see this menu item
     * @param array|null $children Child menu items, if any
     * @param string|null $modalAction The modal action attribute (e.g., 'open-create-collection-modal')
     *
     * @oracular-purpose Validates that either route or modalAction is properly defined
     */
    public function __construct(
        string $translationKey,
        string $route,
        ?string $icon = null,
        ?string $permission = null,
        ?array $children = null,
        ?string $modalAction = null
    ) {
        $this->translationKey = $translationKey;
        $this->name = __($translationKey); // Traduzione immediata
        $this->route = $route;
        $this->icon = $icon;
        $this->permission = $permission;
        $this->children = $children;

        // OS1 Enhancement: Modal action support
        $this->modalAction = $modalAction;
        $this->isModalAction = !empty($modalAction);

        // OS1 Validation: Ensure semantic coherence
        if ($this->isModalAction && $route !== '#') {
            throw new \InvalidArgumentException(
                "Modal action items must use '#' as route. Item: {$translationKey}"
            );
        }
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

    /**
     * Gets the appropriate href for this menu item
     *
     * @return string The href attribute value
     *
     * @oracular-purpose Ensures proper href generation for both routes and modal actions
     */
    public function getHref(): string
    {
        if ($this->isModalAction) {
            return '#';
        }

        return route($this->route);
    }

    /**
     * Gets the HTML attributes for this menu item
     *
     * @return array Associative array of HTML attributes
     *
     * @accessibility-trait Provides proper attributes for screen readers
     */
    public function getHtmlAttributes(): array
    {
        $attributes = [];

        if ($this->isModalAction) {
            $attributes['data-action'] = $this->modalAction;
            $attributes['role'] = 'button';
            $attributes['aria-label'] = $this->name;
        }

        return $attributes;
    }
}
