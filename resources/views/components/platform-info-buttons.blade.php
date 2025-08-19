{{-- resources/views/components/platform-info-buttons.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Platform Info Buttons)
* @date 2025-01-19
* @purpose Bottoni informativi stile OpenSea per guidare l'utente
--}}

<div class="w-full py-4 bg-gray-900">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-3 sm:gap-4">

            {{-- Bottone EGI ? --}}
            <a href="{{ route('info.egi') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-blue-400">EGI</span>
                <span class="ml-1 text-gray-300">?</span>
            </a>

            {{-- Bottone Attivare ? --}}
            <a href="{{ route('info.attivare') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-400">Attivare</span>
                <span class="ml-1 text-gray-300">?</span>
            </a>

            {{-- Bottone Impatto ? --}}
            <a href="{{ route('info.impatto') }}"
                class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="text-purple-400">Impatto</span>
                <span class="ml-1 text-gray-300">?</span>
            </a>

        </div>
    </div>
</div>
