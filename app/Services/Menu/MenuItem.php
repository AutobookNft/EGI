<?php

namespace App\Services\Menu;

/**
 * @Oracode Menu Item class - OS2.0 Enhanced
 * 🎯 Purpose: Base class for all menu items with i18n, modal actions, and parameterized route support.
 *
 * @seo-purpose Menu system foundation for FlorenceEGI navigation
 * @accessibility-trait Supports ARIA navigation patterns
 *
 * @package App\Services\Menu
 * @author Padmin D. Curtis (AI Partner OS2.0) for Fabio Cherici
 * @version 3.1.0 - Parameterized Route Support
 * @deadline 2025-06-30
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

    // OS2.0 ENHANCEMENT: Aggiunto supporto per i parametri della rotta
    public array $routeParams;

    // OS1 Enhancement: Modal action support
    public ?string $modalAction;
    public bool $isModalAction;

    /**
     * Constructor with translation, modal action, and parameterized route support
     *
     * @param string $translationKey The translation key
     * @param string $route The route name (or '#' for modal actions)
     * @param string|null $icon The icon key
     * @param string|null $permission The required permission
     * @param array $routeParams OS2.0 - Associative array of parameters for the route
     * @param array|null $children Child menu items
     * @param string|null $modalAction The modal action attribute
     */
    public function __construct(
        string $translationKey,
        string $route,
        ?string $icon = null,
        ?string $permission = null,
        ?array $children = null,
        ?string $modalAction = null,
        array $routeParams = [], // OS2.0 ENHANCEMENT: Nuovo parametro
    ) {
        $this->translationKey = $translationKey;
        $this->name = __($translationKey);
        $this->route = $route;
        $this->icon = $icon;
        $this->permission = $permission;
        $this->children = $children;

        // OS2.0 ENHANCEMENT: Memorizziamo i parametri
        $this->routeParams = $routeParams;

        // OS1 Enhancement: Modal action support
        $this->modalAction = $modalAction;
        $this->isModalAction = !empty($modalAction);

        // OS1 Validation
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
     */
    public function getHref(): string
    {
        if ($this->isModalAction) {
            return '#';
        }

        // OS2.0 ENHANCEMENT: Usiamo i parametri per generare la rotta
        return route($this->route, $this->routeParams);
    }

    /**
     * Gets the HTML attributes for this menu item
     *
     * @return array Associative array of HTML attributes
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
