<div class="notification-item mb-4 rounded-lg bg-gray-600 p-4" data-notification-id="{{ $notification->id }}" itemscope
    itemtype="https://schema.org/InformAction" aria-label="Notifica: Creazione wallet">

    @include('notifications.wallets.partials.head', ['notification' => $notification])

    @include('notifications.wallets.partials.buttons', ['notification' => $notification])

</div>
