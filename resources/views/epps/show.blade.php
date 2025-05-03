{{-- views/epps/show.blade.php --}}
{{-- Vista dettagliata di un singolo EPP --}}

@extends('layouts.app')

@section('title', $epp->name . ' - Environmental Protection Program - FlorenceEGI')

@section('content')
<div class="epp-detail-container">
    {{-- Header della pagina EPP --}}
    <div class="epp-header" 
         @if($epp->image_path) 
         style="background-image: url('{{ asset('storage/' . $epp->image_path) }}');"
         @endif>
        <div class="epp-header-overlay">
            <div class="epp-header-content">
                {{-- Torna alla lista degli EPP --}}
                <a href="{{ route('epps.index') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to EPPs
                </a>
                
                <div class="epp-header-info">
                    {{-- Badge con il tipo di EPP --}}
                    <div class="epp-type-badge {{ $epp->type }}">
                        {{ $epp->type_name }}
                    </div>
                    
                    <h1 class="epp-name">{{ $epp->name }}</h1>
                    
                    {{-- Barra di progresso --}}
                    <div class="progress-container large">
                        <div class="progress-label">
                            <span>Progress: {{ $epp->completion_percentage }}%</span>
                            <span>{{ number_format($totalFunds, 2) }} / {{ number_format($epp->target_funds, 2) }} ALGO</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $epp->completion_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Corpo principale con le informazioni EPP --}}
    <div class="epp-body">
        <div class="epp-sidebar">
            {{-- Descrizione dell'EPP --}}
            <div class="epp-description-card">
                <h3>About this Project</h3>
                <div class="description-content">
                    {{ $epp->description }}
                </div>
                
                {{-- Organizzazione che gestisce l'EPP --}}
                @if($epp->organization)
                <div class="organization-info">
                    <h4>Managed by</h4>
                    <div class="organization-details">
                        @if($epp->organization->logo_path)
                            <img src="{{ asset('storage/' . $epp->organization->logo_path) }}" alt="{{ $epp->organization->name }}" class="organization-logo">
                        @endif
                        <div class="organization-meta">
                            <div class="organization-name">{{ $epp->organization->name }}</div>
                            @if($epp->organization->website)
                                <a href="{{ $epp->organization->website }}" target="_blank" class="organization-website">
                                    <i class="fas fa-external-link-alt"></i> Website
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- Status dell'EPP --}}
                <div class="epp-status">
                    <div class="status-label">
                        Status: <span class="status-value {{ $epp->status }}">{{ ucfirst($epp->status) }}</span>
                    </div>
                    <div class="status-label">
                        Type: <span class="status-value">{{ $epp->type_name }}</span>
                    </div>
                    <div class="status-label">
                        Created: <span class="status-value">{{ $epp->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Statistiche di impatto --}}
            <div class="impact-stats-card">
                <h3>Impact Metrics</h3>
                <div class="impact-stats">
                    @if($epp->type === 'ARF')
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-tree"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['trees_planted']) }}</div>
                            <div class="impact-label">Trees Planted</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-cloud"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['co2_offset']) }} kg</div>
                            <div class="impact-label">CO2 Offset Per Year</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['area_restored'], 2) }} ha</div>
                            <div class="impact-label">Area Restored</div>
                        </div>
                    @elseif($epp->type === 'APR')
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-trash"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['plastic_removed']) }} kg</div>
                            <div class="impact-label">Plastic Removed</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-fish"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['marine_life_saved']) }}</div>
                            <div class="impact-label">Marine Life Saved</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-tint"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['water_cleaned']) }} m³</div>
                            <div class="impact-label">Water Cleaned</div>
                        </div>
                    @elseif($epp->type === 'BPE')
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-home"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['beehives_supported']) }}</div>
                            <div class="impact-label">Beehives Supported</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-bug"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['bees_supported']) }}</div>
                            <div class="impact-label">Bees Supported</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-seedling"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['plants_pollinated']) }}</div>
                            <div class="impact-label">Plants Pollinated</div>
                        </div>
                    @else
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-hand-holding-heart"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['contributions']) }}</div>
                            <div class="impact-label">Contributions</div>
                        </div>
                        
                        <div class="impact-stat">
                            <div class="impact-icon"><i class="fas fa-coins"></i></div>
                            <div class="impact-value">{{ number_format($impactMetrics['total_impact'], 2) }} ALGO</div>
                            <div class="impact-label">Total Impact</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="epp-content">
            {{-- Milestones della EPP --}}
            <div class="milestones-section">
                <h2>Project Milestones</h2>
                
                <div class="milestones-timeline">
                    @forelse($epp->milestones as $milestone)
                        <div class="milestone-item {{ $milestone->status }}">
                            <div class="milestone-icon">
                                @switch($milestone->status)
                                    @case('completed')
                                        <i class="fas fa-check-circle"></i>
                                        @break
                                    @case('in_progress')
                                        <i class="fas fa-clock"></i>
                                        @break
                                    @default
                                        <i class="fas fa-hourglass"></i>
                                @endswitch
                            </div>
                            
                            <div class="milestone-content">
                                <h3 class="milestone-title">{{ $milestone->title }}</h3>
                                <p class="milestone-description">{{ $milestone->description }}</p>
                                
                                @if($milestone->status === 'completed')
                                    <div class="milestone-date">
                                        Completed on {{ $milestone->completion_date->format('M d, Y') }}
                                    </div>
                                @elseif($milestone->target_date)
                                    <div class="milestone-date {{ $milestone->isOverdue() ? 'overdue' : '' }}">
                                        @if($milestone->isOverdue())
                                            Overdue since 
                                        @else
                                            Target date: 
                                        @endif
                                        {{ $milestone->target_date->format('M d, Y') }}
                                    </div>
                                @endif
                                
                                @if($milestone->target_value > 0)
                                    <div class="milestone-progress">
                                        <div class="progress-label">
                                            <span>Progress</span>
                                            <span>{{ $milestone->current_value }} / {{ $milestone->target_value }}</span>
                                        </div>
                                        <div class="progress-bar small">
                                            <div class="progress-fill" style="width: {{ $milestone->completion_percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($milestone->evidence_url)
                                    <div class="milestone-evidence">
                                        <a href="{{ $milestone->evidence_url }}" target="_blank" class="evidence-link">
                                            <i class="fas fa-external-link-alt"></i> 
                                            View {{ $milestone->evidence_type ?: 'Evidence' }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-milestones">
                            <p>No milestones have been added for this project yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- Collezioni che supportano questo EPP --}}
            <div class="supporting-collections">
                <h2>Supporting Collections ({{ $totalCollections }})</h2>
                
                <div class="collections-grid">
                    @forelse($collections as $collection)
                        @include('components.collection-card', ['collection' => $collection])
                    @empty
                        <div class="empty-collections">
                            <p>No collections are currently supporting this project.</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- Paginazione delle collezioni --}}
                @if($collections->count() > 0)
                    <div class="collections-pagination">
                        {{ $collections->appends(request()->query())->links() }}
                    </div>
                @endif
                
                {{-- Link per vedere tutte le collezioni --}}
                @if($totalCollections > 6)
                    <div class="view-all-link">
                        <a href="{{ route('collections.index', ['epp' => $epp->id]) }}" class="btn-view-all">
                            View All Supporting Collections
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Call to action --}}
    <div class="epp-cta">
        <div class="cta-content">
            <h2>Support This Project</h2>
            <p>
                Every EGI transaction automatically contributes to environmental protection. 
                Create or purchase an EGI from a collection supporting this project to help make a difference.
            </p>
            <div class="cta-buttons">
                <a href="{{ route('collections.index', ['epp' => $epp->id]) }}" class="btn-primary">
                    Find Supporting Collections
                </a>
                <a href="{{ route('egis.create', ['epp' => $epp->id]) }}" class="btn-secondary">
                    Create Your Own EGI
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/epp-detail.css') }}">
@endpush
