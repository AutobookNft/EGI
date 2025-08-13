{{-- resources/views/components/egi-reservation-history.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Reservation History --}}
{{-- Displays reservation/certificate history for an EGI in NFT marketplace style. --}}
{{-- @accessibility-trait Uses semantic markup and clear visual indicators for status --}}
{{-- @schema-type ItemList - Represents a collection of reservation events --}}

@props(['egi', 'certificates' => collect()])

<div class="mt-10 overflow-hidden bg-white rounded-lg shadow-md"
     x-data="{ expanded: false }"
     id="egi-reservation-history">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 cursor-pointer bg-gradient-to-r from-indigo-600 to-purple-600"
         @click="expanded = !expanded"
         aria-expanded="false"
         :aria-expanded="expanded.toString()"
         aria-controls="reservation-history-content">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-lg font-semibold text-white">{{ __('reservation.history.title') }}</h3>
        </div>
        <div class="flex items-center text-white">
            <span class="mr-2 text-sm">{{ $certificates->count() }} {{ trans_choice('reservation.history.entries', $certificates->count()) }}</span>
            <svg x-show="!expanded" class="w-5 h-5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <svg x-show="expanded" class="w-5 h-5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </div>
    </div>

    {{-- Content (collapsible) --}}
    <div id="reservation-history-content"
         class="overflow-hidden transition-all duration-300 border-t border-gray-200"
         x-show="expanded"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-[1000px]"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-[1000px]"
         x-transition:leave-end="opacity-0 max-h-0">

        @if($certificates->isEmpty())
            {{-- Empty state --}}
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <h4 class="mb-1 text-lg font-medium text-gray-900">{{ __('reservation.history.no_entries') }}</h4>
                <p class="text-gray-500">{{ __('reservation.history.be_first') }}</p>
                <button class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm reserve-button hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        data-egi-id="{{ $egi->id }}">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                    </svg>
                    {{ __('reservation.button.reserve') }}
                </button>
            </div>
        @else
            {{-- Timeline view --}}
            <div class="p-6">
                <div class="flow-root">
                    <ul role="list" class="reservation-timeline">
                        @foreach($certificates as $certificate)
                            <li class="reservation-timeline-item">
                                <div class="reservation-timeline-connector">
                                    <div class="reservation-timeline-icon {{ $certificate->is_current_highest ? 'bg-green-500' : ($certificate->is_superseded ? 'bg-yellow-500' : 'bg-blue-500') }}">
                                        @if($certificate->is_current_highest)
                                            <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        @elseif($certificate->is_superseded)
                                            <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="reservation-timeline-content">
                                    {{-- Card Header --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-10 h-10 mr-3 bg-gray-100 rounded-md">
                                                @if($certificate->reservation_type === 'strong')
                                                    <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    @if($certificate->user)
                                                        {{ $certificate->user->name }}
                                                    @else
                                                        <span class="font-mono text-xs">{{ Str::limit($certificate->wallet_address, 15) }}</span>
                                                    @endif
                                                </h4>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('reservation.type.' . $certificate->reservation_type) }}
                                                    @if($certificate->is_current_highest)
                                                        <span class="inline-flex ml-1 px-1.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ __('reservation.badge.highest') }}</span>
                                                    @elseif($certificate->is_superseded)
                                                        <span class="inline-flex ml-1 px-1.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">{{ __('reservation.badge.superseded') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">
                                                €{{ number_format($certificate->offer_amount_fiat, 2) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ number_format($certificate->offer_amount_algo, 8) }} ALGO
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Card Footer --}}
                                    <div class="flex items-center justify-between mt-3 text-xs">
                                        <div class="flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <time datetime="{{ $certificate->created_at->toIso8601String() }}">
                                                {{ $certificate->created_at->diffForHumans() }}
                                            </time>
                                        </div>
                                        <a href="{{ route('egi-certificates.show', $certificate->certificate_uuid) }}"
                                           class="font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ __('reservation.history.view_certificate') }} →
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Timeline styling */
.reservation-timeline {
    margin-top: 1.25rem;
    position: relative;
}

.reservation-timeline-item {
    display: flex;
    position: relative;
    padding-bottom: 1.5rem;
}

.reservation-timeline-item:last-child {
    padding-bottom: 0;
}

.reservation-timeline-connector {
    position: relative;
    flex: 0 0 auto;
    width: 40px;
}

.reservation-timeline-icon {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 1rem;
}

.reservation-timeline-content {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    transition: all 0.3s;
}

.reservation-timeline-content:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

/* Timeline connector line */
.reservation-timeline-item:not(:last-child) .reservation-timeline-connector::before {
    content: '';
    position: absolute;
    top: 28px;
    left: 12px;
    bottom: 0;
    width: 1px;
    background-color: #e5e7eb;
}
</style>

@push('scripts')
<script>
    // This script is only needed if you're not using Alpine.js globally
    document.addEventListener('DOMContentLoaded', function() {
        // Makes sure to initialize the component even if Alpine.js is loaded after this code
        if (typeof Alpine === 'undefined') {
            // If using a module bundler like webpack, you might need a different approach
            console.warn('Alpine.js is required for the EGI Reservation History component.');
        }
    });
</script>
@endpush
