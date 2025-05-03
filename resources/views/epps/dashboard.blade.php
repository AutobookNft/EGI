<!-- resources/views/epps/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'EPP Impact Dashboard | FlorenceEGI')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/epp-dashboard.css') }}">
@endsection

@section('content')
<div class="epp-dashboard-container">
    <header class="dashboard-header">
        <h1>Environment Protection Programs Impact Dashboard</h1>
        <p class="dashboard-subtitle">Real-time monitoring of your contribution to the ecosystem regeneration</p>

        <div class="dashboard-global-metrics">
            <div class="global-metric-card" id="total-contribution">
                <span class="metric-value">€{{ number_format($globalMetrics['totalContribution'], 2) }}</span>
                <span class="metric-label">Total Contribution</span>
            </div>
            <div class="global-metric-card" id="active-epps">
                <span class="metric-value">{{ $globalMetrics['activeEpps'] }}</span>
                <span class="metric-label">Active Programs</span>
            </div>
            <div class="global-metric-card" id="community-members">
                <span class="metric-value">{{ $globalMetrics['contributorsCount'] }}</span>
                <span class="metric-label">Contributing Members</span>
            </div>
        </div>
    </header>

    <section class="dashboard-filters">
        <div class="filter-container">
            <label for="epp-type-filter">Program Type</label>
            <select id="epp-type-filter" class="epp-filter">
                <option value="all">All Programs</option>
                <option value="ARF">Appropriate Restoration Forestry</option>
                <option value="APR">Aquatic Plastic Removal</option>
                <option value="BPE">Bee Population Enhancement</option>
            </select>
        </div>

        <div class="filter-container">
            <label for="time-period-filter">Time Period</label>
            <select id="time-period-filter" class="epp-filter">
                <option value="all">All Time</option>
                <option value="month">Last Month</option>
                <option value="quarter">Last Quarter</option>
                <option value="year">Last Year</option>
            </select>
        </div>

        <div class="filter-container">
            <label for="metrics-order">Sort By</label>
            <select id="metrics-order" class="epp-filter">
                <option value="contribution-desc">Highest Contribution</option>
                <option value="contribution-asc">Lowest Contribution</option>
                <option value="impact-desc">Highest Impact</option>
                <option value="impact-asc">Lowest Impact</option>
                <option value="date-desc">Most Recent</option>
                <option value="date-asc">Oldest</option>
            </select>
        </div>
    </section>

    <div class="dashboard-main">
        <section class="impact-visualization">
            <div class="chart-container">
                <h2>Contribution Distribution by Program Type</h2>
                <canvas id="distribution-chart"></canvas>
            </div>

            <div class="chart-container">
                <h2>Environmental Impact Growth</h2>
                <canvas id="impact-growth-chart"></canvas>
            </div>
        </section>

        <section class="impact-by-type">
            <!-- ARF Impact Section -->
            <div class="impact-type-container" id="arf-impact">
                <div class="impact-type-header">
                    <h2>
                        <span class="impact-icon arf-icon"></span>
                        Appropriate Restoration Forestry
                    </h2>
                </div>

                <div class="impact-metrics">
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['ARF']['treesPlanted']) }}</span>
                        <span class="metric-label">Trees Planted</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['ARF']['hectaresRestored'], 2) }}</span>
                        <span class="metric-label">Hectares Restored</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['ARF']['co2Sequestered'], 2) }}</span>
                        <span class="metric-label">Tons CO2 Sequestered</span>
                    </div>
                </div>

                <div class="arf-visualization">
                    <canvas id="arf-progress-chart"></canvas>
                </div>

                <div class="active-projects">
                    <h3>Active Projects</h3>
                    <ul class="project-list">
                        @foreach($arfProjects as $project)
                        <li class="project-item">
                            <div class="project-info">
                                <h4>{{ $project->name }}</h4>
                                <p>{{ $project->description }}</p>
                                <div class="project-metrics">
                                    <span>{{ number_format($project->metrics['treesPlanted']) }} trees</span>
                                    <span>{{ number_format($project->metrics['hectaresRestored'], 2) }} hectares</span>
                                </div>
                            </div>
                            <div class="project-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $project->getCompletionPercentageAttribute() }}%;"></div>
                                    <span class="progress-percentage">{{ $project->getCompletionPercentageAttribute() }}%</span>
                                </div>
                                <span class="progress-percentage">{{ $project->completion_percentage }}%</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- APR Impact Section -->
            <div class="impact-type-container" id="apr-impact">
                <div class="impact-type-header">
                    <h2>
                        <span class="impact-icon apr-icon"></span>
                        Aquatic Plastic Removal
                    </h2>
                </div>

                <div class="impact-metrics">
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['APR']['plasticRemoved'], 2) }}</span>
                        <span class="metric-label">Tons of Plastic Removed</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['APR']['oceanAreaCleaned'], 2) }}</span>
                        <span class="metric-label">Ocean Area Cleaned (km²)</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['APR']['marineLifeSaved']) }}</span>
                        <span class="metric-label">Marine Life Saved (est.)</span>
                    </div>
                </div>

                <div class="apr-visualization">
                    <canvas id="apr-progress-chart"></canvas>
                </div>

                <div class="active-projects">
                    <h3>Active Projects</h3>
                    <ul class="project-list">
                        @foreach($aprProjects as $project)
                        <li class="project-item">
                            <div class="project-info">
                                <h4>{{ $project->name }}</h4>
                                <p>{{ $project->description }}</p>
                                <div class="project-metrics">
                                    <span>{{ number_format($project->metrics['plasticRemoved'], 2) }} tons</span>
                                    <span>{{ number_format($project->metrics['oceanAreaCleaned'], 2) }} km²</span>
                                </div>
                            </div>
                            <div class="project-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $project->completion_percentage }}%;"></div>
                                </div>
                                <span class="progress-percentage">{{ $project->completion_percentage }}%</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- BPE Impact Section -->
            <div class="impact-type-container" id="bpe-impact">
                <div class="impact-type-header">
                    <h2>
                        <span class="impact-icon bpe-icon"></span>
                        Bee Population Enhancement
                    </h2>
                </div>

                <div class="impact-metrics">
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['BPE']['hivesCreated']) }}</span>
                        <span class="metric-label">Hives Created</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['BPE']['beesProtected']) }}</span>
                        <span class="metric-label">Bees Protected</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">{{ number_format($typeMetrics['BPE']['pollinatedArea'], 2) }}</span>
                        <span class="metric-label">Pollinated Area (km²)</span>
                    </div>
                </div>

                <div class="bpe-visualization">
                    <canvas id="bpe-progress-chart"></canvas>
                </div>

                <div class="active-projects">
                    <h3>Active Projects</h3>
                    <ul class="project-list">
                        @foreach($bpeProjects as $project)
                        <li class="project-item">
                            <div class="project-info">
                                <h4>{{ $project->name }}</h4>
                                <p>{{ $project->description }}</p>
                                <div class="project-metrics">
                                    <span>{{ number_format($project->metrics['hivesCreated']) }} hives</span>
                                    <span>{{ number_format($project->metrics['beesProtected']) }} bees</span>
                                </div>
                            </div>
                            <div class="project-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $project->completion_percentage }}%;"></div>
                                </div>
                                <span class="progress-percentage">{{ $project->completion_percentage }}%</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        <section class="milestone-tracker">
            <h2>Recent Milestones Achieved</h2>
            <div class="milestone-timeline">
                @foreach($recentMilestones as $milestone)
                <div class="milestone-item">
                    <div class="milestone-date">{{ $milestone->completion_date->format('M d, Y') }}</div>
                    <div class="milestone-content">
                        <h3>{{ $milestone->title }}</h3>
                        <p>{{ $milestone->description }}</p>
                        <div class="milestone-metrics">
                            @if($milestone->epp->type == 'ARF')
                                <span>{{ number_format($milestone->metrics['treesPlanted']) }} trees planted</span>
                            @elseif($milestone->epp->type == 'APR')
                                <span>{{ number_format($milestone->metrics['plasticRemoved'], 2) }} tons of plastic removed</span>
                            @elseif($milestone->epp->type == 'BPE')
                                <span>{{ number_format($milestone->metrics['hivesCreated']) }} new hives created</span>
                            @endif
                        </div>
                        <a href="{{ route('epps.show', $milestone->epp->id) }}" class="milestone-link">View project details</a>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="community-contribution">
        <h2>Join Our Mission</h2>
        <p>Every EGI you mint or rebind directly contributes to our environmental protection initiatives. Your participation makes a real, measurable difference.</p>

        <div class="cta-container">
            <a href="{{ route('collections.index') }}" class="cta-button">Explore Collections</a>
            <a href="{{ route('epps.index') }}" class="cta-button">Learn More About EPP</a>
        </div>
    </section>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="{{ asset('js/epp-interactions.js') }}"></script>
@endsection
