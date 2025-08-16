<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FegiAuth;

/**
 * 📜 Collection Badge Component
 * Componente autonomo per visualizzare il badge della collection corrente
 * con gestione completa TypeScript integrata.
 *
 * @version 1.0.0
 * @date 2025-08-16
 * @author Fabio Cherici
 */
class CollectionBadge extends Component {
    public string $size;
    public bool $showWhenEmpty;
    public string $position;
    public ?int $collectionId;
    public ?string $collectionName;
    public int $egiCount;
    public bool $canEdit;
    public string $uniqueId;
    public bool $shouldRender;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $size = 'normal',
        bool $showWhenEmpty = false,
        string $position = 'header'
    ) {
        $this->size = $size;
        $this->showWhenEmpty = $showWhenEmpty;
        $this->position = $position;

        // Genera un ID univoco per questo badge
        $this->uniqueId = 'collection-badge-' . uniqid();

        // Ottieni i dati della collection corrente
        $user = Auth::user();
        if ($user) {
            $this->collectionId = $user->current_collection_id;
            $this->collectionName = $user->getCurrentCollectionName();
            $this->egiCount = $user->getCurrentCollectionEgiCount();

            // Verifica se l'utente può modificare la collection corrente
            if ($this->collectionId && $user->currentCollection) {
                $this->canEdit = $user->can('manage_collection', $user->currentCollection);
            } else {
                $this->canEdit = false;
            }
        } else {
            $this->collectionId = null;
            $this->collectionName = null;
            $this->canEdit = false;
            $this->egiCount = 0;
        }

        // Imposta shouldRender per il template
        $this->shouldRender = $this->shouldRender();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() {
        return view('components.collection-badge');
    }

    /**
     * Determine if the component should be rendered.
     */
    public function shouldRender(): bool {
        // DEBUG: Forza sempre il rendering per test
        return true;

        // Mostra sempre se showWhenEmpty è true
        if ($this->showWhenEmpty) {
            return true;
        }

        // Altrimenti mostra solo se c'è una collection
        return $this->collectionId && $this->collectionName;
    }

    /**
     * Get the CSS classes based on size
     */
    public function getSizeClasses(): array {
        return match ($this->size) {
            'small' => [
                'container' => 'px-2 py-1',
                'text' => 'text-xs',
                'icon' => 'text-xs mr-1'
            ],
            'large' => [
                'container' => 'px-4 py-2',
                'text' => 'text-base',
                'icon' => 'text-base mr-2'
            ],
            default => [
                'container' => 'px-3 py-1.5',
                'text' => 'text-sm',
                'icon' => 'text-sm mr-2'
            ]
        };
    }

    /**
     * Get position-specific CSS classes
     */
    public function getPositionClasses(): string {
        return match ($this->position) {
            'sidebar' => 'mb-2',
            'inline' => 'inline-flex',
            default => 'flex' // navbar
        };
    }

    /**
     * Get the appropriate URL (edit or view)
     */
    public function getBadgeUrl(): string {
        if (!$this->collectionId) {
            return '#';
        }

        if ($this->canEdit) {
            return route('home.collections.edit', $this->collectionId);
        } else {
            return route('home.collections.show', $this->collectionId);
        }
    }

    /**
     * Get the title attribute
     */
    public function getBadgeTitle(): string {
        if (!$this->collectionName) {
            return __('collection.no_current_collection');
        }

        if ($this->canEdit) {
            return __('collection.edit_collection', ['name' => $this->collectionName]);
        } else {
            return __('collection.view_collection', ['name' => $this->collectionName]);
        }
    }
}
