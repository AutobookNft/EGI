@props(['versionHistory', 'currentPolicy'])

<section {{ $attributes->merge(['class' => 'p-6 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50 no-print']) }}
         aria-labelledby="version-history-heading">
    <h2 id="version-history-heading" class="mb-6 text-xl font-semibold text-gray-900">
        {{ __('gdpr.version_history') }}
    </h2>

    <div class="overflow-x-auto" role="region" aria-label="{{ __('gdpr.policy_versions_table') }}">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                {{-- Table headers... --}}
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($versionHistory as $version)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                        {{ $version->version }}
                        @if($version->id === $currentPolicy->id)
                        <span class="inline-flex px-2 py-1 ml-2 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                            {{ __('gdpr.current') }}
                        </span>
                        @endif
                    </td>
                    {{-- Altre colonne... --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
