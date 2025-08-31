{{-- resources/views/components/egi/traits-viewer.blade.php --}}
{{-- 
    EGI Traits Viewer Component - READONLY
    Solo visualizzazione dei traits esistenti renderizzati con PHP
--}}
@props([
    'egi' => null
])

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

<div class="egi-traits-viewer" 
     id="traits-viewer-{{ $egi ? $egi->id : 'new' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}"
     style="position: relative !important; order: -1 !important; margin-top: 0 !important; margin-bottom: 2rem !important;">
    
    {{-- Header con counter --}}
    <div class="traits-header">
        <h3 class="traits-title">
            <span class="traits-icon">🎯</span>
            {{ __('Tratti e Attributi') }}
        </h3>
        <div class="traits-meta">
            <span class="trait-counter">
                <span class="traits-count">{{ $egi && $egi->traits ? $egi->traits->count() : 0 }}</span>/30
            </span>
        </div>
    </div>

    {{-- Traits Grid (readonly) renderizzato con PHP --}}
    <div class="traits-list readonly">
        <div class="traits-grid" id="traits-grid-viewer">
            @if($egi && $egi->traits && $egi->traits->count() > 0)
                @foreach($egi->traits as $trait)
                    @php
                        // Definisci i colori delle categorie
                        $categoryColors = [
                            1 => '#D4A574', // Materials - Oro
                            2 => '#8E44AD', // Visual - Viola  
                            3 => '#1B365D', // Dimensions - Blu
                            4 => '#E67E22', // Special - Arancio
                            5 => '#2D5016', // Sustainability - Verde
                            6 => '#8B4513'  // Cultural - Marrone
                        ];
                        
                        // Definisci le icone delle categorie
                        $categoryIcons = [
                            1 => '📦', // Materials
                            2 => '🎨', // Visual
                            3 => '📐', // Dimensions  
                            4 => '⚡', // Special
                            5 => '🌿', // Sustainability
                            6 => '🏛️'  // Cultural
                        ];
                        
                        $categoryColor = $categoryColors[$trait->category_id] ?? '#6B6B6B';
                        $categoryIcon = $categoryIcons[$trait->category_id] ?? '🏷️';
                    @endphp
                    
                    <div class="trait-card readonly" data-category="{{ $trait->category_id }}">
                        <div class="trait-header readonly">
                            <span class="trait-category-badge" style="background-color: {{ $categoryColor }}">
                                {{ $categoryIcon }}
                            </span>
                        </div>
                        <div class="trait-content">
                            <div class="trait-type">{{ $trait->traitType ? $trait->traitType->name : 'Unknown' }}</div>
                            <div class="trait-value">
                                <span>{{ $trait->display_value ?? $trait->value }}</span>
                                @if($trait->traitType && $trait->traitType->unit)
                                    <span class="trait-unit">{{ $trait->traitType->unit }}</span>
                                @endif
                            </div>
                            
                            {{-- Barra di rarità --}}
                            @if(isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                @php
                                    // Determina la classe di rarità in base alla percentuale
                                    if ($trait->rarity_percentage >= 70) {
                                        $rarityClass = 'common';
                                    } elseif ($trait->rarity_percentage >= 40) {
                                        $rarityClass = 'uncommon';
                                    } elseif ($trait->rarity_percentage >= 20) {
                                        $rarityClass = 'rare';
                                    } elseif ($trait->rarity_percentage >= 10) {
                                        $rarityClass = 'epic';
                                    } elseif ($trait->rarity_percentage >= 5) {
                                        $rarityClass = 'legendary';
                                    } else {
                                        $rarityClass = 'mythic';
                                    }
                                    
                                    // Formula semplice e diretta: più è raro, più la barra è lunga
                                    // Invertiamo direttamente la percentuale per creare differenze evidenti
                                    if ($trait->rarity_percentage <= 5) {
                                        $barWidth = 90; // Leggendario/Mitico - barra quasi piena
                                    } elseif ($trait->rarity_percentage <= 10) {
                                        $barWidth = 75; // Epico
                                    } elseif ($trait->rarity_percentage <= 20) {
                                        $barWidth = 60; // Raro
                                    } elseif ($trait->rarity_percentage <= 40) {
                                        $barWidth = 40; // Poco comune
                                    } elseif ($trait->rarity_percentage <= 70) {
                                        $barWidth = 25; // Comune
                                    } else {
                                        $barWidth = 10; // Molto comune - barra quasi vuota
                                    }
                                @endphp
                                <div class="trait-rarity">
                                    <div class="rarity-bar">
                                        <div class="rarity-fill {{ $rarityClass }}" style="width: {{ number_format($barWidth, 1) }}%"></div>
                                    </div>
                                    <span class="rarity-text">{{ number_format($trait->rarity_percentage, 1) }}% have this</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state-viewer" style="text-align: center; padding: 2rem; color: #666; font-style: italic;">
                    Nessun tratto definito.
                </div>
            @endif
        </div>
    </div>
</div>
