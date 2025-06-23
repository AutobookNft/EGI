@props(['userAcceptance'])

<div {{ $attributes->merge(['class' => 'p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50 no-print']) }}>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('gdpr.your_acceptance_status') }}</h2>

    @if($userAcceptance)
    <div class="p-4 border border-green-200 rounded-lg bg-green-50" role="status" aria-live="polite">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div>
                <p class="font-medium text-green-800">{{ __('gdpr.policy_accepted') }}</p>
                <p class="text-sm text-green-700">
                    {{ __('gdpr.accepted_on') }}:
                    <time datetime="{{ $userAcceptance->created_at->toISOString() }}">
                        {{ $userAcceptance->created_at->format('d M Y, H:i') }}
                    </time>
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50" role="alert" aria-live="assertive">
         {{-- Form di accettazione... --}}
    </div>
    @endif
</div>
