@php
    if ($notification->read_at) {
        $statusClass = 'text-green-500';
        $statusText = __('notification.status.read');
    } else {
        $statusClass = 'text-yellow-400';
        $statusText = __('notification.status.pending_ack');
    }
@endphp

<div class="p-4 mb-4 bg-gray-600 rounded-lg notification-item"
     data-notification-id="{{ $notification->id }}"
     data-payload-type="gdpr"
     aria-labelledby="notification-title-{{ $notification->id }}"
     role="article"
     itemscope itemtype="https://schema.org/InformAction">

    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

        {{-- SEZIONE HEAD --}}
        <div class="w-full p-4 bg-gray-800 rounded-lg shadow-md" aria-label="{{ __('notification.aria.details_label') }}">
            <div class="flex flex-col items-start pb-2 mb-4 border-b border-gray-600">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 id="notification-title-{{ $notification->id }}" class="-mt-1 text-lg font-bold text-white" itemprop="name">{{ $notification->model->title }}</h4>
                </div>
                <div class="flex items-center text-sm text-gray-400">
                    <span>{{ __('notification.label.status') }}:</span>
                    <span class="ml-2 font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-gray-300" itemprop="description" aria-describedby="notification-title-{{ $notification->id }}">
                    {!! nl2br(e($notification->model->content)) !!}
                </p>

                <div class="flex items-center justify-between pt-3 text-xs text-gray-400 border-t border-gray-600">
                    <p>
                        {{ __('notification.label.created_at') . ': ' }}
                        <time datetime="{{ $notification->created_at->toIso8601String() }}" itemprop="startTime">
                            {{ $notification->created_at->diffForHumans() }}
                        </time>
                    </p>
                </div>
            </div>
        </div>

        {{-- SEZIONE BOTTONI --}}
        <div class="flex flex-col space-y-2 notification-actions" data-notification-id="{{ $notification->id }}" aria-label="{{ __('notification.aria.actions_label') }}">

            @if(!$notification->read_at)
                <button
                    type="button"
                    class="flex items-center justify-center px-4 py-2 text-white transition-colors duration-200 bg-indigo-600 rounded-lg response-btn hover:bg-indigo-500"
                    data-action="done"
                    data-notification-id="{{ $notification->id }}"
                    aria-label="{{ __('notification.aria.mark_as_read') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('notification.actions.done') }}
                </button>
            @endif

            @if($notification->model->action_url)
                <a href="{{ $notification->model->action_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center px-4 py-2 text-white transition-colors duration-200 bg-gray-700 rounded-lg hover:bg-gray-600" itemprop="url" aria-label="{{ __('notification.aria.learn_more') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    {{ __('notification.actions.learn_more') }}
                </a>
            @endif
        </div>

    </div>
</div>
