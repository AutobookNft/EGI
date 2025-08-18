{{-- resources/views/components/mobile-homepage-toggle.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Mobile Toggle Component)
* @date 2025-01-18
* @purpose Mobile toggle between carousel and list modes
--}}

@props([
'egis' => collect(),
'creators' => collect(),
'collections' => collect(),
'collectors' => collect()
])

<div class="lg:hidden">
    {{-- Toggle Header --}}
    <div class="py-6 bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <h2 class="mb-3 text-2xl font-bold text-white">
                    🎯 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                        {{ __('egi.mobile_toggle.title') }}
                    </span>
                </h2>
                <p class="text-gray-300">
                    {{ __('egi.mobile_toggle.subtitle') }}
                </p>
            </div>

            {{-- View Mode Toggle --}}
            <div class="flex justify-center">
                <div class="flex gap-1 p-1 bg-gray-800 border border-gray-700 rounded-lg">
                    {{-- Carousel Mode Button (Default) --}}
                    <button
                        class="px-4 py-2 text-sm font-medium transition-all duration-200 rounded mobile-view-toggle active"
                        data-view="carousel" aria-label="{{ __('egi.mobile_toggle.carousel_mode') }}">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        🎠 {{ __('egi.mobile_toggle.carousel_mode') }}
                    </button>

                    {{-- List Mode Button --}}
                    <button
                        class="px-4 py-2 text-sm font-medium transition-all duration-200 rounded mobile-view-toggle"
                        data-view="list" aria-label="{{ __('egi.mobile_toggle.list_mode') }}">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        📋 {{ __('egi.mobile_toggle.list_mode') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Container --}}
    <div id="mobile-content-container">
        {{-- Carousel Mode (Default) --}}
        <div id="mobile-carousel-mode" class="mobile-view-content" data-view="carousel">
            <x-homepage-egi-carousel 
                :egis="$egis" 
                :creators="$creators" 
                :collections="$collections" 
                :collectors="$collectors" 
            />
        </div>

        {{-- List Mode --}}
        <div id="mobile-list-mode" class="hidden mobile-view-content" data-view="list">
            <x-homepage-egi-list 
                :egis="$egis" 
                :creators="$creators" 
                :collections="$collections" 
                :collectors="$collectors" 
            />
        </div>
    </div>
</div>

{{-- Mobile Toggle JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtns = document.querySelectorAll('.mobile-view-toggle');
    const contentElements = document.querySelectorAll('.mobile-view-content');
    
    let currentView = 'carousel'; // Default

    // Toggle function
    function switchView(viewMode) {
        // Update buttons
        toggleBtns.forEach(btn => {
            btn.classList.remove('active', 'bg-purple-600', 'text-white');
            btn.classList.add('text-gray-400');
        });
        
        const activeBtn = document.querySelector(`.mobile-view-toggle[data-view="${viewMode}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-purple-600', 'text-white');
            activeBtn.classList.remove('text-gray-400');
        }

        // Update content
        contentElements.forEach(content => {
            content.classList.add('hidden');
        });

        const targetContent = document.querySelector(`.mobile-view-content[data-view="${viewMode}"]`);
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }

        currentView = viewMode;
    }

    // Button click handlers
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            switchView(view);
        });
    });

    // Initialize default state
    const activeBtn = document.querySelector('.mobile-view-toggle.active');
    if (activeBtn) {
        activeBtn.classList.add('bg-purple-600', 'text-white');
        activeBtn.classList.remove('text-gray-400');
    }
});
</script>

{{-- Custom Styles --}}
<style>
.mobile-view-toggle.active {
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
}

.mobile-view-toggle {
    min-width: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-view-content {
    transition: all 0.3s ease;
}
</style>
