<?php

namespace App\Services\Menu;

class MenuItem
{
    public string $name;
    public string $route;
    public ?string $icon;
    public ?string $permission;
    /** @var MenuItem[]|null */
    public ?array $children;

    public function __construct(
        string $name,
        string $route,
        ?string $icon = null,
        ?string $permission = null,
        ?array $children = null
    ) {
        $this->name = $name;
        $this->route = $route;
        $this->icon = $icon;
        $this->permission = $permission;
        $this->children = $children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }
}
