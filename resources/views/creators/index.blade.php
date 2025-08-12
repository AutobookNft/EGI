{{-- resources/views/creators/index.blade.php --}}
@extends('layouts.app')

@section('title', __('guest_home.featured_creators_title'))
@section('meta_description', 'Scopri tutti i creator di talento dell\'ecosistema FlorenceEGI')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    {{-- Hero Section --}}
    <section class="py-16 bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600">
        <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="mb-6 text-4xl font-bold text-white md:text-6xl">
                    🎨 <span class="text-transparent bg-gradient-to-r from-yellow-300 to-pink-300 bg-clip-text">
                        Creator in Evidenza
                    </span>
                </h1>
                <p class="max-w-3xl mx-auto mb-8 text-xl text-gray-100">
                    Scopri i talentuosi artisti e creator che stanno plasmando il futuro dell'arte digitale
                    nell'ecosistema FlorenceEGI
                </p>

                {{-- Statistics --}}
                <div class="flex flex-wrap justify-center gap-8 mt-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">{{ getCreatorsCount() }}</div>
                        <div class="text-gray-200">Creator Attivi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-pink-300">{{ getCollectionsCount() }}</div>
                        <div class="text-gray-200">Collezioni Pubblicate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-300">{{ getActivatorsCount() }}</div>
                        <div class="text-gray-200">Attivatori Ecosistema</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-16">
        <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Coming Soon Message --}}
            <div class="max-w-4xl mx-auto text-center">
                <div class="p-8 bg-gray-800 border border-gray-700 rounded-2xl shadow-xl">
                    <h2 class="mb-6 text-3xl font-bold text-white">
                        🚀 In Arrivo
                    </h2>
                    <p class="mb-8 text-xl text-gray-300">
                        La pagina completa dei creator è in fase di sviluppo.
                        Presto potrai esplorare tutti i profili, le opere e le collezioni
                        dei nostri talentuosi artisti.
                    </p>

                    {{-- Call to Action --}}
                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-6 py-3 text-lg font-medium text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            ← Torna alla Home
                        </a>
                        <a href="{{ route('collections.index') }}"
                            class="inline-flex items-center px-6 py-3 text-lg font-medium text-blue-400 transition-all duration-200 bg-transparent border border-blue-400 rounded-lg hover:bg-blue-400 hover:text-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Esplora Collezioni →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Future Features Preview --}}
            <div class="grid gap-8 mt-16 md:grid-cols-3">
                <div class="p-6 text-center bg-gray-800 rounded-xl">
                    <div class="mb-4 text-4xl">👤</div>
                    <h3 class="mb-2 text-xl font-semibold text-white">Profili Creator</h3>
                    <p class="text-gray-400">Esplora i profili dettagliati degli artisti con biografie, portfolio e
                        statistiche</p>
                </div>
                <div class="p-6 text-center bg-gray-800 rounded-xl">
                    <div class="mb-4 text-4xl">🎨</div>
                    <h3 class="mb-2 text-xl font-semibold text-white">Opere in Evidenza</h3>
                    <p class="text-gray-400">Scopri le opere più apprezzate e le ultime creazioni degli artisti</p>
                </div>
                <div class="p-6 text-center bg-gray-800 rounded-xl">
                    <div class="mb-4 text-4xl">📊</div>
                    <h3 class="mb-2 text-xl font-semibold text-white">Statistiche Avanzate</h3>
                    <p class="text-gray-400">Analizza performance, vendite e impatto ambientale delle opere</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection