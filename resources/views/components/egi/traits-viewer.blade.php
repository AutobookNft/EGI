{{-- resources/views/components/egi/traits-viewer.blade.php --}}
{{-- 
    EGI Traits Viewer Component - READONLY
    Solo visualizzazione dei traits esistenti
--}}
@props([
    'egi' => null
])

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

<div class="egi-traits-viewer" 
     id="traits-viewer-{{ $egi ? $egi->id : 'new' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}">
    
    {{-- Header con counter --}}
    <div class="traits-header">
        <h3 class="traits-title">
            <span class="traits-icon">🎯</span>
            {{ __('Tratti e Attributi') }}
        </h3>
        <div class="traits-meta">
            <span class="trait-counter">
                <span class="traits-count">0</span>/30
            </span>
        </div>
    </div>

    {{-- Traits Grid (readonly) --}}
    <div class="traits-list readonly">
        <div class="traits-grid" id="traits-grid-viewer">
            {{-- Traits will be inserted here by JS --}}
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Viewer-specific JavaScript
    const TraitsViewer = {
        init: function(egiId) {
            console.log('TraitsViewer: Initializing for EGI', egiId);
            
            const container = document.getElementById(`traits-viewer-${egiId}`);
            if (!container) {
                console.error('TraitsViewer: Container not found for EGI', egiId);
                return;
            }

            this.loadAndDisplayTraits(egiId);
        },

        async loadAndDisplayTraits(egiId) {
            try {
                const response = await fetch(`/egis/${egiId}/traits`);
                const data = await response.json();
                
                if (data.success) {
                    this.renderTraits(data.traits || [], egiId);
                    this.updateCounter(data.traits?.length || 0, egiId);
                }
            } catch (error) {
                console.error('Error loading traits:', error);
            }
        },

        renderTraits(traits, egiId) {
            const grid = document.getElementById('traits-grid-viewer');
            if (!grid) return;

            if (traits.length === 0) {
                grid.innerHTML = '<div class="empty-state-viewer">Nessun tratto definito.</div>';
                return;
            }

            grid.innerHTML = traits.map(trait => {
                const categoryColor = this.getCategoryColor(trait.category_id);
                const isRare = trait.rarity_percentage && trait.rarity_percentage < 10;
                
                return `
                    <div class="trait-card ${isRare ? 'rare' : ''}" data-category="${trait.category_id}">
                        <div class="trait-header">
                            <span class="trait-category-badge" style="background-color: ${categoryColor}">
                                ${this.getCategoryIcon(trait.category_id)}
                            </span>
                        </div>
                        <div class="trait-content">
                            <div class="trait-type">${trait.type_name || trait.trait_type}</div>
                            <div class="trait-value">
                                <span>${this.formatTraitValue(trait)}</span>
                                ${trait.unit ? `<span class="trait-unit">${trait.unit}</span>` : ''}
                            </div>
                            ${trait.rarity_percentage ? `
                                <div class="trait-rarity">
                                    <div class="rarity-bar">
                                        <div class="rarity-fill" style="width: ${100 - trait.rarity_percentage}%"></div>
                                    </div>
                                    <span class="rarity-text">${trait.rarity_percentage.toFixed(1)}% have this</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        },

        updateCounter(count, egiId) {
            const counter = document.querySelector(`#traits-viewer-${egiId} .traits-count`);
            if (counter) {
                counter.textContent = count;
            }
        },

        getCategoryColor(categoryId) {
            const colors = {
                1: '#D4A574', // Materials - Oro
                2: '#8E44AD', // Visual - Viola
                3: '#1B365D', // Dimensions - Blu
                4: '#E67E22', // Special - Arancio
                5: '#2D5016'  // Sustainability - Verde
            };
            return colors[categoryId] || '#6B6B6B';
        },

        getCategoryIcon(categoryId) {
            const icons = {
                1: '📦', // Materials
                2: '🎨', // Visual
                3: '📐', // Dimensions
                4: '⚡', // Special
                5: '🌿'  // Sustainability
            };
            return icons[categoryId] || '🏷️';
        },

        formatTraitValue(trait) {
            if (trait.display_type === 'number' && trait.value) {
                return parseFloat(trait.value).toLocaleString();
            }
            return trait.value || '';
        }
    };

    // Auto-initialize quando il DOM è pronto
    document.addEventListener('DOMContentLoaded', function() {
        const viewer = document.querySelector('.egi-traits-viewer');
        if (viewer) {
            const egiId = viewer.dataset.egiId;
            if (egiId) {
                TraitsViewer.init(egiId);
            }
        }
    });

})();
</script>
