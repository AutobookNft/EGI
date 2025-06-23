@props(['policyContent'])

<nav {{ $attributes->merge(['class' => 'p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50 no-print']) }}
     aria-labelledby="toc-heading">
    <div class="flex items-center justify-between mb-4">
        <h2 id="toc-heading" class="text-xl font-semibold text-gray-900">
            {{ __('gdpr.table_of_contents') }}
        </h2>
        @if(isset($policyContent['estimated_reading_time']))
        <span class="px-3 py-1 text-sm text-blue-600 rounded-full bg-blue-50">
            📖 {{ $policyContent['estimated_reading_time'] }} min lettura
        </span>
        @endif
    </div>

    <ol class="space-y-2" role="list">
        @forelse($policyContent['sections'] as $section)
        <li class="flex items-start">
            <span class="flex-shrink-0 mr-3 font-medium text-gray-500">
                {{ $section['index'] }}.
            </span>
            <a href="#{{ $section['anchor'] }}"
               class="flex-1 text-blue-600 transition-colors hover:text-blue-800 hover:underline {{ $section['level'] > 1 ? 'ml-' . (($section['level'] - 1) * 4) : '' }}"
               data-section="{{ $section['index'] }}">
                {{ $section['title'] }}
            </a>
        </li>
        @empty
        <li class="italic text-gray-500">
            {{ __('gdpr.no_sections_available') }}
        </li>
        @endforelse
    </ol>

    @if(isset($policyContent['total_sections']) && $policyContent['total_sections'] > 0)
    <div class="pt-4 mt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
            {{ __('gdpr.total_sections', ['count' => $policyContent['total_sections']]) }}
        </p>
    </div>
    @endif
</nav>
