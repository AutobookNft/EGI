{{--
    Trait Detail Modal Component
    Displays trait information with image upload capability

    @package FlorenceEGI\Traits\Components
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 1.0.0 (Trait Image System)
    @date 2025-09-01
--}}

@props(['trait'])

<div id="trait-modal-{{ $trait->id }}" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 trait-modal" data-trait-id="{{ $trait->id }}" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('label.trait_modal.trait_details') }}
            </h2>
            <button type="button" class="text-2xl text-gray-400 trait-modal-close hover:text-gray-600">
                &times;
            </button>
        </div>

        {{-- Content --}}
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Image Section --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">{{ __('label.trait_modal.trait_image') }}</h3>

                    {{-- Current Image Display --}}
                    <div class="p-4 text-center border-2 border-gray-300 border-dashed rounded-lg trait-upload-area">
                        <div id="trait-image-preview-{{ $trait->id }}" class="mb-4">
                            @if($trait->image_url)
                                <img src="{{ $trait->modal_image_url }}"
                                     alt="{{ $trait->image_alt_text ?? $trait->name }}"
                                     class="object-contain h-auto max-w-full mx-auto rounded-lg max-h-64">
                            @else
                                <div class="py-8 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2">{{ __('label.trait_modal.no_image_uploaded') }}</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ __('label.trait_modal.drag_drop_files') }}</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ __('label.trait_modal.supported_formats') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Upload Form --}}
                        <form id="trait-image-form-{{ $trait->id }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="trait_id" value="{{ $trait->id }}">

                            <div class="flex flex-col space-y-2">
                                <label for="trait-image-input-{{ $trait->id }}" class="block w-full px-4 py-3 text-sm font-medium text-center text-blue-600 transition-colors border-2 border-blue-300 border-dashed rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-400">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    {{ __('label.trait_modal.choose_files') }}
                                    <input type="file"
                                           name="trait_image"
                                           id="trait-image-input-{{ $trait->id }}"
                                           class="hidden"
                                           accept="image/jpeg,image/png,image/webp,image/gif">
                                </label>

                                <input type="text"
                                       name="image_alt_text"
                                       placeholder="{{ __('label.trait_modal.alt_text') }}"
                                       value="{{ $trait->image_alt_text }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

                                <textarea name="image_description"
                                          placeholder="{{ __('label.trait_modal.image_description') }}"
                                          rows="2"
                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $trait->image_description }}</textarea>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit"
                                        id="trait-upload-btn-{{ $trait->id }}"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                                    {{ __('label.trait_modal.upload_image') }}
                                </button>

                                @if($trait->image_url)
                                    <button type="button"
                                            id="trait-delete-image-btn-{{ $trait->id }}"
                                            class="px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-md hover:bg-red-700">
                                        {{ __('label.trait_modal.delete_image') }}
                                    </button>
                                @endif
                            </div>
                        </form>

                        {{-- Upload Progress --}}
                        <div id="trait-upload-progress-{{ $trait->id }}" class="hidden mt-4">
                            <div class="h-2 bg-gray-200 rounded-full">
                                <div class="h-2 transition-all duration-300 bg-blue-600 rounded-full" style="width: 0%"></div>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ __('traits.uploading') }}...</p>
                        </div>
                    </div>
                </div>

                {{-- Trait Info Section - IDENTICA a traits-viewer --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">{{ __('label.trait_modal.trait_information') }}</h3>
                    
                    {{-- REPLICA ESATTA della trait-card dal traits-viewer --}}
                    @php
                        // Carica colore e icona dal database - IDENTICO a traits-viewer
                        $category = $trait->category;
                        $categoryColor = $category ? $category->color : '#6B6B6B';
                        $categoryIcon = $category ? $category->icon : '🏷️';
                    @endphp

                    <div class="trait-card readonly"
                         style="position: relative; cursor: default; background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="trait-header readonly">
                            <span class="trait-category-badge" style="background-color: {{ $categoryColor }}; color: white; padding: 0.5rem; border-radius: 50%; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                                {{ $categoryIcon }}
                            </span>
                            @if($trait->image_url)
                                <span class="trait-image-indicator"
                                      style="position: absolute;
                                             top: 0.25rem;
                                             left: 0.25rem;
                                             background: rgba(34, 197, 94, 0.9);
                                             color: white;
                                             border-radius: 50%;
                                             width: 1.25rem;
                                             height: 1.25rem;
                                             display: flex;
                                             align-items: center;
                                             justify-content: center;
                                             font-size: 0.75rem;
                                             z-index: 100;"
                                      title="Ha un'immagine">
                                    📷
                                </span>
                            @endif
                        </div>
                        <div class="trait-content">
                            <div class="trait-type" style="color: #374151; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">{{ $trait->traitType ? $trait->traitType->name : 'Unknown' }}</div>
                            <div class="trait-value" style="color: #111827; font-weight: 700; font-size: 1.125rem;">
                                <span>{{ $trait->display_value ?? $trait->value }}</span>
                                @if($trait->traitType && $trait->traitType->unit)
                                    <span class="trait-unit" style="color: #6b7280; font-weight: 500; font-size: 1rem;">{{ $trait->traitType->unit }}</span>
                                @endif
                            </div>

                            {{-- Barra di rarità - IDENTICA a traits-viewer --}}
                            @if(isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                @php
                                    // Determina la classe di rarità in base alla percentuale - IDENTICO
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

                                    // Formula IDENTICA a traits-viewer
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
                                    <div class="rarity-bar" style="background-color: #e5e7eb; border-radius: 0.25rem; height: 0.5rem; overflow: hidden;">
                                        <div class="rarity-fill {{ $rarityClass }}" style="width: {{ number_format($barWidth, 1) }}%; height: 100%; border-radius: 0.25rem;"></div>
                                    </div>
                                    <span class="rarity-text" style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ number_format($trait->rarity_percentage, 1) }}% have this</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end p-6 border-t border-gray-200">
            <button type="button" class="px-6 py-2 text-white transition-colors bg-gray-500 rounded-md trait-modal-close hover:bg-gray-600">
                {{ __('label.trait_modal.close') }}
            </button>
        </div>
    </div>
</div>

{{-- Toast Notifications per questo modale --}}
<div id="trait-toast-container-{{ $trait->id }}" class="fixed top-4 right-4 z-[60] space-y-2"></div>

{{-- Include CSS e JavaScript con Vite --}}
@once
{{-- @vite(['resources/css/trait-detail-modal.css'])
@vite(['resources/js/trait-image-manager.js']) --}}

{{-- Pass translations to JavaScript --}}
<script>
window.traitTranslations = window.traitTranslations || {};
Object.assign(window.traitTranslations, {
    upload_success: '{{ __('traits.upload_success') }}',
    upload_error: '{{ __('traits.upload_error') }}',
    delete_success: '{{ __('traits.delete_success') }}',
    delete_error: '{{ __('traits.delete_error') }}',
    confirm_delete: '{{ __('traits.confirm_delete') }}',
    uploading: '{{ __('traits.uploading') }}',
    file_too_large: '{{ __('traits.file_too_large') }}',
    invalid_file_type: '{{ __('traits.invalid_file_type') }}',
    preview_selected: '{{ __('traits.preview_selected') }}'
});
</script>
@endonce
