{{-- resources/views/home.blade.php --}}
{{-- 📜 Oracode View: FlorenceEGI Homepage NFT-Style Edition --}}

<x-guest-layout :title="__('guest_home.page_title')" :metaDescription="__('guest_home.meta_description')">
    {{-- CSS per effetti NFT e correzioni layout --}}
    <style>
        /* =========== EFFETTI NFT E ANIMAZIONI =========== */
        /* Effetto pulsazione originale */
        @keyframes natan-pulse-only {
            0%, 100% {
                filter: brightness(1) contrast(1.02) drop-shadow(0 0 2px #fff);
                opacity: 1;
            }
            40% {
                filter: brightness(1.14) contrast(1.13) drop-shadow(0 0 6px #fff);
                opacity: 1;
            }
            50% {
                filter: brightness(1.18) contrast(1.18) drop-shadow(0 0 8px #fff);
                opacity: 0.97;
            }
            60% {
                filter: brightness(1.14) contrast(1.13) drop-shadow(0 0 6px #fff);
                opacity: 1;
            }
        }

        .natan-pulse-only {
            animation: natan-pulse-only 3.4s cubic-bezier(.68, 0, .41, 1) infinite;
        }

        /* Effetto gradiente radiale */
        .bg-gradient-radial {
            background-image: radial-gradient(var(--tw-gradient-stops));
        }

        /* Effetto testo brillante */
        .nft-text-glow {
            text-shadow: 0 0 15px currentColor;
        }

        /* Pulsazione NFT */
        @keyframes nft-pulse {
            0%, 100% {
                opacity: 0.7;
            }
            50% {
                opacity: 1;
            }
        }

        .nft-pulse {
            animation: nft-pulse 2s ease-in-out infinite;
        }

        /* Badge shine NFT */
        @keyframes nft-badge-shine {
            0% {
                background-position: 200% center;
            }
            100% {
                background-position: -200% center;
            }
        }

        .nft-badge-shine {
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0) 100%);
            background-size: 200% auto;
            animation: nft-badge-shine 3s linear infinite;
            animation-fill-mode: forwards;
        }

        /* Rotating border NFT */
        @keyframes nft-rotating-border {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .nft-rotating-border {
            background: conic-gradient(from 0deg, transparent, #f59e0b, transparent, transparent);
            animation: nft-rotating-border 8s linear infinite;
            border-radius: 50%;
        }

        .nft-rotating-border-container {
            position: relative;
            overflow: hidden;
            border-radius: 50%;
        }

        /* Testo shimmmering NFT */
        @keyframes nft-shimmer {
            0% {
                filter: brightness(0.8) drop-shadow(0 0 2px rgba(255, 255, 255, 0.3));
            }
            20% {
                filter: brightness(1.2) drop-shadow(0 0 5px rgba(255, 255, 255, 0.5));
            }
            40% {
                filter: brightness(0.9) drop-shadow(0 0 3px rgba(255, 255, 255, 0.4));
            }
            60% {
                filter: brightness(1.1) drop-shadow(0 0 4px rgba(255, 255, 255, 0.5));
            }
            80% {
                filter: brightness(0.9) drop-shadow(0 0 2px rgba(255, 255, 255, 0.3));
            }
            100% {
                filter: brightness(0.8) drop-shadow(0 0 2px rgba(255, 255, 255, 0.3));
            }
        }

        .nft-shimmering-text {
            animation: nft-shimmer 4s ease-in-out infinite;
        }

        /* 3D Float Animation */
        @keyframes nft-3d-float {
            0%, 100% {
                transform: translateY(0) rotateY(0);
            }
            25% {
                transform: translateY(-5px) rotateY(2deg);
            }
            75% {
                transform: translateY(5px) rotateY(-2deg);
            }
        }

        .nft-3d-float {
            animation: nft-3d-float 6s ease-in-out infinite;
            transform-style: preserve-3d;
        }

        /* Hover effects */
        .nft-hover-float {
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nft-hover-float:hover {
            transform: translateY(-8px) scale(1.02);
        }

        .nft-hover-tilt {
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nft-hover-tilt:hover {
            transform: perspective(1000px) rotateX(2deg) rotateY(5deg);
        }

        /* NFT Glass Card Effect */
        .nft-glass-card {
            backdrop-filter: blur(8px);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.05), rgba(0, 0, 0, 0.1));
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* NFT Border Animation */
        @keyframes nft-border-flow {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .nft-animated-border {
            background: linear-gradient(270deg, #4ade80, #22d3ee, #818cf8, #4ade80);
            background-size: 300% 300%;
            animation: nft-border-flow 3s ease infinite;
        }

        /* NFT Holographic Effect */
        .nft-holographic-effect {
            background: linear-gradient(125deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.05) 25%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0.05) 75%, rgba(255, 255, 255, 0) 100%);
            background-size: 200% 200%;
            animation: nft-border-flow 3s ease infinite;
            z-index: 5;
            pointer-events: none;
        }

        /* NFT CTA Button Pulse */
        @keyframes nft-border-pulse {
            0%, 100% {
                opacity: 0.25;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
                transform: scale(1.05);
            }
        }

        .nft-pulsing-border {
            animation: nft-border-pulse 2s ease-in-out infinite;
        }

        .nft-cta-button {
            box-shadow: 0 0 15px rgba(45, 212, 191, 0.3);
        }

        .nft-cta-button:hover {
            box-shadow: 0 0 25px rgba(45, 212, 191, 0.4);
        }

        /* =========== FIX SOVRAPPOSIZIONE =========== */
        /* Fix per evitare sovrapposizione tra below-hero-content e main-content */
        .below-hero-content {
            margin-bottom: 7rem !important; /* Spazio extra sotto le featured collections */
            position: relative;
            z-index: 10;
        }

        /* Assicurare che il main content inizi con spazio adeguato e sfondo visibile */
        #main-content {
            position: relative;
            z-index: 20;
            padding-top: 4rem;
            background: linear-gradient(to bottom, rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.95));
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Sezioni specifiche nel main content */
        .nft-stats-section {
            padding: 3rem 0;
            background: linear-gradient(to bottom, rgba(17, 24, 39, 0.85), rgba(17, 24, 39, 0.9));
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 4rem;
            position: relative;
            z-index: 10;
        }

        .nft-stat-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.15) 100%);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
        }

        /* Griglia collezioni */
        .featured-collections-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            width: 100%;
        }

        @media (min-width: 640px) {
            .featured-collections-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .featured-collections-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
            }
        }

        /* Collection Card NFT Style */
        .collection-card-nft {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            transition: transform 0.3s ease;
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
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>

    {{-- SLOT PER COLONNA SINISTRA: Natan --}}
    <x-slot name="heroContentLeft">
        <div class="flex flex-col items-center gap-5">
            {{-- Immagine Natan con effetti NFT avanzati --}}
            <div class="relative nft-rotating-border-container">
                {{-- Aura NFT intorno a Natan --}}
                <div class="absolute inset-0 -m-4 rounded-full bg-gradient-radial from-amber-500/20 via-amber-400/10 to-transparent blur-2xl nft-pulse"></div>
                {{-- Bordo rotante NFT --}}
                <div class="absolute inset-0 -m-1 rounded-full nft-rotating-border"></div>

                {{-- Effetto holographic overlay --}}
                <div class="absolute inset-0 nft-holographic-effect"></div>

                <img src="/images/default/natan.png" alt="Natan" class="relative z-10 block h-56 md:h-96 lg:h-[40rem] w-auto max-w-none mx-auto natan-pulse-only nft-hover-tilt" />
            </div>

            {{-- Badge narrativo style NFT collectible --}}
            <span class="flex items-center gap-2 px-5 py-4 mt-6 text-lg font-semibold text-blue-900 border shadow-md bg-gradient-to-br from-white/90 to-blue-100/80 border-blue-200/50 badge badge-narrative rounded-xl nft-glass-effect nft-hover-float">
                <svg class="w-5 h-5 text-blue-600 nft-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M15.7 4C15.7 4 16.3 5.2 16.3 7C16.3 8.8 15 10.3 13 10.3C11 10.3 9.2 8.8 9.2 7C9.2 5.2 9.8 4 9.8 4M2 22V19C2 16.8 6.1 15 11 15M18 14C19.5 14 21.5 14.3 23 15.1V22H13V18.9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Natan's Journey</span>

                {{-- NFT rarity badge --}}
                <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold uppercase bg-gradient-to-r from-amber-400 to-yellow-600 text-white rounded nft-badge-shine">Legendary</span>
            </span>

            {{-- Descrizione migliorata con NFT tokenization reference --}}
            <span class="mt-2 text-lg text-center text-white/90 nft-text-glow">
                <span class="font-semibold text-amber-300">Natan</span> accompagna chi crea.
                Con ogni EGI nasce una <span class="font-semibold text-violet-300">storia nuova</span>:
                arte, impatto, futuro, insieme.
                <x-environmental-stats format="natan-badge" />
                <x-environmental-stats format="reservations" textColor="indigo" />
            </span>
        </div>
    </x-slot>

    {{-- SLOT PER COLONNA CENTRALE: Carousel con Molecola sopra --}}
    <x-slot name="heroCarousel">
        <div class="relative flex flex-col items-center w-full">
            <div class="relative flex flex-col items-center">
                {{-- Glow effect sotto la molecola style NFT --}}
                <div class="absolute rounded-full top-24 w-80 h-80 bg-gradient-radial from-cyan-400/20 via-cyan-500/10 to-transparent blur-xl nft-pulse"></div>
                <!-- Molecola SVG/logo con effetto NFT -->
                <div class="relative nft-3d-float">
                    <img src="/images/logo/logo_tr.png" alt="Equilibrium Molecule" class="relative z-10 mb-2 w-96 animate-pulse natan-pulse-only" />

                    {{-- NFT verification badge --}}
                    <div class="absolute flex items-center justify-center w-10 h-10 rounded-full -top-2 -right-2 bg-gradient-to-br from-blue-600 to-indigo-600 nft-badge-shine">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <!-- Testo arcuato migliorato in stile NFT -->
                <svg width="300" height="80" class="absolute z-10 -translate-x-1/2 pointer-events-none top-2 left-1/2 natan-pulse-only" style="overflow: visible;">
                    <defs>
                        <linearGradient id="textGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#4ade80" />
                            <stop offset="30%" stop-color="#22d3ee" />
                            <stop offset="70%" stop-color="#818cf8" />
                            <stop offset="100%" stop-color="#4ade80" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="2.5" result="coloredBlur" />
                            <feMerge>
                                <feMergeNode in="coloredBlur" />
                                <feMergeNode in="SourceGraphic" />
                            </feMerge>
                        </filter>
                        <path id="curve" d="M 30,70 A 140,80 0 0,1 280,70" />
                    </defs>
                    <text font-size="2rem" fill="url(#textGradient)" font-family="Montserrat, Arial, sans-serif" letter-spacing="6" filter="url(#glow)" class="nft-shimmering-text">
                        <textPath href="#curve" startOffset="0%">
                            EQUILIBRIUM
                        </textPath>
                    </text>
                </svg>

                <!-- Testo esplicativo sotto la molecola con ottimizzazione SEO -->
                <div class="relative z-10 max-w-lg mt-5 mb-6 text-center">
                    <h2 class="mb-2 text-xl font-bold leading-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-cyan-200 to-teal-300 md:text-2xl">
                        Equilibrium: Il Token che Pulisce gli Oceani
                    </h2>
                    <p class="text-sm text-gray-200 md:text-base">
                        Ogni <span class="font-semibold text-cyan-300">Equilibrium</span> generato finanzia direttamente la
                        <strong class="font-semibold text-blue-300">rimozione di plastica dagli oceani</strong>.
                        Non è solo un token digitale, ma un <mark class="px-1 rounded bg-blue-900/30">intervento concreto per la salvaguardia marina</mark>.
                        Attraverso la nostra piattaforma, ogni transazione contribuisce a
                        <strong class="text-teal-300">liberare le acque dall'inquinamento plastico</strong>.
                    </p>

                    <!-- Contatore di impatto con animazione per engagement -->
                    <x-environmental-stats format="full" />
                </div>
            </div>

            {{-- Contenitore carousel migliorato in stile NFT marketplace --}}
            <div class="relative w-full p-1 mt-4 overflow-hidden rounded-xl nft-card-container">
                {{-- Bordo NFT animato --}}
                <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-500 rounded-xl opacity-50 blur-sm nft-animated-border"></div>

                {{-- Contenuto carousel --}}
                <div class="relative z-10 overflow-hidden rounded-lg bg-gray-900/40 backdrop-blur-sm nft-glass-card">
                    {{-- Marketplace header --}}
                    <div class="absolute top-0 left-0 right-0 z-20 flex items-center justify-between px-4 py-2 border-b bg-gradient-to-r from-gray-900/80 via-gray-900/60 to-gray-900/80 border-white/10">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="text-xs font-medium text-white/80">Trending EGIs</span>
                        </div>
                        <div class="flex gap-1.5">
                            <div class="px-2 py-0.5 text-[10px] font-medium bg-indigo-900/60 border border-indigo-500/40 text-indigo-300 rounded-full flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                Live
                            </div>
                        </div>
                    </div>

                    @if($randomEgis->isNotEmpty())
                    <x-egi-carousel :egis="$randomEgis" />
                    @endif

                    {{-- Marketplace footer NFT style (prices etc) --}}
                    <div class="absolute bottom-0 left-0 right-0 z-20 flex items-center justify-between px-4 py-2 border-t bg-gradient-to-r from-gray-900/80 via-gray-900/60 to-gray-900/80 border-white/10">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-teal-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 17.97L4.58 13.62 11.943 24l7.37-10.38-7.372 4.35h.003zM12.056 0L4.69 12.223l7.365 4.354 7.365-4.35L12.056 0z" />
                            </svg>
                            <span class="font-mono text-xs text-white/80">Floor: 0.85</span>
                        </div>
                        <div class="text-xs text-white/60">24h: <span class="text-emerald-400">+12.4%</span></div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- SLOT PER COLONNA DESTRA: Badge/Copy impatto --}}
    <x-slot name="heroContentRight">
        <div class="relative flex flex-col items-end gap-4 p-6 overflow-hidden border shadow-xl rounded-xl bg-gradient-to-br from-emerald-900/30 to-emerald-800/40 backdrop-blur-sm border-emerald-400/30 animate-hero-right-glow nft-hover-float nft-glass-card">
            {{-- NFT card header --}}
            <div class="absolute top-0 left-0 right-0 flex items-center justify-between px-4 py-2 border-b bg-gradient-to-r from-emerald-900/40 to-emerald-800/50 border-emerald-500/20">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2 2m0 0l2 2m-2-2l-2 2m0 0l-2 2" />
                    </svg>
                    <span class="text-xs font-medium text-emerald-300">Green Impact</span>
                </div>
                <div class="text-[10px] font-medium text-white/60">#0025</div>
            </div>
            <div class="flex items-center gap-2 mt-4 animate-bounce-slow">
                {{-- SVG Piantina animata con glow in stile NFT token --}}
                <div class="p-2 border rounded-full bg-emerald-900/50 border-emerald-500/30 nft-pulse">
                    <svg class="w-7 h-7 text-emerald-400 animate-grow-leaf" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-5m0 0c4.418 0 8-3.134 8-7v-.25C20 7 19 3 12 3 5 3 4 7 4 8.75V9c0 3.866 3.582 7 8 7z" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-wide text-transparent uppercase bg-clip-text bg-gradient-to-r from-emerald-300 to-emerald-100 drop-shadow nft-shimmering-text">
                    Impatto Reale
                </span>
            </div>

            {{-- Testo migliorato con highlight NFT style --}}
            <span class="mt-2 text-xl font-semibold leading-tight text-right text-white/95 animate-hero-text">
                <span class="font-bold text-emerald-300 nft-text-glow">Sii l'onda</span> del cambiamento.<br>
                Ogni EGI è un <span class="font-mono text-cyan-300 nft-text-glow">token</span> reale per la Terra.
            </span>

            {{-- NFT marketplace stats --}}
            <x-environmental-stats format="equilibrium" textColor="green" />
            <x-environmental-stats format="card-stats" textColor="emerald" />

            {{-- CTA migliorato in stile NFT marketplace --}}
            <a href="{{ route('epps.index') }}" class="relative inline-block px-6 py-3 mt-2 overflow-hidden text-base font-semibold text-white transition duration-200 shadow-md bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl group nft-cta-button">
                <span class="absolute inset-0 bg-[url('/images/default/noise.png')] bg-repeat opacity-10"></span>
                <span class="absolute transition opacity-25 -inset-1 bg-gradient-to-r from-emerald-500 via-cyan-500 to-teal-500 rounded-xl blur group-hover:opacity-40 nft-pulsing-border"></span>
                <span class="relative z-10 flex items-center gap-2">
                    <span>🌍 Scopri l'Impatto</span>
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </span>
            </a>

            {{-- Badges NFT style --}}
            <div class="flex flex-wrap justify-end gap-2 mt-4">
                <div class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-900/60 border border-emerald-500/30 text-emerald-300 flex items-center gap-1.5 nft-badge-shine">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 nft-pulse"></span>
                    Carbon Negative
                </div>
                <div class="px-3 py-1 rounded-full text-xs font-medium bg-violet-900/60 border border-violet-500/30 text-violet-300 flex items-center gap-1.5 nft-badge-shine">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.944 17.97L4.58 13.62 11.943 24l7.37-10.38-7.372 4.35h.003zM12.056 0L4.69 12.223l7.365 4.354 7.365-4.35L12.056 0z" />
                    </svg>
                    Algorand
                </div>
            </div>
        </div>
    </x-slot>
    {{-- SLOT PER CONTENUTO SOTTO HERO: Collezioni in Evidenza NFT Style --}}
    <x-slot name="belowHeroContent">
        @if ($featuredCollections->isNotEmpty())
        <h2 class="mb-6 text-2xl font-semibold text-center text-white sm:text-3xl md:mb-8"
            style="text-shadow: 0 0 30px rgba(147, 51, 234, 0.5);">
            {{ __('guest_home.featured_collections_title') }}
        </h2>
        <div class="featured-collections-grid">
            @foreach ($featuredCollections as $collection)
            <div class="collection-card-nft group">
                {{-- Gradient border animation --}}
                <div
                    class="absolute -inset-[1px] rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 opacity-0 blur-sm transition duration-300 group-hover:opacity-100">
                </div>

                {{-- Card content --}}
                <div class="relative overflow-hidden nft-glass-card rounded-xl">
                    <x-home-collection-card :collection="$collection" :key="'featured-' . $collection->id"
                        imageType="card" />
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </x-slot>

    {{-- CONTENUTO PRINCIPALE (Dopo Hero Section) --}}
    {{-- Nuova Sezione: Statistiche NFT Animate --}}
    <x-egi-stats-section :animate="true" />

    {{-- Sezione: Ultime Gallerie Arrivate con effetti NFT --}}
    <section class="py-16 bg-gray-900/80 backdrop-blur-sm md:py-20" aria-labelledby="latest-galleries-heading">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 mb-10 sm:flex-row">
                <h2 id="latest-galleries-heading"
                    class="text-3xl font-bold text-center text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text sm:text-left">
                    {{ __('guest_home.latest_galleries_title') }}
                </h2>
                <a href="{{ route('home.collections.index') }}"
                    class="relative inline-flex items-center nft-glow-button group">
                    <span
                        class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-purple-600 to-cyan-600 blur group-hover:opacity-100"></span>
                    <span
                        class="relative flex items-center px-6 py-2 leading-none bg-gray-900 rounded-lg ring-1 ring-gray-800">
                        <span class="text-gray-100 transition duration-200 group-hover:text-white">{{
                            __('guest_home.view_all') }}</span>
                        <svg class="w-4 h-4 ml-2 text-purple-400 transition duration-200 group-hover:text-cyan-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </span>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 lg:gap-8">
                @forelse($latestCollections as $collection)
                <div class="collection-card-nft group">
                    <div
                        class="absolute -inset-[1px] rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 opacity-0 blur-sm transition duration-300 group-hover:opacity-100">
                    </div>
                    <div class="relative overflow-hidden nft-glass-card rounded-xl">
                        <x-home-collection-card :collection="$collection" :key="'latest-' . $collection->id"
                            imageType="card" />
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
    @if ($highlightedEpps->isNotEmpty())
    <section class="py-16 text-white bg-gradient-to-b from-emerald-900/90 to-emerald-800/90 backdrop-blur-sm md:py-20"
        aria-labelledby="environmental-impact-heading">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            <h2 id="environmental-impact-heading"
                class="mb-4 text-3xl font-bold text-center text-transparent bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text">
                {{ __('guest_home.your_impact_counts_title') }}
            </h2>
            <p class="max-w-3xl mx-auto mb-10 text-lg text-center text-emerald-100">
                {{ __('guest_home.your_impact_counts_description') }}</p>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ($highlightedEpps as $index => $epp)
                <article
                    class="relative flex flex-col items-center p-6 text-center transition duration-300 ease-in-out nft-glass-card rounded-xl hover:scale-105 hover:transform">
                    {{-- Rarity Badge --}}
                    <div class="absolute right-2 top-2">
                        <span
                            class="@if ($index === 0) from-yellow-400 to-orange-400
                                    @elseif($index === 1) from-purple-400 to-pink-400
                                    @else from-cyan-400 to-blue-400 @endif pulse-badge rounded-full bg-gradient-to-r px-3 py-1 text-xs font-bold text-black">
                            @if ($index === 0)
                            LEGENDARY
                            @elseif($index === 1)
                            EPIC
                            @else
                            RARE
                            @endif
                        </span>
                    </div>

                    <div class="inline-block p-3 mb-4 rounded-full shadow-lg bg-gradient-to-br from-emerald-400 to-green-500"
                        aria-hidden="true">
                        @if ($epp->type === 'ARF')
                        <span class="text-3xl text-white material-symbols-outlined">forest</span>
                        @elseif($epp->type === 'APR')
                        <span class="text-3xl text-white material-symbols-outlined">waves</span>
                        @elseif($epp->type === 'BPE')
                        <span class="text-3xl text-white material-symbols-outlined">hive</span>
                        @else
                        <span class="text-3xl text-white material-symbols-outlined">eco</span>
                        @endif
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">{{ $epp->name }}</h3>
                    <p class="flex-grow mb-4 text-sm line-clamp-3 text-emerald-100">{{ $epp->description }}
                    </p>
                    <div class="relative mt-auto nft-glow-button group">
                        <span
                            class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 blur group-hover:opacity-100"></span>
                        <a href="{{ route('epps.show', $epp->id) }}"
                            class="relative inline-flex items-center px-4 py-2 bg-gray-900 rounded-lg ring-1 ring-gray-800">
                            <span class="text-gray-100 transition duration-200 group-hover:text-white">{{
                                __('guest_home.discover_more') }}</span>
                            <svg class="w-4 h-4 ml-2 text-green-400 transition duration-200 group-hover:text-emerald-400"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="mt-12 text-center">
                <div class="relative inline-block nft-glow-button group">
                    <span
                        class="absolute transition duration-200 rounded-lg opacity-75 -inset-1 animate-pulse bg-gradient-to-r from-emerald-600 to-green-600 blur group-hover:opacity-100"></span>
                    <a href="{{ route('epps.index') }}"
                        class="relative inline-flex items-center px-6 py-3 bg-gray-900 rounded-lg ring-1 ring-gray-800">
                        <span
                            class="text-base font-medium text-gray-100 transition duration-200 group-hover:text-white">
                            {{ __('guest_home.view_all_supported_projects') }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Sezione: CTA Creator NFT Style --}}
    <section class="py-16 text-center bg-gradient-to-b from-gray-900 to-black backdrop-blur-sm md:py-20"
        aria-labelledby="creator-cta-heading">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            <h2 id="creator-cta-heading"
                class="mb-4 text-3xl font-bold text-transparent bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text">
                {{ __('guest_home.are_you_artist_title') }}
            </h2>
            <p class="max-w-2xl mx-auto mb-10 text-lg text-gray-300">{{ __('guest_home.are_you_artist_description') }}
            </p>

            <div class="relative inline-block nft-glow-button group">
                <span
                    class="absolute -inset-1.5 animate-pulse rounded-lg bg-gradient-to-r from-purple-600 via-pink-600 to-cyan-600 opacity-75 blur transition duration-200 group-hover:opacity-100"></span>
                <a href="{{ route('register') }}"
                    class="relative inline-flex items-center px-8 py-4 bg-gray-900 rounded-lg ring-2 ring-purple-500">
                    <span
                        class="text-lg font-bold text-transparent transition-all duration-200 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text group-hover:from-cyan-400 group-hover:to-purple-400">
                        {{ __('guest_home.create_your_gallery') }}
                    </span>
                    <svg class="w-6 h-6 ml-3 text-purple-400 transition duration-200 group-hover:text-cyan-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
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

        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('header');
            const threshold = 100;

            function toggleHeaderStyle() {
                if (window.scrollY > threshold) {
                    header.classList.remove('transparent');
                    header.classList.add('sticky');
                } else {
                    header.classList.add('transparent');
                    header.classList.remove('sticky');
                }
            }

            // Imposta inizialmente
            toggleHeaderStyle();

            // Controlla allo scroll
            window.addEventListener('scroll', toggleHeaderStyle);
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Check if VanillaTilt exists before using it
            if (typeof VanillaTilt !== 'undefined') {
                VanillaTilt.init(document.querySelectorAll(".collection-card-nft"), {
                    max: 15,
                    speed: 400,
                    glare: true,
                    "max-glare": 0.2,
                });
            }

            // Effetto di transizione pagina
            document.body.classList.add('page-transition');

            setTimeout(function() {
                document.body.classList.add('page-loaded');
            }, 100);

            // Aggiungi questo se vuoi anche effetti di transizione per i link interni
            document.querySelectorAll('a[href^="/"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Verifica che sia un link interno
                    if (this.hostname === window.location.hostname) {
                        e.preventDefault();

                        document.body.classList.remove('page-loaded');

                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 400);
                    }
                });
            });
        });
    </script>
    </x-slot>
</x-guest-layout>
