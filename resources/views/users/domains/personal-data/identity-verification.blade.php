<x-app-layout>
    <x-slot name="header">
        <h2>Identity Verification Required</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto">
            <div class="p-6 bg-white rounded-lg shadow">
                <h3>{{ $verification_reason }}</h3>
                <p>This feature will be implemented in the next phase.</p>
                <a href="{{ $return_url }}" class="btn btn-primary">Continue</a>
            </div>
        </div>
    </div>
</x-app-layout>
