{{-- views/epps/index.blade.php --}}
{{-- Vista principale che elenca tutti gli EPP --}}

@extends('layouts.app')

@section('title', 'Environmental Protection Programs - FlorenceEGI')

@section('content')
<div class="epps-container">
    {{-- Header della sezione --}}
    <div class="epps-header">
        <div class="header-content">
            <h1 class="section-title">Environmental Protection Programs</h1>
            <p class="section-description">
                Discover the environmental initiatives supported by FlorenceEGI. Every EGI transaction contributes
                directly to these projects, creating a measurable positive impact on our planet.
            </p>
        </div>
    </div>

    {{-- Statistiche generali --}}
    <div class="epps-stats">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="stats-content">
                <h3>{{ $epps->total() }}</h3>
                <p>Active Projects</p>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="stats-content">
                <h3>{{ \App\Models\Collection::whereNotNull('epp_id')->count() }}</h3>
                <p>Supporting Collections</p>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stats-content">
                <h3>{{ number_format(\App\Models\EppTransaction::sum('amount'), 2) }} ALGO</h3>
                <p>Total Contributions</p>
            </div>
        </div>
    </div>

    {{-- Filtri per gli EPP --}}
    <div class="epps-filter-bar">
        <form action="{{ route('epps.index') }}" method="GET" id="filterForm">
            <div class="filter-group">
                <label for="type">Project Type:</label>
                <select name="type" id="type" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Types</option>
                    <option value="ARF" {{ request('type') == 'ARF' ? 'selected' : '' }}>Reforestation (ARF)</option>
                    <option value="APR" {{ request('type') == 'APR' ? 'selected' : '' }}>Ocean Cleanup (APR)</option>
                    <option value="BPE" {{ request('type') == 'BPE' ? 'selected' : '' }}>Bee Protection (BPE)</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="funds" {{ request('sort') == 'funds' ? 'selected' : '' }}>Most Funded</option>
                    <option value="collections" {{ request('sort') == 'collections' ? 'selected' : '' }}>Most Collections</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Griglia degli EPP --}}
    <div class="epps-grid">
        @forelse ($epps as $epp)
            <div class="epp-card">
                {{-- Immagine dell'EPP --}}
                <div class="epp-image">
                    @if ($epp->image_path)
                        <img src="{{ asset('storage/' . $epp->image_path) }}" alt="{{ $epp->name }}" loading="lazy">
                    @else
                        <div class="placeholder-image">
                            @switch($epp->type)
                                @case('ARF')
                                    <i class="fas fa-tree"></i>
                                    @break
                                @case('APR')
                                    <i class="fas fa-water"></i>
                                    @break
                                @case('BPE')
                                    <i class="fas fa-bug"></i>
                                    @break
                                @default
                                    <i class="fas fa-leaf"></i>
                            @endswitch
                        </div>
                    @endif

                    {{-- Badge con il tipo di EPP --}}
                    <div class="epp-type-badge {{ $epp->type }}">
                        {{ $epp->type }}
                    </div>
                </div>

                {{-- Contenuto della card --}}
                <div class="epp-content">
                    <h3 class="epp-title">
                        <a href="{{ route('epps.show', $epp->id) }}">{{ $epp->name }}</a>
                    </h3>

                    <p class="epp-description">
                        {{ Str::limit($epp->description, 120) }}
                    </p>

                    {{-- Barra di progresso --}}
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Progress</span>
                            <span>{{ $epp->completion_percentage }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $epp->completion_percentage }}%"></div>
                        </div>
                    </div>

                    {{-- Statistiche dell'EPP --}}
                    <div class="epp-stats">
                        <div class="epp-stat">
                            <i class="fas fa-coins"></i>
                            <span>{{ number_format($epp->transactions_sum_amount ?? 0, 2) }} ALGO</span>
                        </div>

                        <div class="epp-stat">
                            <i class="fas fa-cubes"></i>
                            <span>{{ $epp->collections_count ?? 0 }} Collections</span>
                        </div>

                        <div class="epp-stat">
                            <i class="fas fa-exchange-alt"></i>
                            <span>{{ $epp->transactions_count ?? 0 }} Transactions</span>
                        </div>
                    </div>
                </div>

                {{-- Footer della card --}}
                <div class="epp-footer">
                    <a href="{{ route('epps.show', $epp->id) }}" class="btn-view">
                        View Details
                    </a>

                    @if ($epp->collections_count > 0)
                        <a href="{{ route('collections.index', ['epp' => $epp->id]) }}" class="btn-collections">
                            View Collections
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>No EPPs found</h3>
                <p>No Environmental Protection Programs match your current filters.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginazione --}}
    <div class="epps-pagination">
        {{ $epps->appends(request()->query())->links() }}
    </div>

    {{-- Sezione informativa --}}
    <div class="epp-info-section">
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-tree"></i>
            </div>
            <div class="info-content">
                <h3>Reforestation (ARF)</h3>
                <p>
                    Appropriate Restoration Forestry focuses on planting native trees to restore ecosystems,
                    combat climate change, and reverse deforestation.
                </p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-water"></i>
            </div>
            <div class="info-content">
                <h3>Ocean Cleanup (APR)</h3>
                <p>
                    Aquatic Plastic Removal projects are dedicated to removing plastic pollution from
                    oceans, rivers, and other water bodies to protect marine life.
                </p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-bug"></i>
            </div>
            <div class="info-content">
                <h3>Bee Protection (BPE)</h3>
                <p>
                    Bee Population Enhancement initiatives support and grow healthy bee populations,
                    vital pollinators essential for global food security and biodiversity.
                </p>
            </div>
        </div>
    </div>

    {{-- Call to action --}}
    <div class="cta-section">
        <h2>Make a Difference</h2>
        <p>
            Every EGI transaction automatically contributes to these environmental projects.
            Create or purchase an EGI today to support our planet's future.
        </p>
        <div class="cta-buttons">
            <a href="{{ route('collections.index') }}" class="btn-primary">
                Explore Collections
            </a>
            <a href="{{ route('epps.dashboard') }}" class="btn-secondary">
                View Impact Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/epp-styles.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/epp-interactions.js') }}"></script>
@endpush
