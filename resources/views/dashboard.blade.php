<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Notification Center') }}
        </h2>
    </x-slot>

    {{-- Dashboard trasformata in Notification Center - rimossi contenitori con bordi eccessivi --}}
    {{-- Il componente livewire:dashboard gestisce già perfettamente le notifiche --}}
    <livewire:dashboard />
</x-app-layout>
