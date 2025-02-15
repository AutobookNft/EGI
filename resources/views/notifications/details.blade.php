{{-- @php
    // La variabile $notification viene passata dalla route
    $viewKey = $notification->view ?? null;
    $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];
    $view = $config['view'] ?? null;
    $render = $config['render'] ?? 'controller';
    $controller = $config['controller'] ?? null;

    // Log::channel('florenceegi')->info('view debug (AJAX)', [
    //     'viewKey' => $viewKey,
    //     'view'    => $view,
    //     'config'  => $config,
    //     'notification' => $notification,
    // ]);
@endphp

@if($view)
    @if($render === 'livewire')
        @livewire($view, ['notification' => $notification])
    @elseif($render === 'controller' && $controller)
        @php
            Log::channel('florenceegi')->info('controller debug (AJAX)', [
                'controller' => $controller,
                'notification' => $notification,
            ]);
        @endphp
        @include($view, ['notification' => App::make($controller)->show($notification)])
    @else
        @include($view, ['notification' => $notification])
    @endif
@else
    <div class="text-red-500">
        {{ __('Tipo di notifica non supportata') }}: {{ $view }}
    </div>
@endif --}}
