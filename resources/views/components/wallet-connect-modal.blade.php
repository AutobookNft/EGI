{{-- 📜 Oracode Component: NFT-Style Wallet Connection Modal with Secret Link --}}
{{-- 🎯 Purpose: Enable weak authentication through wallet address + secret key --}}
{{-- 🛡️ Security: Two-factor approach for secure wallet connection --}}
{{-- 🎨 Design: NFT-themed with glassmorphism and gradients --}}

<div id="connect-wallet-modal"
     class="fixed inset-0 z-[100] flex items-center justify-center hidden"
     role="dialog"
     aria-modal="true"
     aria-labelledby="connect-wallet-title"
     aria-describedby="connect-wallet-description"
     aria-hidden="true"
     tabindex="-1">

    {{-- Background NFT style --}}
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/90 via-black/95 to-indigo-900/90 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="relative transition-all duration-300 transform scale-95 opacity-0"
         id="connect-wallet-content"
         role="document">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl w-11/12 md:w-[480px] border border-white/20 overflow-hidden">

            {{-- Header --}}
            <div class="relative p-6 bg-gradient-to-r from-purple-600 to-indigo-600">
                <button id="close-connect-wallet-modal"
                        type="button"
                        class="absolute transition-colors top-4 right-4 text-white/80 hover:text-white"
                        aria-label="{{ __('collection.wallet.wallet_close_modal') }}">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Icona animata --}}
                <div class="flex justify-center mb-4" aria-hidden="true">
                    <div class="flex items-center justify-center w-20 h-20 rounded-full bg-white/20 animate-pulse">
                        <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>

                <h2 id="connect-wallet-title"
                    class="text-2xl font-bold text-center text-white">
                    {{ __('collection.wallet.wallet_connect_title') }}
                </h2>
                <p id="connect-wallet-description"
                   class="mt-2 text-center text-white/80">
                    {{ __('collection.wallet.wallet_modal_subtitle') }}
                </p>
            </div>

            {{-- Form --}}
            <form id="connect-wallet-form"
                  method="POST"
                  action="{{ route('wallet.connect') }}"
                  class="p-6"
                  aria-labelledby="connect-wallet-title">
                @csrf

                {{-- Wallet address --}}
                <div class="mb-6">
                    <label for="wallet_address"
                           class="block mb-2 text-sm font-medium text-white/90">
                        {{ __('collection.wallet.wallet_address_label') }}
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                             aria-hidden="true">
                            <svg class="w-5 h-5 text-purple-400 group-focus-within:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input type="text"
                               name="wallet_address"
                               id="wallet_address"
                               required
                               maxlength="58"
                               class="w-full py-3 pl-10 pr-3 font-mono text-white transition border rounded-lg bg-white/10 border-purple-500/30 placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="{{ __('collection.wallet.wallet_address_placeholder') }}"
                               aria-describedby="wallet-address-help wallet-error-message"
                               aria-required="true">
                    </div>
                    <p id="wallet-address-help"
                       class="mt-2 text-xs text-white/60">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            {{ __('collection.wallet.wallet_address_help') }}
                        </span>
                    </p>
                </div>

                {{-- Secret field (hidden) --}}
                <div id="secret-field" class="hidden mb-6">
                    <label for="secret-input"
                           class="block mb-2 text-sm font-medium text-white/90">
                        {{ __('collection.wallet.wallet_secret_label') }}
                    </label>
                    <input type="text"
                           name="secret"
                           id="secret-input"
                           maxlength="50"
                           class="w-full px-3 py-3 font-mono text-white transition border rounded-lg bg-white/10 border-purple-500/30 placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="{{ __('collection.wallet.wallet_secret_placeholder') }}"
                           aria-describedby="secret-help secret-error-message">
                    <p id="secret-help"
                       class="mt-2 text-xs text-white/60">
                        {{ __('collection.wallet.wallet_secret_help') }}
                    </p>
                </div>

                {{-- Error container --}}
                <div id="wallet-error-container"
                     class="hidden mb-4"
                     role="alert"
                     aria-live="polite">
                    <p id="wallet-error-message"
                       class="text-sm text-red-400"></p>
                </div>

                {{-- Submit button --}}
                <button type="submit"
                        id="connect-wallet-submit"
                        class="relative w-full px-6 py-3 overflow-hidden font-semibold text-white transition-all duration-300 rounded-lg group bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-busy="false"
                        aria-disabled="false">
                    <svg class="hidden w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="connect-wallet-button-text">{{ __('collection.wallet.wallet_connect_button') }}</span>
                </button>

                {{-- Registration link --}}
                <p class="mt-4 text-xs text-center text-white/60">
                    {{ __('collection.wallet.wallet_weak_auth_info') }}
                    <a href="{{ route('register') }}"
                       class="text-purple-400 underline transition-colors hover:text-purple-300">
                        {{ __('collection.wallet.wallet_register_full') }}
                    </a>
                </p>
            </form>
        </div>
    </div>
</div>

{{-- Secret Display Modal (for new users) --}}
<div id="secret-display-modal"
     class="fixed inset-0 z-[101] flex items-center justify-center hidden"
     role="dialog"
     aria-modal="true"
     aria-labelledby="secret-display-title">

    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/90 via-black/95 to-indigo-900/90 backdrop-blur-sm"
         aria-hidden="true"></div>

    <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl w-11/12 md:w-[480px] border border-white/20 overflow-hidden p-6">

        {{-- Important Icon --}}
        <div class="flex justify-center mb-4">
            <div class="p-4 rounded-full bg-yellow-400/20 backdrop-blur-sm">
                <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h3 id="secret-display-title"
            class="mb-4 text-2xl font-bold text-center text-white">
            {{ __('collection.wallet.wallet_secret_generated_title') }}
        </h3>

        {{-- Secret Display --}}
        <div class="p-4 mb-4 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20">
            <p class="mb-2 text-xs tracking-wider uppercase text-white/60">
                {{ __('collection.wallet.wallet_your_secret_key') }}
            </p>
            <p id="generated-secret"
               class="font-mono text-lg font-bold text-white break-all select-all"></p>
        </div>

        {{-- Warning Message --}}
        <div class="p-4 mb-6 border rounded-lg bg-red-900/30 border-red-400/30 backdrop-blur-sm">
            <p class="text-sm text-red-300">
                <strong>{{ __('collection.wallet.wallet_secret_warning_title') }}</strong><br>
                {{ __('collection.wallet.wallet_secret_warning_text') }}
            </p>
        </div>

        {{-- Actions --}}
        <div class="space-y-3">
            <button id="copy-secret-button"
                    type="button"
                    class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition border rounded-md shadow-sm border-purple-400/30 bg-white/10 backdrop-blur-sm hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-4M16 3h4v4m0-4L10 13"/>
                </svg>
                {{ __('collection.wallet.wallet_copy_secret') }}
            </button>

            <label class="flex items-center text-white">
                <input type="checkbox"
                       id="save-secret-locally"
                       class="text-purple-600 rounded border-purple-400/30 focus:ring-purple-500 bg-white/10">
                <span class="ml-2 text-sm text-white/80">
                    {{ __('collection.wallet.wallet_save_secret_locally') }}
                </span>
            </label>

            <button id="confirm-secret-saved"
                    type="button"
                    class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('collection.wallet.wallet_confirm_saved') }}
            </button>
        </div>
    </div>
</div>
