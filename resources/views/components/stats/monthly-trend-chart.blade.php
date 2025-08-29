@props([
    'creatorId' => null,
    'monthlyTrend' => null,
    'months' => 6,
    'chartId' => null
])

@php
use App\Models\PaymentDistribution;

// Se non vengono passati i trend, li calcola
if (!$monthlyTrend && $creatorId) {
    $monthlyTrend = PaymentDistribution::getCreatorMonthlyEarnings($creatorId, $months);
}

// Fallback se non ci sono dati
$monthlyTrend = $monthlyTrend ?? [];

// Genera un ID unico per il chart se non fornito
$chartId = $chartId ?? 'monthlyTrendChart_' . uniqid();

// Prepara i dati per Chart.js
$chartData = [
    'labels' => [],
    'earnings' => [],
    'sales' => []
];

// Riempi i dati degli ultimi mesi (anche quelli senza dati)
for ($i = $months - 1; $i >= 0; $i--) {
    $monthKey = now()->subMonths($i)->format('Y-m');
    $monthLabel = now()->subMonths($i)->format('M Y');

    $monthData = collect($monthlyTrend)->firstWhere('month', $monthKey);

    $chartData['labels'][] = $monthLabel;
    $chartData['earnings'][] = $monthData['monthly_earnings'] ?? 0;
    $chartData['sales'][] = $monthData['sales_count'] ?? 0;
}
@endphp

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center space-x-2">
            <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>{{ __('creator.portfolio.trends.title') }}</span>
        </h2>
        <div class="text-sm text-gray-400">
            {{ __('creator.portfolio.trends.subtitle', ['months' => $months]) }}
        </div>
    </div>

    {{-- Chart Container --}}
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @if(count(array_filter($chartData['earnings'])) > 0)
            {{-- Chart Canvas --}}
            <div class="relative">
                <canvas id="{{ $chartId }}" class="w-full" style="height: 300px;"></canvas>
            </div>

            {{-- Chart Legend & Stats --}}
            <div class="mt-4 pt-4 border-t border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-sm">
                    @php
                        $totalEarnings = array_sum($chartData['earnings']);
                        $totalSales = array_sum($chartData['sales']);
                        $avgMonthlyEarnings = count(array_filter($chartData['earnings'])) > 0 ? $totalEarnings / count(array_filter($chartData['earnings'])) : 0;
                        $trend = count($chartData['earnings']) >= 2 ?
                            ($chartData['earnings'][count($chartData['earnings'])-1] > $chartData['earnings'][count($chartData['earnings'])-2] ? 'up' :
                            ($chartData['earnings'][count($chartData['earnings'])-1] < $chartData['earnings'][count($chartData['earnings'])-2] ? 'down' : 'stable')) : 'stable';
                    @endphp

                    <div>
                        <p class="font-semibold text-white">€{{ number_format($totalEarnings, 2) }}</p>
                        <p class="text-gray-400">{{ __('creator.portfolio.trends.total_period') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-white">€{{ number_format($avgMonthlyEarnings, 2) }}</p>
                        <p class="text-gray-400">{{ __('creator.portfolio.trends.avg_monthly') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ number_format($totalSales) }}</p>
                        <p class="text-gray-400">{{ __('creator.portfolio.trends.total_sales') }}</p>
                    </div>
                    <div class="flex items-center justify-center space-x-1">
                        @if($trend === 'up')
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="font-semibold text-green-400">{{ __('creator.portfolio.trends.trending_up') }}</span>
                        @elseif($trend === 'down')
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            <span class="font-semibold text-red-400">{{ __('creator.portfolio.trends.trending_down') }}</span>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                            <span class="font-semibold text-gray-400">{{ __('creator.portfolio.trends.stable') }}</span>
                        @endif
                    </div>
                </div>
            </div>

        @else
            {{-- No Data Message --}}
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">{{ __('creator.portfolio.trends.no_data_title') }}</h3>
                <p class="text-gray-400">{{ __('creator.portfolio.trends.no_data_description') }}</p>
            </div>
        @endif
    </div>
</div>

@if(count(array_filter($chartData['earnings'])) > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('{{ $chartId }}').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: '{{ __("creator.portfolio.trends.earnings") }}',
                            data: @json($chartData['earnings']),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(34, 197, 94)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        },
                        {
                            label: '{{ __("creator.portfolio.trends.sales_count") }}',
                            data: @json($chartData['sales']),
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(168, 85, 247)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#d1d5db',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(31, 41, 55, 0.9)',
                            titleColor: '#ffffff',
                            bodyColor: '#d1d5db',
                            borderColor: '#6b7280',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return context.dataset.label + ': €' + context.parsed.y.toFixed(2);
                                    } else {
                                        return context.dataset.label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(107, 114, 128, 0.2)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: 'rgba(107, 114, 128, 0.2)'
                            },
                            ticks: {
                                color: '#9ca3af',
                                callback: function(value) {
                                    return '€' + value.toFixed(0);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });
    </script>
    @endpush
@endif
