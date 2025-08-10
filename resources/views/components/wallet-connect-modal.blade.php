{{-- 📜 Oracode Component: Unified FEGI Wallet Connection Modal --}}
{{-- 🎯 Purpose: Single modal with multiple sections for FEGI flow --}}
{{-- 🛡️ Security: FEGI-key based weak authentication --}}
{{-- 🎨 Design: NFT-themed with glassmorphism and unified UX --}}

<div id="connect-wallet-modal"
    class="fixed inset-0 z-[100] hidden items-start justify-center pt-4 pb-4 px-4 overflow-y-auto sm:items-center sm:pt-0 sm:pb-0 sm:px-0"
    role="dialog" aria-modal="true" aria-labelledby="connect-wallet-title" aria-describedby="connect-wallet-description"
    aria-hidden="true" tabindex="-1">

    {{-- Background NFT style --}}
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/90 via-black/95 to-indigo-900/90 backdrop-blur-sm"
        aria-hidden="true"></div>

    <div class="relative transition-all duration-300 transform scale-95 opacity-0" id="connect-wallet-content"
        role="document">
        <div
            class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl w-11/12 max-w-[520px] border border-white/20 overflow-hidden max-h-[90vh] sm:max-h-none">

            {{-- Header --}}
            <div class="relative p-6 bg-gradient-to-r from-purple-600 to-indigo-600">
                <button id="close-connect-wallet-modal" type="button"
                    class="absolute transition-colors top-4 right-4 text-white/80 hover:text-white"
                    aria-label="{{ __('collection.wallet.wallet_close_modal') }}">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Dynamic Header Content --}}
                <div class="text-center">
                    {{-- Icon container --}}
                    <div class="flex justify-center mb-4" aria-hidden="true">
                        <div id="header-icon"
                            class="flex items-center justify-center w-20 h-20 rounded-full bg-white/20 animate-pulse">
                            {{-- Default: Key icon --}}
                            <svg id="icon-key" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            {{-- Plus icon for create --}}
                            <svg id="icon-plus" class="hidden w-12 h-12 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{-- Check icon for success --}}
                            <svg id="icon-check" class="hidden w-12 h-12 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{-- Warning icon for credentials --}}
                            <svg id="icon-warning" class="hidden w-12 h-12 text-yellow-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Dynamic Title --}}
                    <h2 id="connect-wallet-title" class="text-2xl font-bold text-center text-white">
                        {{-- {{ __('collection.wallet.fegi_connect_title') }} --}}
                    </h2>
                    <p id="connect-wallet-description" class="mt-2 text-center text-white/80">
                        {{-- {{ __('collection.wallet.fegi_modal_subtitle') }} --}}
                    </p>
                </div>
            </div>

            {{-- Main Content Area --}}
            <div class="p-6 overflow-y-auto max-h-[50vh] sm:max-h-none">

                {{-- SECTION 1: Mode Selection (Initial State) --}}
                <div id="section-mode-selection" class="modal-section">
                    <div class="mb-6 text-center">
                        <p class="text-sm text-white/90">
                            {{ __('collection.wallet.fegi_choose_option') }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Existing FEGI Key Option --}}
                        <button id="btn-use-existing-fegi" type="button"
                            class="w-full p-4 transition-all border rounded-lg border-white/20 bg-white/5 hover:bg-white/10 group">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <svg class="w-8 h-8 text-blue-400 group-hover:text-blue-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1 1 21 9z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-white">{{ __('collection.wallet.fegi_use_existing') }}
                                    </h3>
                                    <p class="text-sm text-white/70">{{ __('collection.wallet.fegi_use_existing_desc')
                                        }}</p>
                                </div>
                            </div>
                        </button>

                        {{-- Create New Account Option --}}
                        <button id="btn-create-new-account" type="button"
                            class="w-full p-4 transition-all border rounded-lg border-emerald-500/30 bg-emerald-900/20 hover:bg-emerald-800/30 group">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <svg class="w-8 h-8 text-emerald-400 group-hover:text-emerald-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-white">{{ __('collection.wallet.fegi_create_new') }}
                                    </h3>
                                    <p class="text-sm text-white/70">{{ __('collection.wallet.fegi_create_new_desc') }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- SECTION 2: FEGI Input Form --}}
                <div id="section-fegi-input" class="hidden modal-section">
                    <form id="fegi-input-form" method="POST" action="{{ route('wallet.connect') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="fegi_key_input" class="block mb-2 text-sm font-medium text-white/90">
                                {{ __('collection.wallet.fegi_key_label') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                    aria-hidden="true">
                                    <svg class="w-5 h-5 text-purple-400 group-focus-within:text-purple-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1 1 21 9z" />
                                    </svg>
                                </div>
                                <input type="text" name="fegi_key" id="fegi_key_input" required
                                    pattern="FEGI-\d{4}-[A-Z0-9]{15}" maxlength="25"
                                    class="w-full py-3 pl-10 pr-3 font-mono text-white transition border rounded-lg bg-white/10 border-purple-500/30 placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="FEGI-2025-ABC123DEF456GHI" aria-describedby="fegi-key-help"
                                    aria-required="true">
                            </div>
                            <p id="fegi-key-help" class="mt-2 text-xs text-white/60">
                                {{ __('collection.wallet.fegi_key_help') }}
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button id="btn-back-to-selection" type="button"
                                class="flex-1 px-4 py-3 text-sm font-medium transition-all border rounded-lg text-white/70 hover:text-white border-white/20 hover:bg-white/10">
                                ← {{ __('collection.wallet.fegi_back_to_options') }}
                            </button>
                            <button type="submit" id="fegi-submit-button"
                                class="flex-1 px-6 py-3 font-semibold text-white transition-all duration-300 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="fegi-submit-text">{{ __('collection.wallet.fegi_connect_button') }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- SECTION 3: Create Account Loading --}}
                <div id="section-create-loading" class="hidden modal-section">
                    <div class="py-8 text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-emerald-500/20">
                            <svg class="w-8 h-8 text-emerald-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="m15.84 10.02l1.42-1.42a6 6 0 00-8.52 0l1.42 1.42a4 4 0 015.68 0zM12 6a6 6 0 016 6h-2a4 4 0 00-4-4V6z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">{{
                            __('collection.wallet.fegi_creating_account') }}</h3>
                        <p class="text-sm text-white/70">{{ __('collection.wallet.fegi_creating_account_desc') }}</p>
                    </div>
                </div>

                {{-- SECTION 4: Credentials Display --}}
                <div id="section-credentials-display" class="hidden modal-section">
                    <div class="mb-6 text-center">
                        <h3 class="mb-2 text-xl font-semibold text-white">{{
                            __('collection.wallet.fegi_credentials_generated_title') }}</h3>
                        <p class="text-sm text-white/70">{{ __('collection.wallet.fegi_credentials_success_desc') }}</p>
                    </div>

                    {{-- Algorand Address Display --}}
                    <div class="p-4 mb-4 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20">
                        <p class="mb-2 text-xs tracking-wider uppercase text-white/60">
                            {{ __('collection.wallet.fegi_your_algorand_address') }}
                        </p>
                        <p id="display-algorand-address"
                            class="font-mono text-sm font-bold text-blue-300 break-all select-all"></p>
                    </div>

                    {{-- FEGI Key Display --}}
                    <div class="p-4 mb-6 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20">
                        <p class="mb-2 text-xs tracking-wider uppercase text-white/60">
                            {{ __('collection.wallet.fegi_your_fegi_key') }}
                        </p>
                        <p id="display-fegi-key"
                            class="font-mono text-lg font-bold break-all select-all text-emerald-300"></p>
                    </div>

                    {{-- Warning Message --}}
                    <div class="p-4 mb-6 border rounded-lg bg-red-900/30 border-red-400/30 backdrop-blur-sm">
                        <p class="text-sm text-red-300">
                            <strong>{{ __('collection.wallet.fegi_credentials_warning_title') }}</strong><br>
                            {{ __('collection.wallet.fegi_credentials_warning_text') }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-3">
                        <button id="btn-copy-credentials" type="button"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition border rounded-md shadow-sm border-purple-400/30 bg-white/10 backdrop-blur-sm hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-4M16 3h4v4m0-4L10 13" />
                            </svg>
                            <span id="copy-credentials-text">{{ __('collection.wallet.fegi_copy_credentials') }}</span>
                        </button>

                        <label class="flex items-center text-white">
                            <input type="checkbox" id="save-fegi-locally"
                                class="text-purple-600 rounded border-purple-400/30 focus:ring-purple-500 bg-white/10">
                            <span class="ml-2 text-sm text-white/80">
                                {{ __('collection.wallet.fegi_save_locally') }}
                            </span>
                        </label>

                        <button id="btn-confirm-credentials-saved" type="button"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            {{ __('collection.wallet.fegi_confirm_saved') }}
                        </button>
                    </div>
                </div>

                {{-- Error container (sempre visibile se necessario) --}}
                <div id="wallet-error-container" class="hidden mt-4" role="alert" aria-live="polite">
                    <div class="p-4 border rounded-lg bg-red-900/30 border-red-400/30 backdrop-blur-sm">
                        <p id="wallet-error-message" class="text-sm text-red-300"></p>
                    </div>
                </div>

                {{-- Registration link --}}
                <p class="mt-6 text-xs text-center text-white/60">
                    {{ __('collection.wallet.fegi_weak_auth_info') }}
                    <a href="{{ route('register') }}"
                        class="text-purple-400 underline transition-colors hover:text-purple-300">
                        {{ __('collection.wallet.fegi_register_full') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Transizioni smooth per le sezioni */
    .modal-section {
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .modal-section.hidden {
        opacity: 0;
        transform: translateY(10px);
        pointer-events: none;
    }

    .modal-section:not(.hidden) {
        opacity: 1;
        transform: translateY(0);
    }

    /* Animazione icone header */
    #header-icon svg {
        transition: all 0.3s ease-in-out;
    }
</style>
