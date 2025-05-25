@extends('layouts.gdpr')

@section('title', __('gdpr.limit_processing'))

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header Card --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('gdpr.limit_processing') }}</h1>
                    <p class="mt-2 text-gray-600">{{ __('gdpr.limit_processing_description') }}</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Current Status Card --}}
        <div class="p-6 mb-8 border border-yellow-200 shadow-lg bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('gdpr.current_processing_status') }}</h3>
                    <p class="mt-1 text-gray-700">
                        @if($activeRestrictions->isEmpty())
                            {{ __('gdpr.no_processing_restrictions') }}
                        @else
                            {{ __('gdpr.active_restrictions_count', ['count' => $activeRestrictions->count()]) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Active Restrictions --}}
        @if($activeRestrictions->isNotEmpty())
        <div class="p-6 mb-8 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                {{ __('gdpr.active_restrictions') }}
            </h2>

            <div class="space-y-4">
                @foreach($activeRestrictions as $restriction)
                <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">
                                {{ __('gdpr.restriction_types.' . $restriction->restriction_type) }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-700">{{ $restriction->reason }}</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('gdpr.active_since') }}: {{ $restriction->created_at->format('d M Y') }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('gdpr.limit-processing.lift', $restriction) }}" class="ml-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('{{ __('gdpr.confirm_lift_restriction') }}')"
                                    class="text-sm font-medium text-red-600 transition-colors duration-200 hover:text-red-800">
                                {{ __('gdpr.lift_restriction') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Request New Restriction Form --}}
        <div class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4" />
                </svg>
                {{ __('gdpr.request_new_restriction') }}
            </h2>

            <form method="POST" action="{{ route('gdpr.limit-processing.store') }}" class="space-y-6">
                @csrf

                {{-- Restriction Type --}}
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-700">
                        {{ __('gdpr.select_data_categories') }}
                    </label>
                    <div class="space-y-3">
                        @foreach($availableRestrictions as $key => $restriction)
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox"
                                   name="restrictions[]"
                                   value="{{ $key }}"
                                   class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   @if(in_array($key, old('restrictions', []))) checked @endif>
                            <div class="ml-3">
                                <span class="block font-medium text-gray-900 transition-colors group-hover:text-blue-600">
                                    {{ $restriction['name'] }}
                                </span>
                                <span class="block text-sm text-gray-500">
                                    {{ $restriction['description'] }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('restrictions')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reason for Restriction --}}
                <div>
                    <label for="reason" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.reason_for_restriction') }}
                    </label>
                    <select name="reason"
                            id="reason"
                            required
                            class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('gdpr.select_reason') }}</option>
                        <option value="accuracy_dispute" {{ old('reason') == 'accuracy_dispute' ? 'selected' : '' }}>
                            {{ __('gdpr.reasons.accuracy_dispute') }}
                        </option>
                        <option value="unlawful_processing" {{ old('reason') == 'unlawful_processing' ? 'selected' : '' }}>
                            {{ __('gdpr.reasons.unlawful_processing') }}
                        </option>
                        <option value="legal_claims" {{ old('reason') == 'legal_claims' ? 'selected' : '' }}>
                            {{ __('gdpr.reasons.legal_claims') }}
                        </option>
                        <option value="public_interest" {{ old('reason') == 'public_interest' ? 'selected' : '' }}>
                            {{ __('gdpr.reasons.public_interest') }}
                        </option>
                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>
                            {{ __('gdpr.reasons.other') }}
                        </option>
                    </select>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Additional Details --}}
                <div>
                    <label for="details" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.additional_details') }}
                    </label>
                    <textarea name="details"
                              id="details"
                              rows="4"
                              required
                              class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="{{ __('gdpr.provide_detailed_explanation') }}">{{ old('details') }}</textarea>
                    @error('details')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">{{ __('gdpr.details_help_text') }}</p>
                </div>

                {{-- Legal Notice --}}
                <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">{{ __('gdpr.legal_notice') }}</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>{{ __('gdpr.restriction_legal_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('gdpr.consent') }}"
                       class="inline-flex items-center px-6 py-3 text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        {{ __('gdpr.cancel') }}
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 text-white transition-all duration-200 bg-yellow-600 border border-transparent rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('gdpr.submit_restriction_request') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Processing History --}}
        @if($processingHistory->isNotEmpty())
        <div class="p-6 mt-8 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <h2 class="mb-6 text-xl font-semibold text-gray-900">{{ __('gdpr.restriction_history') }}</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.type') }}
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.status') }}
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.date_requested') }}
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.date_lifted') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($processingHistory as $history)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ __('gdpr.restriction_types.' . $history->restriction_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $history->is_active ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $history->is_active ? __('gdpr.active') : __('gdpr.lifted') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $history->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $history->lifted_at ? $history->lifted_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic reason field behavior
    const reasonSelect = document.getElementById('reason');
    const detailsTextarea = document.getElementById('details');

    reasonSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            detailsTextarea.setAttribute('placeholder', '{{ __("gdpr.please_specify_reason") }}');
            detailsTextarea.focus();
        } else {
            detailsTextarea.setAttribute('placeholder', '{{ __("gdpr.provide_detailed_explanation") }}');
        }
    });

    // Checkbox group validation
    const checkboxes = document.querySelectorAll('input[name="restrictions[]"]');
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('input[name="restrictions[]"]:checked');

        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('{{ __("gdpr.select_at_least_one_category") }}');
            checkboxes[0].focus();
            return false;
        }
    });

    // Visual feedback for checkbox selection
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('bg-blue-50', 'border-l-4', 'border-blue-500', 'pl-3');
            } else {
                label.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500', 'pl-3');
            }
        });
    });
});
</script>
@endpush
@endsection
