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
     id="traits-manager-{{ $egi ? $egi->id : 'new' }}"
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
            <p class="empty-text">{{ __('traits.empty_state') }}</p>
            @if(!$readonly)
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
            {{-- Dopo il bottone "Add Trait" --}}
            @if(!$readonly)
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
        </div>
    </div>
</div>
@endonce


<style>
/* ==========================================
   EGI Traits Manager Styles - FIXED CONTRASTS
   FlorenceEGI Brand Compliant
   ========================================== */
.save-traits-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background: var(--verde-rinascita);
    border: none;
    border-radius: 0.5rem;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.save-traits-button:hover {
    background: #234012;
    transform: translateY(-1px);
}
.egi-traits-manager {
    --oro-fiorentino: #D4A574;
    --verde-rinascita: #2D5016;
    --blu-algoritmo: #1B365D;
    --grigio-pietra: #6B6B6B;
    --rosso-urgenza: #C13120;
    --arancio-energia: #E67E22;
    --viola-innovazione: #8E44AD;
    
    font-family: 'Source Sans Pro', 'Open Sans', sans-serif;
    position: relative;
}

/* Header Section */
.traits-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(212, 165, 116, 0.2);
}

.traits-title {
    font-family: 'Playfair Display', 'Crimson Text', serif;
    font-size: 1.5rem;
    font-weight: 600;
    color: white; /* CAMBIATO per dark mode */
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.traits-icon {
    font-size: 1.25rem;
}

.traits-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.trait-counter {
    background: rgba(212, 165, 116, 0.2); /* Oro con opacity */
    color: var(--oro-fiorentino);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.trait-counter.near-limit {
    background: rgba(193, 49, 32, 0.2);
    color: #FF6B6B; /* Rosso più chiaro per contrasto */
}

.trait-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4ADE80; /* Verde più chiaro per dark mode */
    font-size: 0.875rem;
    font-weight: 500;
}

.status-icon {
    width: 1rem;
    height: 1rem;
}

/* Categories Navigation */
.trait-categories {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.category-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05); /* Semi-transparent per dark mode */
    border: 1px solid rgba(212, 165, 116, 0.3);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #E5E5E5; /* Grigio chiaro per dark mode */
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.category-tab:hover:not(:disabled) {
    border-color: var(--oro-fiorentino);
    background: rgba(212, 165, 116, 0.15);
    color: white;
}

.category-tab.active {
    background: var(--oro-fiorentino);
    border-color: var(--oro-fiorentino);
    color: #1B365D; /* Blu scuro su oro per contrasto */
    font-weight: 600;
}

.category-tab:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.category-icon {
    font-size: 1rem;
}

.category-count {
    background: rgba(0, 0, 0, 0.3);
    padding: 0.125rem 0.375rem;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

/* Traits List */
.traits-list {
    min-height: 200px;
    margin-bottom: 1.5rem;
}

.traits-list.readonly {
    pointer-events: none;
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: rgba(212, 165, 116, 0.05);
    border: 2px dashed rgba(212, 165, 116, 0.3);
    border-radius: 0.75rem;
}

.empty-icon {
    width: 3rem;
    height: 3rem;
    color: var(--oro-fiorentino);
    margin: 0 auto 1rem;
    opacity: 0.7;
}

.empty-text {
    color: #A0A0A0; /* Grigio più chiaro per dark mode */
    margin-bottom: 1rem;
}

.empty-cta {
    background: var(--oro-fiorentino);
    color: var(--blu-algoritmo); /* Blu scuro su oro */
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.empty-cta:hover {
    background: #E5B584; /* Oro più chiaro */
    transform: translateY(-1px);
}

/* Traits Grid */
.traits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.trait-card {
    background: rgba(255, 255, 255, 0.05); /* Semi-transparent per dark mode */
    border: 1px solid rgba(212, 165, 116, 0.2);
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.trait-card:hover {
    border-color: var(--oro-fiorentino);
    background: rgba(212, 165, 116, 0.1);
    box-shadow: 0 4px 12px rgba(212, 165, 116, 0.15);
}

.trait-card.rare {
    background: linear-gradient(135deg, rgba(142, 68, 173, 0.15) 0%, rgba(212, 165, 116, 0.15) 100%);
    border-color: var(--viola-innovazione);
}

.trait-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid rgba(212, 165, 116, 0.1);
}

.trait-category-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.375rem;
    font-size: 1rem;
}

.trait-remove {
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(193, 49, 32, 0.2);
    color: #FF6B6B;
    border: none;
    border-radius: 50%;
    font-size: 1.25rem;
    line-height: 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.trait-remove:hover {
    background: var(--rosso-urgenza);
    color: white;
}

.trait-content {
    padding: 0.75rem;
}

.trait-type {
    font-size: 0.75rem;
    color: #A0A0A0; /* Grigio chiaro per dark mode */
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.25rem;
}

.trait-value {
    font-size: 1rem;
    font-weight: 600;
    color: white; /* Bianco per dark mode */
    margin-bottom: 0.5rem;
}

.trait-unit {
    font-weight: 400;
    opacity: 0.7;
    margin-left: 0.25rem;
    color: var(--oro-fiorentino);
}

.trait-rarity {
    margin-top: 0.5rem;
}

.rarity-bar {
    height: 0.25rem;
    background: rgba(107, 107, 107, 0.3);
    border-radius: 0.125rem;
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.rarity-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--viola-innovazione) 0%, var(--oro-fiorentino) 100%);
    transition: width 0.5s ease;
}

.rarity-text {
    font-size: 0.75rem;
    color: #A0A0A0;
}

/* Add Trait Button */
.add-trait-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: 2px dashed var(--oro-fiorentino);
    border-radius: 0.5rem;
    color: var(--oro-fiorentino);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-trait-button:hover {
    background: rgba(212, 165, 116, 0.1);
    border-style: solid;
}

.button-icon {
    width: 1.25rem;
    height: 1.25rem;
}

/* Modal - RIMANE CHIARA */
.trait-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-content {
    background: #FFFFFF; /* Modal rimane bianca per leggibilità */
    border-radius: 0.75rem;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #E5E5E5;
    background: linear-gradient(135deg, rgba(212, 165, 116, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
}

.modal-title {
    font-family: 'Playfair Display', 'Crimson Text', serif;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--blu-algoritmo);
    margin: 0;
}

.modal-close {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: var(--grigio-pietra);
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: rgba(107, 107, 107, 0.1);
    color: var(--rosso-urgenza);
}

.modal-body {
    padding: 1.5rem;
    background: white;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--blu-algoritmo);
}

.category-selector {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.75rem;
}

.category-option {
    position: relative;
}

.category-option input {
    position: absolute;
    opacity: 0;
}

.category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 0.5rem;
    background: white;
    border: 2px solid #E5E5E5;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-option input:checked + .category-card {
    background: rgba(212, 165, 116, 0.15);
    border-color: var(--oro-fiorentino);
    box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
}

.category-card:hover {
    border-color: var(--oro-fiorentino);
    background: rgba(212, 165, 116, 0.05);
}

.category-label {
    font-size: 0.875rem;
    text-align: center;
    color: var(--blu-algoritmo);
    font-weight: 500;
}

.form-select,
.form-input {
    width: 100%;
    padding: 0.75rem;
    background: white;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    font-size: 1rem;
    color: var(--blu-algoritmo);
    transition: all 0.3s ease;
}

.form-select:focus,
.form-input:focus {
    outline: none;
    border-color: var(--oro-fiorentino);
    box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
}

.input-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.input-suffix {
    color: var(--grigio-pietra);
    font-weight: 600;
}

.trait-preview {
    background: rgba(45, 80, 22, 0.08);
    border: 1px solid var(--verde-rinascita);
    border-radius: 0.5rem;
    padding: 1rem;
}

.preview-label {
    font-size: 0.75rem;
    color: var(--verde-rinascita);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.preview-card {
    font-size: 1rem;
    color: var(--blu-algoritmo);
}

.preview-type {
    font-weight: 600;
    color: var(--blu-algoritmo);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.5rem;
    border-top: 1px solid #E5E5E5;
    background: #FAFAFA;
}

.btn-cancel {
    padding: 0.625rem 1.5rem;
    background: white;
    border: 1px solid var(--grigio-pietra);
    border-radius: 0.5rem;
    color: var(--grigio-pietra);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #F5F5F5;
    border-color: var(--blu-algoritmo);
    color: var(--blu-algoritmo);
}

.btn-confirm {
    padding: 0.625rem 1.5rem;
    background: var(--verde-rinascita);
    border: none;
    border-radius: 0.5rem;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(45, 80, 22, 0.2);
}

.btn-confirm:hover:not(:disabled) {
    background: #234012;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(45, 80, 22, 0.3);
}

.btn-confirm:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    box-shadow: none;
}

/* Dark mode adjustments for main interface */
@media (prefers-color-scheme: dark) {
    .trait-card {
        background: rgba(255, 255, 255, 0.03);
    }
    
    .category-tab {
        background: rgba(255, 255, 255, 0.03);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .traits-grid {
        grid-template-columns: 1fr;
    }
    
    .category-selector {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
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
        traits: [],
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
    TraitsManager.init = function(egiId) {
        console.log('TraitsManager: Initializing for EGI', egiId);
        
        const container = document.getElementById(`traits-manager-${egiId}`);
        if (!container) {
            console.error('TraitsManager: Container not found');
            return;
        }

        state.currentEgiId = egiId;
        state.readonly = container.dataset.readonly === 'true';
        
        // Cache elements
        elements.container = container;
        elements.grid = container.querySelector('#traits-grid');
        elements.emptyState = container.querySelector('#empty-state');
        elements.addButton = container.querySelector('#add-trait-btn');
        elements.counter = container.querySelector('.traits-count');
        elements.hiddenInput = container.querySelector(`#traits-json-${egiId}`);
        elements.categoriesNav = container.querySelector('#categories-nav');
        
        // Load initial data
        loadCategories().then(() => {
            if (egiId !== 'new') {
                loadExistingTraits(egiId);
            } else {
                updateUI();
            }
        });
    };

    /**
     * Load categories from API
     */
    async function loadCategories() {
        try {
            const response = await fetch('/api/traits/categories');
            const data = await response.json();
            
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
            const response = await fetch(`/api/egis/${egiId}/traits`);
            const data = await response.json();
            
            if (data.success) {
                state.traits = data.traits || [];
                state.isLocked = data.is_locked || false;
                updateUI();
            }
        } catch (error) {
            console.error('Error loading traits:', error);
            updateUI();
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
     * Update UI based on current state
     */
    function updateUI() {
        // Update counter
        if (elements.counter) {
            elements.counter.textContent = state.traits.length;
            const counterContainer = elements.counter.parentElement;
            if (state.traits.length > 25) {
                counterContainer.classList.add('near-limit');
            } else {
                counterContainer.classList.remove('near-limit');
            }
        }

        const saveBtn = document.getElementById('save-traits-btn');
        if (saveBtn) {
            saveBtn.style.display = state.traits.length > 0 ? 'flex' : 'none';
        }

        // Show/hide empty state
        if (elements.emptyState) {
            elements.emptyState.style.display = state.traits.length === 0 ? 'block' : 'none';
        }

        // Show/hide add button
        if (elements.addButton) {
            elements.addButton.style.display = 
                (!state.readonly && !state.isLocked && state.traits.length < 30 && state.traits.length > 0) 
                ? 'flex' : 'none';
        }

        // Render traits grid
        renderTraits();

        // Update hidden input
        if (elements.hiddenInput) {
            elements.hiddenInput.value = JSON.stringify(state.traits);
        }

        // Update category counts
        updateCategoryCounts();
    }

    /**
     * Render traits grid
     */
    function renderTraits() {
        if (!elements.grid) return;

        if (state.traits.length === 0) {
            elements.grid.innerHTML = '';
            return;
        }

        elements.grid.innerHTML = state.traits.map((trait, index) => {
            const categoryColor = getCategoryColor(trait.category_id);
            const isRare = trait.rarity_percentage && trait.rarity_percentage < 10;
            
            return `
                <div class="trait-card ${isRare ? 'rare' : ''}" data-category="${trait.category_id}">
                    <div class="trait-header">
                        <span class="trait-category-badge" style="background-color: ${categoryColor}">
                            ${getCategoryIcon(trait.category_id)}
                        </span>
                        ${!state.readonly && !state.isLocked ? `
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
            const response = await fetch(`/api/traits/types?category_id=${categoryId}`);
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
        
        console.log('Saving traits for EGI:', state.currentEgiId);
        console.log('Traits to save:', state.traits);
        
        try {
            const response = await fetch(`/api/egis/${state.currentEgiId}/traits`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    traits: state.traits
                })
            });
            
            const data = await response.json();
            console.log('Save response:', data);
            
            if (data.success) {
                // Mostra messaggio di successo
                TraitsManager.showNotification('Traits saved successfully!', 'success');
            } else {
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
     * Auto-save quando si aggiunge/rimuove un trait
     */
    const originalAddTrait = TraitsManager.addTrait;
    TraitsManager.addTrait = function() {
        originalAddTrait.call(this);
        // Auto-save dopo aver aggiunto
        setTimeout(() => {
            TraitsManager.saveTraits();
        }, 500);
    };

    const originalRemoveTrait = TraitsManager.removeTrait;
    TraitsManager.removeTrait = function(index) {
        originalRemoveTrait.call(this, index);
        // Auto-save dopo aver rimosso
        setTimeout(() => {
            TraitsManager.saveTraits();
        }, 500);
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
            sort_order: state.traits.length
        };

        state.traits.push(newTrait);
        updateUI();
        TraitsManager.closeModal();
    };

    /**
     * Remove trait
     */
    TraitsManager.removeTrait = function(index) {
        state.traits.splice(index, 1);
        // Reorder
        state.traits.forEach((trait, i) => {
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
            5: '#2D5016'  // Sustainability - Verde
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

    // Auto-initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Find all trait managers on page
        document.querySelectorAll('.egi-traits-manager').forEach(container => {
            const egiId = container.dataset.egiId || 'new';
            TraitsManager.init(egiId);
        });
    });

})();
</script>