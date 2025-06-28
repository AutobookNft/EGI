{{-- resources/views/home.blade.php --}}
{{-- 📜 Oracode View: FlorenceEGI Homepage NFT-Style Edition --}}

@vite(['resources/css/home-nft.css', 'resources/js/home-nft.js'])

<x-guest-layout :title="__('guest_home.page_title')" :metaDescription="__('guest_home.meta_description')">

    <x-slot name="heroFullWidth">

        <x-collection-hero-banner :collections="$featuredCollections" id="mainHeroCarousel"/>

    </x-slot>

    {{-- Contenuto SOTTO il testo hero: Collezioni in Evidenza NFT Style --}}
    <x-slot name="belowHeroContent">
       <x-collections-carousel
            :collections="$featuredCollections"
            title="Collezioni in Evidenza"
            bgClass="bg-gray-900"
            marginClass="mb-12"
        />
    </x-slot>

    <x-slot name="belowHeroContent_2">
        {{-- Sezione: Ultime Gallerie Arrivate con effetti NFT --}}
        <x-collections-carousel
            :collections="$latestCollections"
            title={{ __('guest_home.latest_galleries_title') }}
            bgClass="bg-gray-900"
            marginClass="mb-12"
        />
    </x-slot>

    {{-- Sezione: Protagonisti e Attori dell'Ecosistema --}}
    {{-- POPOLIAMO IL NUOVO SLOT $actorContent CON IL NOSTRO COMPONENTE actors-section --}}
    <x-slot name="actorContent">
        <x-actors-section /> {{-- Il componente che contiene la griglia delle 4 card attore --}}
    </x-slot>

    <x-slot name="heroNatanAssistant">
        <x-natan-assistant />
    </x-slot>

    {{-- Sezione: Progetti Ambientali (EPP) NFT Style --}}

    <x-epp-cta-banner
        :title="__('guest_home.epp_banner_title')"
        :subtitle="__('guest_home.epp_banner_subtitle')"
        :message="__('guest_home.epp_banner_message_v2')" {{-- Usa un messaggio specifico che enfatizzi protezione/recupero --}}
        :ctaText="__('guest_home.epp_banner_cta')"
        ctaLink="{{ route('archetypes.patron') }}"
        {{-- Scegli un'immagine di sfondo appropriata per gli EPP --}}
        {{-- backgroundImage="{{ asset('images/banners/forest_regeneration.jpg') }}" --}}
        heightClass="min-h-[50vh] md:min-h-[65vh]"
        overlayColor="bg-gradient-to-br from-gray-900/80 via-verde-rinascita/50 to-gray-900/80" {{-- Overlay più brandizzato --}}
    />




</x-guest-layout>
