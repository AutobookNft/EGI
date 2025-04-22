<div class="mb-4 rounded-lg bg-gray-600 p-4" data-notification-id="{{ $notification->id }}" itemscope
    itemtype="https://schema.org/InformAction" aria-label="Notifica: Creazione wallet">

    data payload id: {{ $notification->model->id }}

    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

        @include('notifications.wallets.partials.head', ['notification' => $notification])


        @include('notifications.wallets.partials.buttons', ['notification' => $notification])

    </div>

</div>
