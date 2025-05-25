<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('menu.statistics') }}
        </h2>
    </x-slot>

    {{-- Main Content --}}
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-violet-800">
    <div class="container px-4 py-8 mx-auto">
        {{-- Header Section --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="mb-2 text-4xl font-bold text-white">
                    {{ __('menu.statistics') }}
                </h1>
                <p class="text-gray-300">
                    {{ __('statistics.dashboard_subtitle') }}
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center space-x-4">
                <button
                    id="refresh-stats"
                    class="flex items-center px-4 py-2 space-x-2 text-white transition-colors duration-200 bg-blue-600 rounded-lg hover:bg-blue-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ __('statistics.refresh') }}</span>
                </button>

                <div class="text-sm text-gray-400" id="last-updated">
                    {{ __('statistics.loading') }}...
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="loading-overlay" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="flex items-center p-6 space-x-4 bg-white rounded-lg">
                <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
                <span class="text-gray-700">{{ __('statistics.calculating') }}...</span>
            </div>
        </div>

        {{-- Error State --}}
        <div id="error-container" class="hidden mb-6">
            <div class="px-4 py-3 text-red-100 bg-red-900 border border-red-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span id="error-message">{{ __('statistics.error_loading') }}</span>
                </div>
            </div>
        </div>

        {{-- Statistics Content --}}
        <div id="statistics-content" class="hidden">
            {{-- KPI Summary Boxes --}}
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                {{-- Total Likes --}}
                <div class="p-6 text-white shadow-lg bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-pink-100">{{ __('statistics.total_likes') }}</p>
                            <p class="text-3xl font-bold" id="kpi-total-likes">0</p>
                        </div>
                        <div class="p-3 bg-white rounded-lg bg-opacity-20">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Reservations --}}
                <div class="p-6 text-white shadow-lg bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-100">{{ __('statistics.total_reservations') }}</p>
                            <p class="text-3xl font-bold" id="kpi-total-reservations">0</p>
                            <p class="text-xs text-blue-200" id="kpi-strong-reservations">0 strong</p>
                        </div>
                        <div class="p-3 bg-white rounded-lg bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Amount --}}
                <div class="p-6 text-white shadow-lg bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-100">{{ __('statistics.total_amount') }}</p>
                            <p class="text-3xl font-bold" id="kpi-total-amount">€0</p>
                        </div>
                        <div class="p-3 bg-white rounded-lg bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- EPP Quota --}}
                <div class="p-6 text-white shadow-lg bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-100">{{ __('statistics.epp_quota') }}</p>
                            <p class="text-3xl font-bold" id="kpi-epp-quota">€0</p>
                        </div>
                        <div class="p-3 bg-white rounded-lg bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detailed Statistics --}}
            <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                {{-- Likes by Collection --}}
                <div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
                    <h3 class="mb-4 text-xl font-semibold text-white">{{ __('statistics.likes_by_collection') }}</h3>
                    <div id="likes-by-collection" class="space-y-3">
                        <div class="py-8 text-center text-gray-300">{{ __('statistics.no_data') }}</div>
                    </div>
                </div>

                {{-- Reservations by Collection --}}
                <div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
                    <h3 class="mb-4 text-xl font-semibold text-white">{{ __('statistics.reservations_by_collection') }}</h3>
                    <div id="reservations-by-collection" class="space-y-3">
                        <div class="py-8 text-center text-gray-300">{{ __('statistics.no_data') }}</div>
                    </div>
                </div>
            </div>

            {{-- Top EGIs --}}
            <div class="p-6 mb-8 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
                <h3 class="mb-4 text-xl font-semibold text-white">{{ __('statistics.top_egis') }}</h3>
                <div id="top-egis" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="py-8 text-center text-gray-300 col-span-full">{{ __('statistics.no_data') }}</div>
                </div>
            </div>

            {{-- EPP Breakdown --}}
            <div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
                <h3 class="mb-4 text-xl font-semibold text-white">{{ __('statistics.epp_breakdown') }}</h3>
                <div id="epp-breakdown" class="space-y-4">
                    <div class="py-8 text-center text-gray-300">{{ __('statistics.no_data') }}</div>
                </div>
            </div>
        </div>

        {{-- GDPR Placeholder --}}
        <div class="p-6 mt-8 bg-yellow-900 bg-opacity-50 border border-yellow-700 rounded-xl">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h4 class="font-medium text-yellow-200">{{ __('statistics.gdpr_check') }}</h4>
                    <p class="text-sm text-yellow-300">{{ __('statistics.gdpr_coming_soon') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

{{-- JavaScript for Statistics Loading --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let statisticsData = null;

    // Elements
    const loadingOverlay = document.getElementById('loading-overlay');
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');
    const statisticsContent = document.getElementById('statistics-content');
    const refreshButton = document.getElementById('refresh-stats');
    const lastUpdated = document.getElementById('last-updated');

    // Load statistics on page load
    loadStatistics();

    // Refresh button handler
    refreshButton?.addEventListener('click', function() {
        loadStatistics(true);
    });

    /**
     * Load statistics from API
     */
    async function loadStatistics(forceRefresh = false) {
        showLoading();
        hideError();

        try {
            const url = forceRefresh ? '/dashboard/statistics/data?refresh=1' : '/dashboard/statistics/data';
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (result.success) {
                statisticsData = result.data;
                renderStatistics(statisticsData);
                updateLastUpdated(statisticsData.generated_at);
                showContent();
            } else {
                throw new Error(result.message || 'Failed to load statistics');
            }

        } catch (error) {
            console.error('Statistics loading error:', error);
            showError(error.message);
        } finally {
            hideLoading();
        }
    }

    /**
     * Render statistics data to UI
     */
    function renderStatistics(data) {
        // Update KPI boxes
        updateKPIs(data.summary);

        // Update detailed sections
        renderLikesByCollection(data.likes.by_collection);
        renderReservationsByCollection(data.reservations.by_collection);
        renderTopEgis(data.likes.top_egis);
        renderEppBreakdown(data.epp_potential.by_collection);
    }

    /**
     * Update KPI summary boxes
     */
    function updateKPIs(summary) {
        document.getElementById('kpi-total-likes').textContent = summary.total_likes.toLocaleString();
        document.getElementById('kpi-total-reservations').textContent = summary.total_reservations.toLocaleString();
        document.getElementById('kpi-strong-reservations').textContent = `${summary.strong_reservations} strong`;
        document.getElementById('kpi-total-amount').textContent = `€${summary.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('kpi-epp-quota').textContent = `€${summary.epp_quota.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    }

    /**
     * Render likes by collection
     */
    function renderLikesByCollection(collections) {
        const container = document.getElementById('likes-by-collection');

        if (collections.length === 0) {
            container.innerHTML = '<div class="py-4 text-center text-gray-300">{{ __("statistics.no_collections") }}</div>';
            return;
        }

        const html = collections.map(collection => `
            <div class="flex items-center justify-between p-3 bg-white rounded-lg bg-opacity-5">
                <div>
                    <h4 class="font-medium text-white truncate">${escapeHtml(collection.collection_name)}</h4>
                    <p class="text-sm text-gray-300">${collection.total_likes} likes totali</p>
                </div>
                <div class="text-right">
                    <div class="font-medium text-pink-400">${collection.collection_likes} collezione</div>
                    <div class="text-sm text-blue-400">${collection.egi_likes} EGI</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Render reservations by collection
     */
    function renderReservationsByCollection(collections) {
        const container = document.getElementById('reservations-by-collection');

        if (collections.length === 0) {
            container.innerHTML = '<div class="py-4 text-center text-gray-300">{{ __("statistics.no_reservations") }}</div>';
            return;
        }

        const html = collections.map(collection => `
            <div class="flex items-center justify-between p-3 bg-white rounded-lg bg-opacity-5">
                <div>
                    <h4 class="font-medium text-white truncate">${escapeHtml(collection.collection_name)}</h4>
                    <p class="text-sm text-gray-300">${collection.total_reservations} prenotazioni</p>
                </div>
                <div class="text-right">
                    <div class="font-medium text-green-400">${collection.strong_reservations} strong</div>
                    <div class="text-sm text-blue-400">${collection.weak_reservations} weak</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Render top EGIs
     */
    function renderTopEgis(egis) {
        const container = document.getElementById('top-egis');

        if (egis.length === 0) {
            container.innerHTML = '<div class="py-8 text-center text-gray-300 col-span-full">{{ __("statistics.no_top_egis") }}</div>';
            return;
        }

        const html = egis.map((egi, index) => `
            <div class="p-4 bg-white rounded-lg bg-opacity-5">
                <div class="flex items-center mb-2 space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 font-bold text-white rounded-full bg-gradient-to-r from-yellow-400 to-orange-500">
                        ${index + 1}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-white truncate">${escapeHtml(egi.title || 'Untitled EGI')}</h4>
                        <p class="text-xs text-gray-400 truncate">${escapeHtml(egi.collection_name)}</p>
                    </div>
                </div>
                <div class="text-lg font-bold text-pink-400">${egi.likes_count} likes</div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Render EPP breakdown
     */
    function renderEppBreakdown(collections) {
        const container = document.getElementById('epp-breakdown');

        if (collections.length === 0) {
            container.innerHTML = '<div class="py-4 text-center text-gray-300">{{ __("statistics.no_epp_data") }}</div>';
            return;
        }

        const html = collections.map(collection => `
            <div class="flex items-center justify-between p-4 bg-white rounded-lg bg-opacity-5">
                <div>
                    <h4 class="font-medium text-white">${escapeHtml(collection.collection_name)}</h4>
                    <p class="text-sm text-gray-300">${collection.epp_percentage}% EPP quota</p>
                </div>
                <div class="text-right">
                    <div class="font-bold text-purple-400">€${collection.epp_quota.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                    <div class="text-sm text-gray-400">from €${collection.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    // Utility functions
    function showLoading() {
        loadingOverlay?.classList.remove('hidden');
    }

    function hideLoading() {
        loadingOverlay?.classList.add('hidden');
    }

    function showError(message) {
        if (errorMessage) errorMessage.textContent = message;
        errorContainer?.classList.remove('hidden');
        statisticsContent?.classList.add('hidden');
    }

    function hideError() {
        errorContainer?.classList.add('hidden');
    }

    function showContent() {
        statisticsContent?.classList.remove('hidden');
    }

    function updateLastUpdated(timestamp) {
        if (lastUpdated) {
            const date = new Date(timestamp);
            lastUpdated.textContent = `{{ __('statistics.last_updated') }}: ${date.toLocaleString()}`;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

