@extends('layouts.gdpr')

@section('title', __('gdpr.privacy_policy'))

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8" role="main" aria-labelledby="privacy-policy-title">
    <div class="max-w-4xl mx-auto">
        {{-- Header Card with ARIA --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 id="privacy-policy-title" class="text-3xl font-bold text-gray-900">
                        {{ __('gdpr.privacy_policy') }}
                    </h1>
                    <p class="mt-2 text-gray-600" id="privacy-policy-desc">
                        {{ __('gdpr.privacy_policy_description') }}
                    </p>
                </div>
                <div class="hidden sm:block" aria-hidden="true">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Version Info Card with live region --}}
        <div class="p-6 mb-8 border border-blue-200 shadow-lg bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl"
             role="region"
             aria-label="{{ __('gdpr.policy_version_info') }}">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('gdpr.current_version') }}</h2>
                    <p class="mt-1 text-gray-700">
                        <strong>{{ __('gdpr.version') }}:</strong> {{ $currentPolicy->version }}
                    </p>
                    <p class="text-gray-700">
                        <strong>{{ __('gdpr.effective_date') }}:</strong>
                        <time datetime="{{ $currentPolicy->effective_date->toISOString() }}">
                            {{ $currentPolicy->effective_date->format('d M Y') }}
                        </time>
                    </p>
                    <p class="text-gray-700">
                        <strong>{{ __('gdpr.last_updated') }}:</strong>
                        <time datetime="{{ $currentPolicy->updated_at->toISOString() }}">
                            {{ $currentPolicy->updated_at->format('d M Y, H:i') }}
                        </time>
                    </p>
                </div>

                {{-- Policy Actions --}}
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('gdpr.privacy-policy.download') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 transition-colors bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                       aria-label="{{ __('gdpr.download_pdf_version') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('gdpr.download_pdf') }}
                    </a>

                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            aria-label="{{ __('gdpr.print_policy') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        {{ __('gdpr.print') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Table of Contents with navigation --}}
        <nav class="p-6 mb-8 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
             aria-labelledby="toc-heading">
            <h2 id="toc-heading" class="mb-4 text-xl font-semibold text-gray-900">
                {{ __('gdpr.table_of_contents') }}
            </h2>
            <ol class="space-y-2" role="list">
                @foreach($policyContent['sections'] as $index => $section)
                <li>
                    <a href="#section-{{ $index + 1 }}"
                       class="flex items-center text-blue-600 transition-colors hover:text-blue-800 hover:underline">
                        <span class="mr-2 text-gray-500">{{ $index + 1 }}.</span>
                        {{ $section['title'] }}
                    </a>
                </li>
                @endforeach
            </ol>
        </nav>

        {{-- Policy Content with semantic sections --}}
        <article class="p-8 prose prose-lg border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50 max-w-none"
                 aria-describedby="privacy-policy-desc">

            @foreach($policyContent['sections'] as $index => $section)
            <section id="section-{{ $index + 1 }}"
                     class="mb-8 scroll-mt-6"
                     aria-labelledby="section-{{ $index + 1 }}-heading">

                <h2 id="section-{{ $index + 1 }}-heading" class="mb-4 text-2xl font-bold text-gray-900">
                    {{ $index + 1 }}. {{ $section['title'] }}
                </h2>

                @if(isset($section['content']))
                    {!! nl2br(e($section['content'])) !!}
                @endif

                @if(isset($section['subsections']))
                    @foreach($section['subsections'] as $subIndex => $subsection)
                    <div class="mt-4 ml-6">
                        <h3 class="mb-2 text-lg font-semibold text-gray-800">
                            {{ $index + 1 }}.{{ $subIndex + 1 }} {{ $subsection['title'] }}
                        </h3>
                        <div class="text-gray-700">
                            {!! nl2br(e($subsection['content'])) !!}
                        </div>
                    </div>
                    @endforeach
                @endif

                @if(isset($section['list_items']))
                <ul class="mt-4 space-y-2 list-disc list-inside" role="list">
                    @foreach($section['list_items'] as $item)
                    <li class="text-gray-700">{{ $item }}</li>
                    @endforeach
                </ul>
                @endif
            </section>

            @if(!$loop->last)
            <hr class="my-8 border-gray-200" role="separator">
            @endif
            @endforeach

            {{-- Contact Information Section --}}
            <section class="p-6 mt-12 rounded-lg bg-gray-50"
                     aria-labelledby="contact-section-heading">
                <h2 id="contact-section-heading" class="mb-4 text-xl font-bold text-gray-900">
                    {{ __('gdpr.contact_information') }}
                </h2>

                <address class="not-italic">
                    <p class="mb-2">
                        <strong>{{ __('gdpr.data_controller') }}:</strong> {{ config('app.company_name') }}
                    </p>
                    <p class="mb-2">
                        <strong>{{ __('gdpr.email') }}:</strong>
                        <a href="mailto:{{ config('gdpr.privacy_email') }}"
                           class="text-blue-600 underline hover:text-blue-800">
                            {{ config('gdpr.privacy_email') }}
                        </a>
                    </p>
                    <p class="mb-2">
                        <strong>{{ __('gdpr.address') }}:</strong> {{ config('app.company_address') }}
                    </p>
                    @if(config('gdpr.dpo_email'))
                    <p>
                        <strong>{{ __('gdpr.dpo_email') }}:</strong>
                        <a href="mailto:{{ config('gdpr.dpo_email') }}"
                           class="text-blue-600 underline hover:text-blue-800">
                            {{ config('gdpr.dpo_email') }}
                        </a>
                    </p>
                    @endif
                </address>
            </section>
        </article>

        {{-- User Acceptance Status --}}
        <div class="p-6 mt-8 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('gdpr.your_acceptance_status') }}</h2>

            @if($userAcceptance)
            <div class="p-4 border border-green-200 rounded-lg bg-green-50"
                 role="status"
                 aria-live="polite">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-green-800">
                            {{ __('gdpr.policy_accepted') }}
                        </p>
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
            <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50"
                 role="alert"
                 aria-live="assertive">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <p class="font-medium text-yellow-800">
                            {{ __('gdpr.policy_not_accepted') }}
                        </p>
                        <p class="mt-1 text-sm text-yellow-700">
                            {{ __('gdpr.please_review_and_accept') }}
                        </p>

                        <form method="POST" action="{{ route('gdpr.privacy-policy.accept') }}" class="mt-4">
                            @csrf
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox"
                                       name="accept_policy"
                                       id="accept_policy"
                                       required
                                       aria-required="true"
                                       class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ __('gdpr.i_have_read_and_accept') }}
                                </span>
                            </label>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('gdpr.accept_policy') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Version History --}}
        @if($versionHistory->count() > 1)
        <section class="p-6 mt-8 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                 aria-labelledby="version-history-heading">
            <h2 id="version-history-heading" class="mb-6 text-xl font-semibold text-gray-900">
                {{ __('gdpr.version_history') }}
            </h2>

            <div class="overflow-x-auto" role="region" aria-label="{{ __('gdpr.policy_versions_table') }}">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.version') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.effective_date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.summary_of_changes') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.actions') }}
                            </th>
                        </tr>
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
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <time datetime="{{ $version->effective_date->toISOString() }}">
                                    {{ $version->effective_date->format('d M Y') }}
                                </time>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ Str::limit($version->change_summary, 100) }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <a href="{{ route('gdpr.privacy-policy.version', $version->version) }}"
                                   class="text-blue-600 underline hover:text-blue-800">
                                    {{ __('gdpr.view') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif
    </div>
</div>

{{-- Print styles --}}
@push('styles')
<style media="print">
    .no-print { display: none !important; }
    body { font-size: 12pt; }
    .prose { max-width: 100%; }
    a { color: inherit; text-decoration: none; }
    .bg-white\/80 { background: white; box-shadow: none; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for table of contents
    const tocLinks = document.querySelectorAll('nav a[href^="#"]');

    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').slice(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Update URL without scrolling
                history.pushState(null, null, '#' + targetId);

                // Set focus for accessibility
                targetElement.setAttribute('tabindex', '-1');
                targetElement.focus();
            }
        });
    });

    // Highlight current section in view
    const sections = document.querySelectorAll('article section[id]');
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const id = entry.target.getAttribute('id');
            const tocLink = document.querySelector(`nav a[href="#${id}"]`);

            if (entry.isIntersecting && tocLink) {
                // Remove all active classes
                document.querySelectorAll('nav a').forEach(link => {
                    link.classList.remove('font-bold', 'text-blue-800');
                });

                // Add active class to current
                tocLink.classList.add('font-bold', 'text-blue-800');
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        observer.observe(section);
    });

    // Form validation for policy acceptance
    const acceptForm = document.querySelector('form[action*="accept"]');
    if (acceptForm) {
        acceptForm.addEventListener('submit', function(e) {
            const checkbox = this.querySelector('#accept_policy');

            if (!checkbox.checked) {
                e.preventDefault();
                checkbox.setAttribute('aria-invalid', 'true');
                checkbox.focus();

                // Announce error
                const error = document.createElement('span');
                error.className = 'sr-only';
                error.setAttribute('role', 'alert');
                error.textContent = '{{ __("gdpr.must_accept_policy") }}';
                this.appendChild(error);

                setTimeout(() => error.remove(), 3000);
            }
        });
    }

    // Copy policy link functionality
    const copyButton = document.createElement('button');
    copyButton.className = 'fixed bottom-4 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 no-print';
    copyButton.setAttribute('aria-label', '{{ __("gdpr.copy_policy_link") }}');
    copyButton.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-2.392 0-4.744.175-6.284.516m9.032 10.568A8.961 8.961 0 0112 21c-2.392 0-4.744-.975-6.284-2.416"></path></svg>';

    copyButton.addEventListener('click', function() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-20 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.textContent = '{{ __("gdpr.link_copied") }}';
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 3000);
        });
    });

    document.body.appendChild(copyButton);
});
</script>
@endpush
@endsection
