<div id="header-buttons" class="flex overflow-x-auto p-2 space-x-2 no-scrollbar">
    @foreach($pendingNotifications as $notif)
        @include('livewire.partials.head-thumbnail', ['notif' => $notif])
    @endforeach
</div>
