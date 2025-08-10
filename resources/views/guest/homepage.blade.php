{{-- resources/views/guest/homepage.blade.php --}}
{{--
* @package App\Views
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Guest Homepage with Collector Carousel)
* @date 2025-08-10
* @purpose Marketing-focused homepage for non-authenticated users
--}}

@extends('layouts.app')

@section('title', 'Florence EGI - La Piattaforma per Creator e Collezionisti')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">

    {{-- Hero Section --}}
    <section class="relative py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    Florence <span
                        class="bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">EGI</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto mb-8">
                    La piattaforma che connette <strong class="text-blue-400">Creator</strong> e <strong
                        class="text-purple-400">Collezionisti</strong>
                    in un ecosistema innovativo di arte digitale e investimenti culturali
                </p>
            </div>

            {{-- Platform Statistics --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mb-12">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-blue-400">{{ number_format($platformStats['total_creators']) }}
                    </div>
                    <div class="text-sm text-gray-300 mt-1">Creator Attivi</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-purple-400">{{ number_format($platformStats['total_collectors'])
                        }}</div>
                    <div class="text-sm text-gray-300 mt-1">Collezionisti</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-green-400">{{ number_format($platformStats['total_egis']) }}
                    </div>
                    <div class="text-sm text-gray-300 mt-1">EGI Disponibili</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <div class="text-3xl font-bold text-yellow-400">{{
                        number_format($platformStats['total_collections']) }}</div>
                    <div class="text-sm text-gray-300 mt-1">Collezioni</div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                    </svg>
                    Diventa un Creator
                </a>
                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                    </svg>
                    Inizia a Collezionare
                </a>
            </div>
        </div>
    </section>

    {{-- Top Collectors Carousel --}}
    <x-collector-carousel :collectors="$topCollectors" />

    {{-- Features Section --}}
    <section class="py-20 bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">
                    Come Funziona Florence EGI
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Un ecosistema innovativo dove creatività e investimento si incontrano
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                {{-- For Creators --}}
                <div
                    class="bg-gradient-to-br from-blue-600/10 to-blue-800/10 p-8 rounded-2xl border border-blue-500/20">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Per i Creator</h3>
                    </div>
                    <ul class="space-y-4 text-gray-300">
                        <li class="flex items-start">
                            <span class="text-blue-400 mr-3">🎨</span>
                            <span>Crea le tue collezioni digitali uniche</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-400 mr-3">💰</span>
                            <span>Ottieni finanziamenti per i tuoi progetti</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-400 mr-3">📈</span>
                            <span>Monitora il successo delle tue opere</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-400 mr-3">🌟</span>
                            <span>Costruisci la tua community di supporter</span>
                        </li>
                    </ul>
                </div>

                {{-- For Collectors --}}
                <div
                    class="bg-gradient-to-br from-purple-600/10 to-purple-800/10 p-8 rounded-2xl border border-purple-500/20">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Per i Collezionisti</h3>
                    </div>
                    <ul class="space-y-4 text-gray-300">
                        <li class="flex items-start">
                            <span class="text-purple-400 mr-3">🎯</span>
                            <span>Investi in opere d'arte digitali innovative</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-purple-400 mr-3">🏆</span>
                            <span>Supporta i creator che ami e ottieni visibilità</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-purple-400 mr-3">💎</span>
                            <span>Costruisci la tua collezione esclusiva</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-purple-400 mr-3">📊</span>
                            <span>Entra nella classifica dei top investitori</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA Section --}}
    <section class="py-20 bg-gradient-to-r from-blue-600/20 to-purple-600/20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Pronto a Iniziare la Tua Avventura?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Unisciti alla community di creator e collezionisti più innovativa d'Italia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                    Registrati Gratuitamente
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
            <p class="text-sm text-gray-400 mt-4">
                La registrazione è completamente gratuita e senza impegno
            </p>
        </div>
    </section>
</div>
@endsection
