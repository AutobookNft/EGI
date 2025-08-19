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
        <div class="flex gap-3 overflow-x-auto sm:gap-4 scrollbar-hide snap-x snap-mandatory">
            <div class="flex gap-3 px-2 sm:gap-4">

                {{-- Bottone FlorenceEgi ? --}}
                <a href="{{ route('info.florence-egi') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <span class="text-pink-400">FlorenceEgi</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone EGI ? --}}
                <a href="{{ route('info.egi') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-blue-400">EGI</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Attivare ? --}}
                <a href="{{ route('info.attivare') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-400">Attivare</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Impatto ? --}}
                <a href="{{ route('info.impatto') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="text-purple-400">Impatto</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Creator ? --}}
                <a href="{{ route('info.creator') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                    <span class="text-amber-400">Creator</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Attivatori ? --}}
                <a href="{{ route('info.attivatori') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 011-1h1a2 2 0 100-4H7a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z">
                        </path>
                    </svg>
                    <span class="text-emerald-400">Attivatori</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Mecenati ? --}}
                <a href="{{ route('info.mecenati') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    <span class="text-rose-400">Mecenati</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Aziende ? --}}
                <a href="{{ route('info.aziende') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="text-indigo-400">Aziende</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

                {{-- Bottone Trader-pro ? --}}
                <a href="{{ route('info.trader-pro') }}"
                    class="flex items-center flex-shrink-0 px-4 py-2 text-xs font-medium text-white transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-gray-900 snap-start">
                    <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="text-cyan-400">Trader-pro</span>
                    <span class="ml-1 text-gray-300">?</span>
                </a>

            </div>
        </div>
    </div>
</div>

{{-- CSS per nascondere scrollbar --}}
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
</style>
