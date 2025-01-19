<div class="bg-gray-600 p-4 mb-4 rounded-lg shadow-md">
    <p class="text-lg font-semibold text-white">{{ $notification->data['message'] }}</p>
    <p class="text-sm text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
</div>
