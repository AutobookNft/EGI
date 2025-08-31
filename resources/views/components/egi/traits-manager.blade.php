{{-- resources/views/components/egi/traits-manager.blade.php --}}
{{-- 
    EGI Traits Manager Component - VANILLA JS VERSION
    @package FlorenceEGI\Components
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 2.0.0 (FlorenceEGI Traits System - NO ALPINE!)
    @date 2024-12-27
--}}
@props([
    'egi' => null,
    'readonly' => false
])

<div class="egi-traits-manager" 
     id="traits-manager-{{ $egi ? $egi->id : 'new' }}-{{ $readonly ? 'readonly' : 'editable' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}"
     data-readonly="{{ $readonly ? 'true' : 'false' }}">
    
    {{-- Header con counter e stato --}}
    <div class="traits-header">
        <h3 class="traits-title">
            <span class="traits-icon">🎯</span>
            {{ __('traits.title') }}
        </h3>
        <div class="traits-meta">
            <span class="trait-counter">
                <span class="traits-count">0</span>/30
            </span>
            <span class="trait-status" style="display: none;">
                <svg class="status-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                {{ __('traits.locked_on_ipfs') }}
            </span>
        </div>
    </div>

    {{-- Categories Navigation --}}
    @if(!$readonly)
    <div class="trait-categories" id="categories-nav">
        {{-- Categories will be inserted here by JS --}}
    </div>
    @endif

    {{-- Active Traits List --}}
    <div class="traits-list {{ $readonly ? 'readonly' : '' }}">
        {{-- Empty State --}}
        <div class="empty-state" id="empty-state" style="display: none;">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            @if($readonly)
            <p class="empty-text">{{ __('Nessun tratto definito per questo EGI.') }}</p>
            @else
            <p class="empty-text">{{ __('traits.empty_state') }}</p>
            <button type="button" 
                    class="empty-cta"
                    onclick="TraitsManager.openModal('{{ $egi ? $egi->id : 'new' }}')">
                {{ __('traits.add_first_trait') }}
            </button>
            @endif
        </div>

        {{-- Traits Grid --}}
        <div class="traits-grid" id="traits-grid">
            {{-- Traits will be inserted here by JS --}}
        </div>
    </div>

    {{-- Add Trait Button --}}
    @if(!$readonly)
    <button type="button" 
            class="add-trait-button"
            id="add-trait-btn"
            onclick="TraitsManager.openModal('{{ $egi ? $egi->id : 'new' }}')"
            style="display: none;">
        <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('traits.add_trait') }}
    </button>
    
    {{-- Save All Traits Button - SPOSTATO QUI FUORI DAL MODAL --}}
    <button type="button" 
            class="save-traits-button"
            onclick="TraitsManager.saveTraits()"
            style="display: none;"
            id="save-traits-btn">
        <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Save All Traits
    </button>
    @endif

    {{-- Hidden input for form submission --}}
    <input type="hidden" 
           name="traits" 
           id="traits-json-{{ $egi ? $egi->id : 'new' }}"
           value="[]">
</div>

{{-- Trait Modal (single instance at page level) --}}
@once
<div class="trait-modal" id="trait-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ __('traits.add_new_trait') }}</h4>
            <button type="button" 
                    class="modal-close"
                    onclick="TraitsManager.closeModal()">
                ×
            </button>
        </div>

        <div class="modal-body">
            {{-- Step 1: Select Category --}}
            <div class="form-group">
                <label class="form-label">{{ __('traits.select_category') }}</label>
                <div class="category-selector" id="category-selector">
                    {{-- Categories will be inserted here by JS --}}
                </div>
            </div>

            {{-- Step 2: Select Trait Type --}}
            <div class="form-group" id="type-selector-group" style="display: none;">
                <label class="form-label">{{ __('traits.select_type') }}</label>
                <select class="form-select" id="trait-type-select" onchange="TraitsManager.onTypeSelected()">
                    <option value="">{{ __('traits.choose_type') }}</option>
                </select>
            </div>

            {{-- Step 3: Select/Input Value --}}
            <div class="form-group" id="value-selector-group" style="display: none;">
                <label class="form-label">{{ __('traits.select_value') }}</label>
                <div id="value-input-container">
                    {{-- Input will be inserted here based on type --}}
                </div>
            </div>

            {{-- Preview --}}
            <div class="trait-preview" id="trait-preview" style="display: none;">
                <div class="preview-label">{{ __('traits.preview') }}</div>
                <div class="preview-card">
                    <span class="preview-type"></span>:
                    <span class="preview-value"></span>
                    <span class="preview-unit"></span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" 
                    class="btn-cancel"
                    onclick="TraitsManager.closeModal()">
                {{ __('traits.cancel') }}
            </button>
            <button type="button" 
                    class="btn-confirm"
                    id="confirm-trait-btn"
                    onclick="TraitsManager.addTrait()"
                    disabled>
                {{ __('traits.add') }}
            </button>
        </div>
    </div>
</div>
@endonce



{{-- JavaScript PURO - NIENTE ALPINE! --}}
<script>
/**
 * EGI Traits Manager - Vanilla JavaScript
 * NO ALPINE.JS - Pure Enterprise JavaScript
 * 
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0
 * @date 2024-12-27
 */
(function() {
    'use strict';

    // Global namespace
    window.TraitsManager = window.TraitsManager || {};

    // State management
    const state = {
        currentEgiId: null,
        categories: [],
        availableTypes: [],
        existingTraits: [],  // Traits esistenti dal DB (readonly)
        editingTraits: [],   // Traits in corso di modifica (editable)
        modalData: {
            category_id: null,
            trait_type_id: null,
            value: null,
            currentType: null
        },
        isLocked: false,
        readonly: false
    };

    // Cache DOM elements
    const elements = {};

    /**
     * Initialize manager for a specific EGI
     */
    TraitsManager.init = function(egiId, containerType) {
        console.log('TraitsManager: Initializing for EGI', egiId, 'type:', containerType);
        
        const containerId = `traits-manager-${egiId}-${containerType}`;
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('TraitsManager: Container not found:', containerId);
            return;
        }

        const isReadonly = container.dataset.readonly === 'true';
        
        // Per il primo componente (readonly o editabile), carica i dati
        if (!TraitsManager[`dataLoaded_${egiId}`]) {
            state.currentEgiId = egiId;
            TraitsManager[`dataLoaded_${egiId}`] = true;
            
            // Load initial data
            loadCategories().then(() => {
                if (egiId !== 'new') {
                    loadExistingTraits(egiId);
                } else {
                    updateAllContainers(egiId);
                }
            });
        }
        
        // Salva riferimento al container
        if (!TraitsManager.containers) TraitsManager.containers = {};
        if (!TraitsManager.containers[egiId]) TraitsManager.containers[egiId] = {};
        
        TraitsManager.containers[egiId][containerType] = {
            container: container,
            grid: container.querySelector('#traits-grid'),
            emptyState: container.querySelector('#empty-state'),
            addButton: container.querySelector('#add-trait-btn'),
            counter: container.querySelector('.traits-count'),
            hiddenInput: container.querySelector(`#traits-json-${egiId}`),
            categoriesNav: container.querySelector('#categories-nav'),
            readonly: isReadonly
        };
        
        console.log('TraitsManager: Container registered for', egiId, containerType);
    };

    /**
     * Load categories from API
     */
    async function loadCategories() {
        try {
            console.log('Loading categories from:', '/traits/categories');
            const response = await fetch('/traits/categories');
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Categories data:', data);
            
            if (data.success) {
                state.categories = data.categories;
                renderCategories();
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            // Use fallback data
            state.categories = [
                {id: 1, name: 'Materials', slug: 'materials', icon: '📦'},
                {id: 2, name: 'Visual', slug: 'visual', icon: '🎨'},
                {id: 3, name: 'Dimensions', slug: 'dimensions', icon: '📐'},
                {id: 4, name: 'Special', slug: 'special', icon: '⚡'},
                {id: 5, name: 'Sustainability', slug: 'sustainability', icon: '🌿'}
            ];
            renderCategories();
        }
    }

    /**
     * Load existing traits for an EGI
     */
    async function loadExistingTraits(egiId) {
        try {
            const response = await fetch(`/egis/${egiId}/traits`);
            const data = await response.json();
            
            if (data.success) {
                state.existingTraits = data.traits || [];
                state.editingTraits = []; // Inizia vuoto per l'editing
                state.isLocked = data.is_locked || false;
                updateAllContainers(egiId);
            }
        } catch (error) {
            console.error('Error loading traits:', error);
            updateAllContainers(egiId);
        }
    }

    /**
     * Render categories navigation
     */
    function renderCategories() {
        if (!elements.categoriesNav || state.readonly) return;
        
        elements.categoriesNav.innerHTML = state.categories.map(cat => `
            <button type="button" 
                    class="category-tab" 
                    data-category-id="${cat.id}"
                    onclick="TraitsManager.filterByCategory(${cat.id})">
                <span class="category-icon">${cat.icon}</span>
                <span class="category-name">${cat.name}</span>
                <span class="category-count">0</span>
            </button>
        `).join('');
    }

    /**
     * Update all containers for the current EGI
     */
    function updateAllContainers(egiId) {
        if (!TraitsManager.containers || !TraitsManager.containers[egiId]) return;
        
        Object.values(TraitsManager.containers[egiId]).forEach(containerData => {
            updateContainerUI(containerData);
        });
    }

    /**
     * Update UI for a specific container
     */
    function updateContainerUI(containerData) {
        if (!containerData) return;
        
        // Determina quali traits mostrare
        const traitsToShow = containerData.readonly ? state.existingTraits : state.editingTraits;
        
        // Update counter
        if (containerData.counter) {
            const count = containerData.readonly ? state.existingTraits.length : state.editingTraits.length;
            containerData.counter.textContent = count;
            const counterContainer = containerData.counter.parentElement;
            if (count > 25) {
                counterContainer.classList.add('near-limit');
            } else {
                counterContainer.classList.remove('near-limit');
            }
        }

        // Update save button (solo per container editabile)
        if (!containerData.readonly) {
            const saveBtn = document.getElementById('save-traits-btn');
            if (saveBtn) {
                saveBtn.style.display = state.editingTraits.length > 0 ? 'flex' : 'none';
            }
        }

        // Show/hide empty state
        if (containerData.emptyState) {
            containerData.emptyState.style.display = traitsToShow.length === 0 ? 'block' : 'none';
        }

        // Show/hide add button (solo per container editabile)
        if (containerData.addButton && !containerData.readonly) {
            containerData.addButton.style.display = 
                (!state.isLocked && state.editingTraits.length < 30) ? 'flex' : 'none';
        }

        // Render traits grid
        renderTraitsInContainer(containerData, traitsToShow);

        // Update hidden input
        if (containerData.hiddenInput) {
            containerData.hiddenInput.value = JSON.stringify(state.editingTraits);
        }
    }

    /**
     * Legacy updateUI function - now updates all containers
     */
    function updateUI() {
        updateAllContainers(state.currentEgiId);
        updateCategoryCounts();
    }

    /**
     * Render traits grid in a specific container
     */
    function renderTraitsInContainer(containerData, traitsToRender) {
        if (!containerData.grid) return;

        if (traitsToRender.length === 0) {
            containerData.grid.innerHTML = '';
            return;
        }

        containerData.grid.innerHTML = traitsToRender.map((trait, index) => {
            const categoryColor = getCategoryColor(trait.category_id);
            const isRare = trait.rarity_percentage && trait.rarity_percentage < 10;
            
            return `
                <div class="trait-card ${isRare ? 'rare' : ''}" data-category="${trait.category_id}">
                    <div class="trait-header">
                        <span class="trait-category-badge" style="background-color: ${categoryColor}">
                            ${getCategoryIcon(trait.category_id)}
                        </span>
                        ${!containerData.readonly && !state.isLocked ? `
                            <button type="button" 
                                    class="trait-remove"
                                    onclick="TraitsManager.removeTrait(${index})">
                                ×
                            </button>
                        ` : ''}
                    </div>
                    <div class="trait-content">
                        <div class="trait-type">${trait.type_name || trait.trait_type}</div>
                        <div class="trait-value">
                            <span>${formatTraitValue(trait)}</span>
                            ${trait.unit ? `<span class="trait-unit">${trait.unit}</span>` : ''}
                        </div>
                        ${trait.rarity_percentage ? `
                            <div class="trait-rarity">
                                <div class="rarity-bar">
                                    <div class="rarity-fill" style="width: ${100 - trait.rarity_percentage}%"></div>
                                </div>
                                <span class="rarity-text">${trait.rarity_percentage}% have this</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Open trait modal
     */
    TraitsManager.openModal = function(egiId) {
        console.log('Opening modal for EGI:', egiId);
        
        state.currentEgiId = egiId;
        state.modalData = {
            category_id: null,
            trait_type_id: null,
            value: null,
            currentType: null
        };

        const modal = document.getElementById('trait-modal');
        if (!modal) {
            console.error('Modal element not found');
            return;
        }

        // Reset modal state
        document.getElementById('category-selector').innerHTML = '';
        document.getElementById('type-selector-group').style.display = 'none';
        document.getElementById('value-selector-group').style.display = 'none';
        document.getElementById('trait-preview').style.display = 'none';
        document.getElementById('confirm-trait-btn').disabled = true;

        // Render categories in modal
        renderModalCategories();

        // Show modal
        modal.style.display = 'flex';
    };

    /**
     * Close modal
     */
    TraitsManager.closeModal = function() {
        const modal = document.getElementById('trait-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Render categories in modal
     */
    function renderModalCategories() {
        const selector = document.getElementById('category-selector');
        if (!selector) return;

        selector.innerHTML = state.categories.map(cat => `
            <label class="category-option">
                <input type="radio" 
                       name="modal_category" 
                       value="${cat.id}"
                       onchange="TraitsManager.onCategorySelected(${cat.id})">
                <div class="category-card">
                    <span class="category-icon">${cat.icon}</span>
                    <span class="category-label">${cat.name}</span>
                </div>
            </label>
        `).join('');
    }

    /**
     * Category selected in modal
     */
    TraitsManager.onCategorySelected = async function(categoryId) {
        state.modalData.category_id = categoryId;
        state.modalData.trait_type_id = null;
        state.modalData.value = null;

        // Load trait types for category
        try {
            const response = await fetch(`/traits/types?category_id=${categoryId}`);
            const data = await response.json();
            
            if (data.success) {
                state.availableTypes = data.types || [];
                renderTraitTypes();
            }
        } catch (error) {
            console.error('Error loading trait types:', error);
        }
    };

    /**
     * Render trait types dropdown
     */
    function renderTraitTypes() {
        const group = document.getElementById('type-selector-group');
        const select = document.getElementById('trait-type-select');
        
        if (!group || !select) return;

        select.innerHTML = '<option value="">Choose a type...</option>' +
            state.availableTypes.map(type => 
                `<option value="${type.id}">${type.name}</option>`
            ).join('');

        group.style.display = 'block';
    }

    /**
     * Trait type selected
     */
    TraitsManager.onTypeSelected = function() {
        const select = document.getElementById('trait-type-select');
        const typeId = parseInt(select.value);
        
        if (!typeId) {
            document.getElementById('value-selector-group').style.display = 'none';
            return;
        }

        const type = state.availableTypes.find(t => t.id === typeId);
        if (!type) return;

        state.modalData.trait_type_id = typeId;
        state.modalData.currentType = type;

        renderValueInput(type);
    };

    /**
     * Render value input based on type
     */
    function renderValueInput(type) {
        const group = document.getElementById('value-selector-group');
        const container = document.getElementById('value-input-container');
        
        if (!group || !container) {
            console.error('Value selector elements not found');
            return;
        }

        let html = '';
        
        // Parse allowed_values se è una stringa JSON
        let allowedValues = null;
        if (type.allowed_values) {
            try {
                allowedValues = typeof type.allowed_values === 'string' 
                    ? JSON.parse(type.allowed_values) 
                    : type.allowed_values;
            } catch (e) {
                console.error('Error parsing allowed values:', e);
            }
        }

        if (allowedValues && allowedValues.length > 0) {
            // Predefined values dropdown
            html = `
                <select class="form-select" id="trait-value-input" onchange="TraitsManager.onValueChanged()">
                    <option value="">Choose a value...</option>
                    ${allowedValues.map(v => `<option value="${v}">${v}</option>`).join('')}
                </select>
            `;
        } else if (type.display_type === 'number' || type.display_type === 'percentage' || type.display_type === 'boost_number') {
            // Numeric input
            const min = type.display_type === 'percentage' ? '0' : '';
            const max = (type.display_type === 'percentage' || type.display_type === 'boost_number') ? '100' : '';
            html = `
                <div class="input-group">
                    <input type="number" 
                        class="form-input"
                        id="trait-value-input"
                        ${min !== '' ? `min="${min}"` : ''}
                        ${max !== '' ? `max="${max}"` : ''}
                        step="0.01"
                        placeholder="Enter value"
                        oninput="TraitsManager.onValueChanged()">
                    ${type.unit ? `<span class="input-suffix">${type.unit}</span>` : ''}
                </div>
            `;
        } else if (type.display_type === 'date') {
            // Date input
            html = `
                <input type="date" 
                    class="form-input"
                    id="trait-value-input"
                    onchange="TraitsManager.onValueChanged()">
            `;
        } else {
            // Fallback text input
            html = `
                <input type="text" 
                    class="form-input"
                    id="trait-value-input"
                    placeholder="Enter value"
                    oninput="TraitsManager.onValueChanged()">
            `;
        }

        console.log('Generated HTML for value input:', html);
        container.innerHTML = html;
        group.style.display = 'block';
    }

    /**
     * Save traits to database
     */
    TraitsManager.saveTraits = async function() {
        if (!state.currentEgiId || state.currentEgiId === 'new') {
            console.error('Cannot save traits without EGI ID');
            return;
        }
        
        console.log('=== SAVING TRAITS ===');
        console.log('EGI ID:', state.currentEgiId);
        console.log('Traits to save:', state.traits);
        console.log('URL:', `/egis/${state.currentEgiId}/traits`);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');
        
        try {
            const response = await fetch(`/egis/${state.currentEgiId}/traits`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    traits: state.traits
                })
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));
            
            const data = await response.json();
            console.log('Save response data:', data);
            
            if (data.success) {
                // Mostra messaggio di successo
                TraitsManager.showNotification('Traits saved successfully!', 'success');
            } else {
                console.error('Save failed:', data.message);
                TraitsManager.showNotification(data.message || 'Error saving traits', 'error');
            }
        } catch (error) {
            console.error('Error saving traits:', error);
            TraitsManager.showNotification('Error saving traits', 'error');
        }
    };

    /**
     * Show notification (semplice implementazione)
     */
    TraitsManager.showNotification = function(message, type) {
        console.log('Showing notification:', message, type);
        // Crea un div di notifica temporaneo
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4ADE80' : '#FF6B6B'};
            color: white;
            border-radius: 8px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Rimuovi dopo 3 secondi
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };

    /**
     * Value changed in modal
     */
    TraitsManager.onValueChanged = function() {
        const input = document.getElementById('trait-value-input');
        const value = input.value;
        
        state.modalData.value = value;

        // Update preview
        const preview = document.getElementById('trait-preview');
        if (value && preview) {
            preview.querySelector('.preview-type').textContent = state.modalData.currentType.name;
            preview.querySelector('.preview-value').textContent = value;
            preview.querySelector('.preview-unit').textContent = state.modalData.currentType.unit || '';
            preview.style.display = 'block';
        } else if (preview) {
            preview.style.display = 'none';
        }

        // Enable/disable confirm button
        document.getElementById('confirm-trait-btn').disabled = !value;
    };

    /**
     * Add trait from modal
     */
    TraitsManager.addTrait = function() {
        if (!state.modalData.value || !state.modalData.trait_type_id) {
            return;
        }

        const category = state.categories.find(c => c.id === state.modalData.category_id);
        const type = state.modalData.currentType;

        const newTrait = {
            tempId: Date.now(),
            category_id: state.modalData.category_id,
            category_name: category.name,
            trait_type_id: state.modalData.trait_type_id,
            type_name: type.name,
            value: state.modalData.value,
            display_type: type.display_type,
            unit: type.unit,
            sort_order: state.editingTraits.length
        };

        state.editingTraits.push(newTrait);
        updateUI();
        TraitsManager.closeModal();
    };

    /**
     * Remove trait
     */
    TraitsManager.removeTrait = function(index) {
        state.editingTraits.splice(index, 1);
        // Reorder
        state.editingTraits.forEach((trait, i) => {
            trait.sort_order = i;
        });
        updateUI();
    };

    /**
     * Filter by category
     */
    TraitsManager.filterByCategory = function(categoryId) {
        // Implementation for filtering view
        console.log('Filter by category:', categoryId);
    };

    /**
     * Helper functions
     */
    function getCategoryColor(categoryId) {
        const colors = {
            1: '#D4A574', // Materials - Oro
            2: '#8E44AD', // Visual - Viola
            3: '#1B365D', // Dimensions - Blu
            4: '#E67E22', // Special - Arancio
            5: '#2D5016', // Sustainability - Verde
            6: '#8B4513'  // Cultural - Marrone
        };
        return colors[categoryId] || '#6B6B6B';
    }

    function getCategoryIcon(categoryId) {
        const category = state.categories.find(c => c.id === categoryId);
        return category ? category.icon : '🏷️';
    }

    function formatTraitValue(trait) {
        if (trait.display_type === 'percentage') {
            return trait.value + '%';
        }
        if (trait.display_type === 'date' && trait.value) {
            return new Date(trait.value).getFullYear();
        }
        return trait.value;
    }

    function updateCategoryCounts() {
        if (!elements.categoriesNav) return;

        state.categories.forEach(cat => {
            const count = state.traits.filter(t => t.category_id === cat.id).length;
            const btn = elements.categoriesNav.querySelector(`[data-category-id="${cat.id}"] .category-count`);
            if (btn) {
                btn.textContent = count;
            }
        });
    }

    // Auto-initialize on DOM ready (solo una volta)
    if (!window.TraitsManagerInitialized) {
        window.TraitsManagerInitialized = true;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== TRAITS MANAGER INITIALIZING ===');
            // Find all trait managers on page
            const managers = document.querySelectorAll('.egi-traits-manager');
            console.log('Found managers:', managers.length);
            
            managers.forEach(container => {
                const egiId = container.dataset.egiId || 'new';
                const containerId = container.id;
                let containerType = 'editable'; // default
                
                if (containerId.includes('-readonly')) {
                    containerType = 'readonly';
                } else if (containerId.includes('-editable')) {
                    containerType = 'editable';
                }
                
                console.log('Initializing manager:', egiId, 'type:', containerType);
                TraitsManager.init(egiId, containerType);
            });
        });
    }

})();
</script>