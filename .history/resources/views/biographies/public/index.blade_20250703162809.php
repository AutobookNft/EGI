{{--
/**
 * @Oracode Biography Public Index - Showcase Rinascimento Digitale
 * 🎯 Purpose: Public discovery page for biographies with SEO optimization
 * 🛡️ Privacy: Only published biographies, GDPR-compliant display
 * 🧱 Core Logic: Grid layout, filtering, search, Schema.org markup
 *
 * @package Resources\Views\Biographies
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-03
 */
--}}

<x-guest-layout
    :title="__('biography.public_index.page_title')"
    :meta-description="__('biography.public_index.meta_description')">

    {{-- Schema.org Markup --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "{{ __('biography.public_index.schema_name') }}",
            "description": "{{ __('biography.public_index.schema_description') }}",
            "url": "{{ route('biographies.public.index') }}",
            "mainEntity": {
                "@type": "ItemList",
                "name": "{{ __('biography.public_index.collection_name') }}",
                "description": "{{ __('biography.public_index.collection_description') }}",
                "numberOfItems": {{ $biographies->total() ?? 0 }},
                "itemListElement": [
                    @foreach($biographies ?? [] as $index => $biography)
                    {
                        "@type": "ListItem",
                        "position": {{ $index + 1 }},
                        "item": {
                            "@type": "CreativeWork",
                            "@id": "{{ route('biographies.public.show', $biography->slug) }}",
                            "name": "{{ $biography->title }}",
                            "description": "{{ $biography->content_preview }}",
                            "author": {
                                "@type": "Person",
                                "name": "{{ $biography->user->name }}"
                            },
                            "datePublished": "{{ $biography->created_at->toISOString() }}",
                            "genre": "{{ __('biography.schema.genre') }}"
                        }
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "{{ __('site.home') }}",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ __('biography.public_index.breadcrumb') }}",
                        "item": "{{ route('biographies.public.index') }}"
                    }
                ]
            },
            "publisher": {
                "@type": "Organization",
                "name": "FlorenceEGI",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/logo/logo_1.webp') }}"
                }
            }
        }
        </script>
    </x-slot>

    {{-- SEO Meta Extra --}}
    <x-slot name="headExtra">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ __('biography.public_index.og_title') }}">
        <meta property="og:description" content="{{ __('biography.public_index.og_description') }}">
        <meta property="og:url" content="{{ route('biographies.public.index') }}">
        <meta property="og:image" content="{{ asset('images/biography/og-image.webp') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ __('biography.public_index.twitter_title') }}">
        <meta name="twitter:description" content="{{ __('biography.public_index.twitter_description') }}">
        <link rel="canonical" href="{{ route('biographies.public.index') }}">
        <link rel="alternate" hreflang="it" href="{{ route('biographies.public.index') }}">
    </x-slot>

    {{-- Hero Section --}}
    <x-slot name="heroFullWidth">
        <div class="biography-hero-showcase" role="banner" aria-labelledby="hero-title">
            <div class="hero-content-container">
                <h1 id="hero-title" class="hero-main-title">
                    {{ __('biography.public_index.hero_title') }}
                </h1>
                <p class="hero-subtitle" id="hero-description">
                    {{ __('biography.public_index.hero_subtitle') }}
                </p>

                {{-- Call to Action --}}
                <div class="hero-actions" role="group" aria-labelledby="hero-actions-label">
                    <span id="hero-actions-label" class="sr-only">{{ __('biography.public_index.hero_actions_label') }}</span>

                    <a href="{{ route('biographies.create') }}"
                       class="btn-primary-fgi"
                       aria-describedby="hero-description">
                        <span class="material-symbols-outlined btn-icon" aria-hidden="true">auto_stories</span>
                        {{ __('biography.public_index.create_biography_btn') }}
                    </a>

                    <a href="{{ route('biographies.onboarding') }}"
                       class="btn-secondary-fgi"
                       aria-describedby="hero-description">
                        <span class="material-symbols-outlined btn-icon" aria-hidden="true">info</span>
                        {{ __('biography.public_index.learn_more_btn') }}
                    </a>
                </div>

                {{-- Stats Section --}}
                <div class="hero-stats" role="region" aria-labelledby="stats-heading">
                    <h2 id="stats-heading" class="sr-only">{{ __('biography.public_index.stats_heading') }}</h2>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number" aria-describedby="stat-biographies">{{ $totalBiographies ?? 0 }}</span>
                            <span class="stat-label" id="stat-biographies">{{ __('biography.public_index.stat_biographies') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" aria-describedby="stat-chapters">{{ $totalChapters ?? 0 }}</span>
                            <span class="stat-label" id="stat-chapters">{{ __('biography.public_index.stat_chapters') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" aria-describedby="stat-stories">{{ $totalStories ?? 0 }}</span>
                            <span class="stat-label" id="stat-stories">{{ __('biography.public_index.stat_stories') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Main Content --}}
    <main class="biography-listing-container" role="main" aria-labelledby="main-content-heading">

        {{-- Page Header with Navigation --}}
        <header class="listing-header" role="banner">
            <nav aria-label="{{ __('biography.public_index.breadcrumb_nav_label') }}">
                <ol class="breadcrumb-nav" role="list">
                    <li role="listitem">
                        <a href="{{ url('/') }}" class="breadcrumb-link">
                            <span class="material-symbols-outlined breadcrumb-icon" aria-hidden="true">home</span>
                            {{ __('site.home') }}
                        </a>
                    </li>
                    <li role="listitem" aria-current="page">
                        <span class="breadcrumb-current">{{ __('biography.public_index.breadcrumb') }}</span>
                    </li>
                </ol>
            </nav>

            <div class="page-title-section">
                <h1 id="main-content-heading" class="page-main-title">
                    {{ __('biography.public_index.main_title') }}
                </h1>
                <p class="page-description">
                    {{ __('biography.public_index.main_description') }}
                </p>
            </div>
        </header>

        {{-- Filtering and Search Section --}}
        <section class="biography-filters" role="search" aria-labelledby="filter-heading">
            <h2 id="filter-heading" class="filter-section-title">
                {{ __('biography.public_index.filter_title') }}
            </h2>

            <form class="filter-form" method="GET" action="{{ route('biographies.public.index') }}"
                  aria-label="{{ __('biography.public_index.filter_form_label') }}">

                <div class="filter-row">
                    {{-- Search Input --}}
                    <div class="search-field">
                        <label for="search" class="search-label">
                            {{ __('biography.public_index.search_label') }}
                        </label>
                        <div class="search-input-wrapper">
                            <span class="material-symbols-outlined search-icon" aria-hidden="true">search</span>
                            <input type="search"
                                   id="search"
                                   name="search"
                                   class="search-input"
                                   placeholder="{{ __('biography.public_index.search_placeholder') }}"
                                   value="{{ request('search') }}"
                                   aria-describedby="search-help">
                            <span id="search-help" class="sr-only">{{ __('biography.public_index.search_help') }}</span>
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div class="filter-field">
                        <label for="type" class="filter-label">
                            {{ __('biography.public_index.type_label') }}
                        </label>
                        <select id="type" name="type" class="filter-select" aria-describedby="type-help">
                            <option value="">{{ __('biography.public_index.type_all') }}</option>
                            <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>
                                {{ __('biography.public_index.type_single') }}
                            </option>
                            <option value="chapters" {{ request('type') === 'chapters' ? 'selected' : '' }}>
                                {{ __('biography.public_index.type_chapters') }}
                            </option>
                        </select>
                        <span id="type-help" class="sr-only">{{ __('biography.public_index.type_help') }}</span>
                    </div>

                    {{-- Sort Filter --}}
                    <div class="filter-field">
                        <label for="sort" class="filter-label">
                            {{ __('biography.public_index.sort_label') }}
                        </label>
                        <select id="sort" name="sort" class="filter-select" aria-describedby="sort-help">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                                {{ __('biography.public_index.sort_newest') }}
                            </option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                {{ __('biography.public_index.sort_oldest') }}
                            </option>
                            <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>
                                {{ __('biography.public_index.sort_title') }}
                            </option>
                        </select>
                        <span id="sort-help" class="sr-only">{{ __('biography.public_index.sort_help') }}</span>
                    </div>

                    {{-- Filter Actions --}}
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter-apply" aria-describedby="apply-help">
                            <span class="material-symbols-outlined btn-icon" aria-hidden="true">filter_list</span>
                            {{ __('biography.public_index.apply_filters') }}
                        </button>
                        <a href="{{ route('biographies.public.index') }}"
                           class="btn-filter-reset"
                           aria-describedby="reset-help">
                            <span class="material-symbols-outlined btn-icon" aria-hidden="true">clear</span>
                            {{ __('biography.public_index.reset_filters') }}
                        </a>
                        <span id="apply-help" class="sr-only">{{ __('biography.public_index.apply_help') }}</span>
                        <span id="reset-help" class="sr-only">{{ __('biography.public_index.reset_help') }}</span>
                    </div>
                </div>
            </form>
        </section>

        {{-- Results Summary --}}
        @if($biographies && $biographies->count() > 0)
            <div class="results-summary" role="status" aria-live="polite">
                <p class="results-text">
                    {{ __('biography.public_index.results_summary', [
                        'showing' => $biographies->count(),
                        'total' => $biographies->total(),
                        'term' => request('search') ? ' "' . request('search') . '"' : ''
                    ]) }}
                </p>
            </div>
        @endif

        {{-- Biography Grid --}}
        <section class="biography-grid-section" aria-labelledby="biography-list-heading">
            <h2 id="biography-list-heading" class="sr-only">
                {{ __('biography.public_index.biography_list_heading') }}
            </h2>

            @if($biographies && $biographies->count() > 0)
                <div class="biography-grid" role="list" aria-label="{{ __('biography.public_index.biography_grid_label') }}">
                    @foreach($biographies as $biography)
                        <article class="biography-card" role="listitem" aria-labelledby="bio-title-{{ $biography->id }}">
                            <div class="card-content">
                                {{-- Featured Image --}}
                                @if($biography->getFirstMediaUrl('featured_image'))
                                    <div class="card-image">
                                        <img src="{{ $biography->getFirstMediaUrl('featured_image', 'web') }}"
                                             alt="{{ __('biography.public_index.featured_image_alt', ['title' => $biography->title]) }}"
                                             class="biography-image"
                                             loading="lazy"
                                             decoding="async">
                                    </div>
                                @endif

                                {{-- Content --}}
                                <div class="card-body">
                                    <header class="card-header">
                                        <h3 id="bio-title-{{ $biography->id }}" class="biography-title">
                                            <a href="{{ route('biographies.public.show', $biography->slug) }}"
                                               class="biography-link"
                                               aria-describedby="bio-meta-{{ $biography->id }}">
                                                {{ $biography->title }}
                                            </a>
                                        </h3>

                                        <div id="bio-meta-{{ $biography->id }}" class="biography-meta">
                                            <span class="author-name">
                                                <span class="material-symbols-outlined meta-icon" aria-hidden="true">person</span>
                                                {{ $biography->user->name }}
                                            </span>
                                            <span class="biography-type">
                                                <span class="material-symbols-outlined meta-icon" aria-hidden="true">
                                                    {{ $biography->type === 'chapters' ? 'menu_book' : 'article' }}
                                                </span>
                                                {{ __('biography.types.' . $biography->type) }}
                                            </span>
                                            <time datetime="{{ $biography->created_at->toISOString() }}" class="creation-date">
                                                <span class="material-symbols-outlined meta-icon" aria-hidden="true">schedule</span>
                                                {{ $biography->created_at->diffForHumans() }}
                                            </time>
                                        </div>
                                    </header>

                                    <div class="card-excerpt">
                                        <p class="biography-preview">
                                            {{ $biography->content_preview }}
                                        </p>
                                    </div>

                                    {{-- Reading Time & Chapters Info --}}
                                    <footer class="card-footer">
                                        <div class="reading-info">
                                            <span class="reading-time">
                                                <span class="material-symbols-outlined info-icon" aria-hidden="true">schedule</span>
                                                {{ __('biography.public_index.reading_time', ['minutes' => $biography->getEstimatedReadingTime()]) }}
                                            </span>

                                            @if($biography->type === 'chapters' && $biography->publishedChapters->count() > 0)
                                                <span class="chapters-count">
                                                    <span class="material-symbols-outlined info-icon" aria-hidden="true">collections_bookmark</span>
                                                    {{ __('biography.public_index.chapters_count', ['count' => $biography->publishedChapters->count()]) }}
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('biographies.public.show', $biography->slug) }}"
                                           class="btn-read-more"
                                           aria-label="{{ __('biography.public_index.read_biography_aria', ['title' => $biography->title]) }}">
                                            {{ __('biography.public_index.read_more') }}
                                            <span class="material-symbols-outlined btn-icon" aria-hidden="true">arrow_forward</span>
                                        </a>
                                    </footer>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($biographies->hasPages())
                    <nav class="pagination-nav" role="navigation" aria-label="{{ __('biography.public_index.pagination_nav_label') }}">
                        {{ $biographies->links('pagination::custom-pagination') }}
                    </nav>
                @endif

            @else
                {{-- Empty State --}}
                <div class="empty-state" role="status">
                    <div class="empty-content">
                        <span class="material-symbols-outlined empty-icon" aria-hidden="true">auto_stories</span>
                        <h3 class="empty-title">{{ __('biography.public_index.empty_title') }}</h3>
                        <p class="empty-description">{{ __('biography.public_index.empty_description') }}</p>

                        <div class="empty-actions">
                            <a href="{{ route('biographies.create') }}" class="btn-primary-fgi">
                                <span class="material-symbols-outlined btn-icon" aria-hidden="true">add</span>
                                {{ __('biography.public_index.create_first_biography') }}
                            </a>

                            @if(request()->hasAny(['search', 'type', 'sort']))
                                <a href="{{ route('biographies.public.index') }}" class="btn-secondary-fgi">
                                    <span class="material-symbols-outlined btn-icon" aria-hidden="true">clear</span>
                                    {{ __('biography.public_index.clear_filters') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </main>

    {{-- Additional Scripts --}}
    @push('scripts')
        <script>
            // Auto-submit filter form on select change for better UX
            document.querySelectorAll('.filter-select').forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });
        </script>
    @endpush
</x-guest-layout>
