{{--
@Oracode OS1: Create Collection Modal Component
🎯 Purpose: Reusable modal for collection creation across guest and authenticated layouts
🧱 Core Logic: Minimal form (name only) with rich UX feedback and FlorenceEGI branding
🛡️ GDPR: Zero data collection beyond authenticated user context
📥 Input: None (triggered by user action)
📤 Output: AJAX form submission to collections.create endpoint
🎨 Design: FlorenceEGI Brand Guidelines compliant with Rinascimento aesthetics

@accessibility Full ARIA support, keyboard navigation, focus management
@responsive Mobile-first design with graceful desktop enhancement
@ux-states Loading, error, success states with meaningful feedback
@color-palette Oro Fiorentino (#D4A574), Verde Rinascita (#2D5016), Blu Algoritmo (#1B365D)
@typography Playfair Display (headings), Source Sans Pro (body)

@since OS1-v1.0
@author Padmin D. Curtis (for Fabio Cherici)
--}}

<!-- Modal Background Overlay -->
<div id="create-collection-modal"
     class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black bg-opacity-75 transition-opacity duration-300 ease-out"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     aria-labelledby="create-collection-modal-title"
     aria-describedby="create-collection-modal-description">

    <!-- Modal Container -->
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl w-[95%] max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
         id="create-collection-modal-container"
         role="document">

        <!-- Modal Header -->
        <header class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full shadow-lg bg-gradient-to-br from-yellow-400 to-yellow-600">
                        <span class="text-xl font-medium text-gray-900 material-symbols-outlined" aria-hidden="true">add</span>
                    </div>
                    <div>
                        <h2 id="create-collection-modal-title"
                            class="text-xl font-bold text-white font-playfair">
                            {{ __('collection.create_new_collection') }}
                        </h2>
                        <p id="create-collection-modal-description"
                           class="text-sm text-gray-400 font-source-sans">
                            {{ __('collection.create_modal_subtitle') }}
                        </p>
                    </div>
                </div>
                <button type="button"
                        id="close-create-collection-modal"
                        class="flex items-center justify-center w-8 h-8 text-gray-400 transition-colors duration-200 rounded-full hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                        aria-label="{{ __('collection.close_modal') }}">
                    <span class="text-lg material-symbols-outlined" aria-hidden="true">close</span>
                </button>
            </div>
        </header>

        <!-- Modal Body -->
        <main class="px-6 py-6">
            <!-- Success State (Hidden by default) -->
            <div id="create-collection-success-state"
                 class="hidden space-y-4 text-center"
                 role="status"
                 aria-live="polite">
                <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-full shadow-lg bg-gradient-to-br from-green-500 to-green-600">
                    <span class="text-2xl text-white material-symbols-outlined" aria-hidden="true">check</span>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-white font-playfair">{{ __('collection.creation_success_title') }}</h3>
                    <p id="success-message" class="text-gray-300 font-source-sans"></p>
                    <p class="text-sm text-gray-400 font-source-sans">{{ __('collection.redirecting_shortly') }}</p>
                </div>
                <div class="w-full h-2 overflow-hidden bg-gray-700 rounded-full">
                    <div id="redirect-progress" class="w-0 h-full transition-all ease-linear bg-gradient-to-r from-green-500 to-green-400 duration-3000"></div>
                </div>
            </div>

            <!-- Form State (Default) -->
            <form id="create-collection-form"
                  class="space-y-6"
                  novalidate>
                @csrf

                <!-- Collection Name Input -->
                <div class="space-y-2">
                    <label for="collection_name"
                           class="block text-sm font-medium text-gray-300 font-source-sans">
                        {{ __('collection.collection_name') }}
                        <span class="ml-1 text-red-400" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="collection_name"
                               name="collection_name"
                               class="w-full px-4 py-3 text-white placeholder-gray-400 transition-colors duration-200 bg-gray-800 border border-gray-600 rounded-lg font-source-sans focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="{{ __('collection.enter_collection_name') }}"
                               maxlength="100"
                               required
                               autocomplete="off"
                               aria-describedby="collection-name-help collection-name-error">

                        <!-- Input Icon -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-xl text-gray-400 material-symbols-outlined" aria-hidden="true">folder</span>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <p id="collection-name-help"
                       class="text-xs text-gray-400 font-source-sans">
                        {{ __('collection.name_help_text') }}
                    </p>

                    <!-- Error Message Container -->
                    <div id="collection-name-error"
                         class="hidden text-sm text-red-400 font-source-sans"
                         role="alert"
                         aria-live="polite">
                    </div>
                </div>

                <!-- Character Counter -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>{{ __('collection.minimum_2_characters') }}</span>
                    <span id="character-counter"
                          class="font-mono"
                          aria-live="polite">
                        <span id="current-length">0</span>/100
                    </span>
                </div>

                <!-- Global Error Message -->
                <div id="global-error-message"
                     class="hidden p-4 border border-red-700 rounded-lg bg-red-900/50"
                     role="alert"
                     aria-live="assertive">
                    <div class="flex items-start space-x-3">
                        <span class="material-symbols-outlined text-red-400 text-xl flex-shrink-0 mt-0.5" aria-hidden="true">error</span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-medium text-red-300 font-source-sans">{{ __('collections.creation_failed') }}</h4>
                            <p id="global-error-text" class="text-sm text-red-200 font-source-sans"></p>
                        </div>
                    </div>
                </div>
            </form>
        </main>

        <!-- Modal Footer -->
        <footer class="px-6 py-4 border-t border-gray-700 bg-gray-800/50 rounded-b-2xl">
            <div class="flex items-center justify-between space-x-3">
                <!-- Collection Stats (if available) -->
                <div class="items-center hidden text-xs text-gray-400 lg:flex font-source-sans">
                    <span class="mr-1 text-sm material-symbols-outlined" aria-hidden="true">info</span>
                    <span id="user-collection-stats">{{ __('collection.loading_stats') }}</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center w-full space-x-3 lg:w-auto">
                    <button type="button"
                            id="cancel-create-collection"
                            class="flex-1 lg:flex-none px-4 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition-colors duration-200 font-source-sans">
                        {{ __('label.cancel') }}
                    </button>

                    <button type="submit"
                            form="create-collection-form"
                            id="submit-create-collection"
                            class="flex-1 lg:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-gray-900 bg-gradient-to-r from-yellow-400 to-yellow-500 border border-transparent rounded-lg hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-200 shadow-lg hover:shadow-xl font-source-sans font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-yellow-400 disabled:hover:to-yellow-500">

                        <!-- Default State -->
                        <span id="submit-text-default" class="flex items-center">
                            <span class="mr-2 text-sm material-symbols-outlined" aria-hidden="true">add</span>
                            {{ __('collection.create_collection') }}
                        </span>

                        <!-- Loading State -->
                        <span id="submit-text-loading" class="items-center hidden">
                            <svg class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('collection.creating') }}
                        </span>
                    </button>
                </div>
            </div>
        </footer>
    </div>
</div>

{{-- OS1 Enhancement: Preload user collection stats if authenticated --}}
@auth
<script type="application/json" id="user-collection-data">
{
    "total_collections": {{ auth()->user()->collections()->count() }},
    "max_allowed": {{ config('egi.max_collections_per_user', 10) }}
}
</script>
@endauth

<style>
/* OS1 Custom Styles for Collection Creation Modal */
#create-collection-modal {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

#create-collection-modal.modal-open {
    display: flex !important;
}

#create-collection-modal.modal-open #create-collection-modal-container {
    transform: scale(1);
    opacity: 1;
}

/* Character counter color states */
#character-counter.text-warning {
    color: #f59e0b;
}

#character-counter.text-danger {
    color: #ef4444;
}

/* Enhanced focus styles for accessibility */
#create-collection-modal input:focus,
#create-collection-modal button:focus {
    box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.3);
}

/* Progress bar animation */
#redirect-progress {
    transition: width 3s linear;
}

/* Responsive typography */
@media (max-width: 640px) {
    #create-collection-modal-title {
        font-size: 1.125rem;
        line-height: 1.75rem;
    }
}
</style>
