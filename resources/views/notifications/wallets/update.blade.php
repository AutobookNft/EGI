<div class="notification-item mb-4 rounded-lg bg-gray-600 p-4" itemscope itemtype="https://schema.org/InformAction"
    aria-label="Notifica: Modifica wallet" data-notification-id="{{ $notification->id }}">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

        @include('notifications.wallets.partials.head', ['notification' => $notification])

        @include('notifications.wallets.partials.buttons', ['notification' => $notification])
    </div>

</div>
