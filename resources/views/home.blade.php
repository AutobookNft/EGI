{{-- resources/views/home.blade.php --}}
{{-- 📜 Oracode View: FlorenceEGI Homepage --}}
{{-- Main landing page featuring hero section with overlayed featured collections, latest additions, EPP highlights, and CTAs. --}}

<x-guest-layout
    :title="__('FlorenceEGI | Il Rinascimento Digitale per Arte ed Ecologia')"
    :metaDescription="__('Crea, colleziona e investi in Arte Digitale (EGI) unica che supporta progetti concreti di protezione ambientale. Unisciti al Rinascimento Digitale.')">

    {{-- Contenuto Testuale Hero: Lasciato vuoto come richiesto --}}
    <x-slot name="heroContent">
        {{-- Intenzionalmente vuoto per ora --}}
    </x-slot>

     {{-- NUOVO Slot Carousel --}}
     <x-slot name="heroCarousel">
        {{-- Renderizza il componente carousel se ci sono EGI validi --}}
        @if($randomEgis->isNotEmpty())
            <x-egi-carousel :egis="$randomEgis" />
        @endif
    </x-slot>

    {{-- Contenuto SOTTO il testo hero (ma dentro l'area): Collezioni in Evidenza --}}
    <x-slot name="belowHeroContent">
        @if($featuredCollections->isNotEmpty())
             {{-- @style: Titolo visibile sopra le card, testo bianco con ombra per leggibilità sull'animazione. --}}
             <h2 class="text-2xl sm:text-3xl font-semibold text-white mb-6 md:mb-8 text-center" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                 {{ __('Collezioni in Evidenza') }}
             </h2>
             {{-- @style: Griglia responsive, stessi gap delle altre sezioni. --}}
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                 @foreach($featuredCollections as $collection)
                     {{-- La card deve avere uno sfondo o un bordo per staccare dall'animazione --}}
                     {{-- Assicurati che x-collection-card abbia bg-white o simile --}}
                     <x-home-collection-card :collection="$collection" :key="'featured-'.$collection->id" imageType="card" />
                 @endforeach
             </div>
         @else
            {{-- Puoi inserire un messaggio alternativo qui se non ci sono collezioni in evidenza --}}
            {{-- <p class="text-center text-white/80">Nessuna collezione in evidenza al momento.</p> --}}
         @endif
    </x-slot>

    {{-- Contenuto Principale (iniettato nello $slot del layout, sotto l'hero) --}}

    {{-- ✨ Sezione: Ultime Gallerie Arrivate --}}
    <section class="bg-gray-50 py-16 md:py-20">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col sm:flex-row justify-between items-center mb-10 gap-4">
            <h2 class="text-3xl font-bold text-gray-900 text-center sm:text-left">{{ __('Ultime Gallerie Arrivate') }}</h2>
            <a href="{{ route('home.collections.index') }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800 group flex-shrink-0">
                {{ __('Vedi tutte') }}
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-150" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
          </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
            @forelse($latestCollections as $collection)
                <x-home-collection-card :collection="$collection" :key="'latest-'.$collection->id" imageType="card" />
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <p>{{ __('Nessuna nuova galleria al momento. Torna presto a trovarci!') }}</p>
                </div>
            @endforelse
        </div>
      </div>
    </section>

    {{-- 🌳 Sezione: Progetti Ambientali (EPP) --}}
    @if($highlightedEpps->isNotEmpty())
        <section class="bg-emerald-800 text-white py-16 md:py-20">
             <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                 <h2 class="text-3xl font-bold mb-4 text-center">{{ __('Il Tuo Impatto Conta') }}</h2>
                 <p class="text-lg text-emerald-100 mb-10 text-center max-w-3xl mx-auto">{{ __('Ogni EGI che collezioni contribuisce direttamente a progetti ambientali verificati. Scopri dove va il tuo supporto.') }}</p>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                     @foreach($highlightedEpps as $epp)
                         <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 flex flex-col items-center text-center hover:bg-white/20 transition duration-300 ease-in-out">
                             <div class="p-3 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 mb-4 inline-block shadow-lg">
                                 @if($epp->type === 'ARF') <span class="material-symbols-outlined text-white text-3xl">forest</span>
                                 @elseif($epp->type === 'APR') <span class="material-symbols-outlined text-white text-3xl">waves</span>
                                 @elseif($epp->type === 'BPE') <span class="material-symbols-outlined text-white text-3xl">hive</span>
                                 @else <span class="material-symbols-outlined text-white text-3xl">eco</span>
                                 @endif
                             </div>
                             <h3 class="text-xl font-semibold mb-2">{{ $epp->name }}</h3>
                             <p class="text-sm text-emerald-100 mb-4 line-clamp-3 flex-grow">{{ $epp->description }}</p>
                             <a href="{{ route('epps.show', $epp->id) }}" class="mt-auto inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-emerald-800 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-800 focus:ring-white">
                                 {{ __('Scopri di più') }}
                                 <svg class="w-4 h-4 ml-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                 </svg>
                             </a>
                         </div>
                     @endforeach
                 </div>
                  <div class="mt-12 text-center">
                      <a href="{{ route('epps.index') }}" class="text-base font-medium text-emerald-100 hover:text-white underline underline-offset-4 decoration-emerald-400 hover:decoration-white transition">
                          {{ __('Vedi tutti i progetti supportati') }}
                      </a>
                  </div>
             </div>
         </section>
    @endif

    {{-- 📣 Sezione: CTA Creator --}}
    <section class="bg-gray-100 py-16 md:py-20 text-center">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Sei un Artista o un Creator?') }}</h2>
          <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">{{ __('Trasforma la tua arte in un asset digitale con un impatto reale. Unisciti a FlorenceEGI e dai valore alle tue creazioni supportando l\'ambiente.') }}</p>
          <a href="{{ route('register') }}"
             class="inline-block px-8 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-lg text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
             {{ __('Crea la tua Galleria EGI') }}
          </a>
      </div>
    </section>

</x-guest-layout>
