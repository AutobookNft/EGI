{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tutte le Notifiche')

@section('content')
<div class="min-h-screen bg-gray-900">
    <div class="container px-4 py-8 mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Tutte le Notifiche</h1>
            <p class="mt-2 text-gray-400">Gestisci le tue notifiche e aggiornamenti</p>
        </div>

        {{-- Notifications List --}}
        @php
        $user = App\Helpers\FegiAuth::user();
        $notifications = $user ? $user->customNotifications()->orderBy('created_at', 'desc')->paginate(20) : collect();
        @endphp

        @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
            @php
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            $message = $data['message'] ?? 'Hai una nuova notifica';
            $isRead = $notification->read_at !== null;
            @endphp

            <div
                class="p-6 bg-gray-800 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors duration-200 {{ $isRead ? '' : 'bg-gray-800/80 border-indigo-500/50' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                {{ class_basename($notification->type ?? 'Notification') }}
                            </span>
                            @if(!$isRead)
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            @endif
                        </div>

                        <h3 class="text-lg font-medium text-white mb-2">
                            {{ $message }}
                        </h3>

                        <p class="text-sm text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('notifications.show', $notification->id) }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors duration-200">
                            Visualizza
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
        @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8v8a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2z">
                </path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-white">Nessuna notifica</h3>
            <p class="mt-2 text-gray-400">Non hai ancora ricevuto notifiche.</p>
        </div>
        @endif
    </div>
</div>
@endsection
