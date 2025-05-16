{{-- resources/views/home.blade.php --}}
{{-- 📜 Oracode View: FlorenceEGI Homepage NFT-Style Edition --}}

<x-guest-layout
    :title="__('guest_home.page_title')"
    :metaDescription="__('guest_home.meta_description')">

    {{-- Custom CSS per effetti NFT --}}
    <x-slot name="headExtra">
        <style>
            #hero-section .hero-content-overlay > div {
                position: relative;
                z-index: 10;
            }

            /* Assicura che il carousel sia visibile */
            .hero-content-overlay .lg\:col-span-6 {
                position: relative;
                z-index: 12;
            }

            /* NFT Glassmorphism Effects */
            .nft-glass-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.18);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .nft-glass-card:hover {
                transform: translateY(-4px);
                background: rgba(255, 255, 255, 0.08);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2),
                           0 0 20px rgba(147, 51, 234, 0.4);
            }

            /* NFT Glow Button */
            .nft-glow-button {
                position: relative;
                overflow: hidden;
            }

            .nft-glow-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(147, 51, 234, 0.4), transparent);
                transition: left 0.5s;
            }

            .nft-glow-button:hover::before {
                left: 100%;
            }

            /* NFT Stat Card */
            .nft-stat-card {
                background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.15) 100%);
                backdrop-filter: blur(10px);
            }

            /* Collection Card NFT Style */
            .collection-card-nft {
                position: relative;
                overflow: hidden;
                border-radius: 1rem;
            }

            .collection-card-nft::before {
                content: '';
                position: absolute;
                inset: -2px;
                background: linear-gradient(45deg, #f39c12, #e74c3c, #9b59b6, #3498db, #2ecc71);
                background-size: 300% 300%;
                border-radius: 1rem;
                opacity: 0;
                transition: opacity 0.3s ease;
                animation: gradient-shift 4s ease infinite;
                z-index: -1;
            }

            .collection-card-nft:hover::before {
                opacity: 1;
            }

            @keyframes gradient-shift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Pulsing badge */
            .pulse-badge {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
        </style>
    </x-slot>

    {{-- Hero Content: Testo a sinistra del carousel con effetto glow --}}
    <x-slot name="heroContentLeft">
        <div class="p-6 text-white nft-glass-card rounded-xl">
            <h2 class="mb-3 text-xl font-bold text-transparent lg:text-2xl bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                {{ __('guest_home.hero_left_title') }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-100 lg:text-base">
                {{ __('guest_home.hero_left_description') }}
            </p>
        </div>
    </x-slot>

    {{-- Carousel centrale con glow effect --}}
    <x-slot name="heroCarousel">
        <div class="relative w-full">
            {{-- Glow effect behind carousel --}}
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-pink-600/20 blur-3xl"></div>
            @if($randomEgis->isNotEmpty())
                <x-egi-carousel :egis="$randomEgis" />
            @endif
        </div>
    </x-slot>

    {{-- Hero Content: Testo a destra del carousel con effetto glow --}}
    <x-slot name="heroContentRight">
        <div class="p-6 text-white nft-glass-card rounded-xl">
            <h2 class="mb-3 text-xl font-bold text-transparent lg:text-2xl bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">
                {{ __('guest_home.hero_right_title') }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-100 lg:text-base">
                {{ __('guest_home.hero_right_description') }}
            </p>
        </div>
    </x-slot>

    {{-- Contenuto SOTTO il testo hero: Collezioni in Evidenza NFT Style --}}
    <x-slot name="belowHeroContent">
        @if($featuredCollections->isNotEmpty())
            <h2 class="mb-6 text-2xl font-semibold text-center text-white sm:text-3xl md:mb-8" style="text-shadow: 0 0 30px rgba(147, 51, 234, 0.5);">
                {{ __('guest_home.featured_collections_title') }}
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @foreach($featuredCollections as $collection)
                    <div class="collection-card-nft group">
                        {{-- Gradient border animation --}}
                        <div class="absolute -inset-[1px] bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl opacity-0 group-hover:opacity-100 transition duration-300 blur-sm"></div>

                        {{-- Card content --}}
                        <div class="relative overflow-hidden nft-glass-card rounded-xl">
                            <x-home-collection-card :collection="$collection" :key="'featured-'.$collection->id" imageType="card" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-slot>

    {{-- Nuova Sezione: Statistiche NFT Animate --}}
    <section class="py-16 bg-gray-900/50 backdrop-blur-sm nft-stats-section">
        <div class="container px-4 mx-auto">
            <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                <div class="p-6 text-center border nft-stat-card rounded-xl border-purple-500/20">
                    <div class="text-3xl font-bold text-transparent md:text-4xl bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                        <span data-counter="1234">0</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">{{ __('guest_home.total_egi_created') }}</p>
                </div>
                <div class="p-6 text-center border nft-stat-card rounded-xl border-cyan-500/20">
                    <div class="text-3xl font-bold text-transparent md:text-4xl bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">
                        <span data-counter="567">0</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">{{ __('guest_home.active_collectors') }}</p>
                </div>
                <div class="p-6 text-center border nft-stat-card rounded-xl border-green-500/20">
                    <div class="text-3xl font-bold text-transparent md:text-4xl bg-clip-text bg-gradient-to-r from-green-400 to-emerald-400">
                        <span data-counter="89000">0</span> €
                    </div>
                    <p class="mt-2 text-sm text-gray-400">{{ __('guest_home.environmental_impact') }}</p>
                </div>
                <div class="p-6 text-center border nft-stat-card rounded-xl border-orange-500/20">
                    <div class="text-3xl font-bold text-transparent md:text-4xl bg-clip-text bg-gradient-to-r from-orange-400 to-red-400">
                        <span data-counter="42">0</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">{{ __('guest_home.supported_projects') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Sezione: Ultime Gallerie Arrivate con effetti NFT --}}
    <section class="py-16 bg-gray-900/80 backdrop-blur-sm md:py-20" aria-labelledby="latest-galleries-heading">
      <div class="container px-4 mx-auto sm:px-6 lg:px-8">
          <div class="flex flex-col items-center justify-between gap-4 mb-10 sm:flex-row">
            <h2 id="latest-galleries-heading" class="text-3xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 sm:text-left">
                {{ __('guest_home.latest_galleries_title') }}
            </h2>
            <a href="{{ route('home.collections.index') }}" class="relative inline-flex items-center nft-glow-button group">
                <span class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-purple-600 to-cyan-600 blur group-hover:opacity-100"></span>
                <span class="relative flex items-center px-6 py-2 leading-none bg-gray-900 rounded-lg ring-1 ring-gray-800">
                    <span class="text-gray-100 transition duration-200 group-hover:text-white">{{ __('guest_home.view_all') }}</span>
                    <svg class="w-4 h-4 ml-2 text-purple-400 transition duration-200 group-hover:text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </span>
            </a>
          </div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 lg:gap-8">
            @forelse($latestCollections as $collection)
                <div class="collection-card-nft group">
                    <div class="absolute -inset-[1px] bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl opacity-0 group-hover:opacity-100 transition duration-300 blur-sm"></div>
                    <div class="relative overflow-hidden nft-glass-card rounded-xl">
                        <x-home-collection-card :collection="$collection" :key="'latest-'.$collection->id" imageType="card" />
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 col-span-full" role="status">
                    <p>{{ __('guest_home.no_new_galleries') }}</p>
                </div>
            @endforelse
        </div>
      </div>
    </section>

    {{-- Sezione: Progetti Ambientali (EPP) NFT Style --}}
    @if($highlightedEpps->isNotEmpty())
        <section class="py-16 text-white bg-gradient-to-b from-emerald-900/90 to-emerald-800/90 backdrop-blur-sm md:py-20" aria-labelledby="environmental-impact-heading">
             <div class="container px-4 mx-auto sm:px-6 lg:px-8">
                 <h2 id="environmental-impact-heading" class="mb-4 text-3xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-400">
                    {{ __('guest_home.your_impact_counts_title') }}
                 </h2>
                 <p class="max-w-3xl mx-auto mb-10 text-lg text-center text-emerald-100">{{ __('guest_home.your_impact_counts_description') }}</p>
                 <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                     @foreach($highlightedEpps as $index => $epp)
                         <article class="relative flex flex-col items-center p-6 text-center transition duration-300 ease-in-out rounded-xl nft-glass-card hover:transform hover:scale-105">
                             {{-- Rarity Badge --}}
                             <div class="absolute top-2 right-2">
                                <span class="px-3 py-1 text-xs font-bold text-black bg-gradient-to-r
                                    @if($index === 0) from-yellow-400 to-orange-400
                                    @elseif($index === 1) from-purple-400 to-pink-400
                                    @else from-cyan-400 to-blue-400
                                    @endif rounded-full pulse-badge">
                                    @if($index === 0) LEGENDARY
                                    @elseif($index === 1) EPIC
                                    @else RARE
                                    @endif
                                </span>
                             </div>

                             <div class="inline-block p-3 mb-4 rounded-full shadow-lg bg-gradient-to-br from-emerald-400 to-green-500" aria-hidden="true">
                                 @if($epp->type === 'ARF') <span class="text-3xl text-white material-symbols-outlined">forest</span>
                                 @elseif($epp->type === 'APR') <span class="text-3xl text-white material-symbols-outlined">waves</span>
                                 @elseif($epp->type === 'BPE') <span class="text-3xl text-white material-symbols-outlined">hive</span>
                                 @else <span class="text-3xl text-white material-symbols-outlined">eco</span>
                                 @endif
                             </div>
                             <h3 class="mb-2 text-xl font-semibold">{{ $epp->name }}</h3>
                             <p class="flex-grow mb-4 text-sm text-emerald-100 line-clamp-3">{{ $epp->description }}</p>
                             <div class="relative mt-auto nft-glow-button group">
                                <span class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 blur group-hover:opacity-100"></span>
                                <a href="{{ route('epps.show', $epp->id) }}" class="relative inline-flex items-center px-4 py-2 bg-gray-900 rounded-lg ring-1 ring-gray-800">
                                    <span class="text-gray-100 transition duration-200 group-hover:text-white">{{ __('guest_home.discover_more') }}</span>
                                    <svg class="w-4 h-4 ml-2 text-green-400 transition duration-200 group-hover:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                </a>
                             </div>
                         </article>
                     @endforeach
                 </div>
                  <div class="mt-12 text-center">
                      <div class="relative inline-block nft-glow-button group">
                        <span class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-emerald-600 to-green-600 blur group-hover:opacity-100 animate-pulse"></span>
                        <a href="{{ route('epps.index') }}" class="relative inline-flex items-center px-6 py-3 bg-gray-900 rounded-lg ring-1 ring-gray-800">
                            <span class="text-base font-medium text-gray-100 transition duration-200 group-hover:text-white">
                                {{ __('guest_home.view_all_supported_projects') }}
                            </span>
                        </a>
                      </div>
                  </div>
             </div>
         </section>
    @endif

    {{-- Sezione: CTA Creator NFT Style --}}
    <section class="py-16 text-center bg-gradient-to-b from-gray-900 to-black backdrop-blur-sm md:py-20" aria-labelledby="creator-cta-heading">
      <div class="container px-4 mx-auto sm:px-6 lg:px-8">
          <h2 id="creator-cta-heading" class="mb-4 text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400">
            {{ __('guest_home.are_you_artist_title') }}
          </h2>
          <p class="max-w-2xl mx-auto mb-10 text-lg text-gray-300">{{ __('guest_home.are_you_artist_description') }}</p>

          <div class="relative inline-block nft-glow-button group">
            <span class="absolute -inset-1.5 bg-gradient-to-r from-purple-600 via-pink-600 to-cyan-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-200 animate-pulse"></span>
            <a href="{{ route('register') }}" class="relative inline-flex items-center px-8 py-4 bg-gray-900 rounded-lg ring-2 ring-purple-500">
                <span class="text-lg font-bold text-transparent transition-all duration-200 bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 group-hover:from-cyan-400 group-hover:to-purple-400">
                    {{ __('guest_home.create_your_gallery') }}
                </span>
                <svg class="w-6 h-6 ml-3 text-purple-400 transition duration-200 group-hover:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </a>
          </div>
      </div>
    </section>

    {{-- Script per animazione contatori --}}
    <x-slot name="scriptExtra">
        <script>
            function animateCounters() {
                const counters = document.querySelectorAll('[data-counter]');

                counters.forEach(counter => {
                    const target = parseInt(counter.dataset.counter);
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent = Math.floor(current).toLocaleString();
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target.toLocaleString();
                        }
                    };

                    updateCounter();
                });
            }

            // Trigger on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.disconnect();
                    }
                });
            });

            const statsSection = document.querySelector('.nft-stats-section');
            if (statsSection) observer.observe(statsSection);
        </script>
    </x-slot>

</x-guest-layout>
