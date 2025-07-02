<?php

namespace App\Http\Controllers\Notifications;

use App\Models\CustomDatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Supports\NotificationViewResolver;
use Illuminate\Support\Facades\Log;

class NotificationDetailsController extends Controller
{
    public function show($id)
    {

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
            'id' => $id,
        ]);

        $notification = Auth::user()
            ->customNotifications()
            ->where('id', $id)
            ->with('model')
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Not found'], 404);
        }

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
            'notificationType' => $notification->type,
        ]);

        /**
         * Se la notifica ha una vista specificata, usala, altrimenti risolvi la vista in base al tipo di notifica
         * NotificationViewResolver::resolveView($notification->type); si basa sul FQCN della notifica,
         * standard OS 1.5 Self declaring code. Adottato inzialmente solo per le notifiche GDPR.
         */
        $viewKey = $notification->view; // ?? NotificationViewResolver::resolveView($notification->type);

        if (is_array($viewKey)) {
            $viewKey = array_map('strtolower', $viewKey); // Converte ogni elemento dell'array in minuscolo
        } elseif (is_string($viewKey)) {
            $viewKey = strtolower($viewKey); // Converte la stringa in minuscolo
        }

        $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];

        $view = $config['view'] ?? null;

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
            'config' => $config,
            'viewKey' => $viewKey,
            'view' => $view,
        ]);


        $render = $config['render'] ?? 'controller';
        $controller = $config['controller'] ?? null;

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
            'viewKey' => $viewKey,
            'view' => $view,
            'render' => $render,
            'controller' => $controller,
        ]);

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
            'notification' => $notification,
        ]);

        if ($view) {
            if ($render === 'livewire') {
                Log::channel('florenceegi')->info('Sto aprendo Livewire', [
                    'render' => $render,
                    'view' => $view,
                    'notification' => $notification,
                ]);
                return view('livewire.' . $view, ['notification' => $notification]);
            } elseif ($render === 'controller' && $controller) {
                $data = app()->make($controller)->prepare($notification);
                Log::channel('florenceegi')->info('Sto aprendo controller: ', [
                    'controller' => $controller,
                    'data' => $data,
                ]);
                return view($view, $data);
            } else {
                Log::channel('florenceegi')->info('Sto aprendo con altro', [
                    'render' => $render,
                    'view' => $view,
                    'notification' => $notification,
                ]);
                return view($view, ['notification' => $notification]);
            }

        } else {
            return response()->json(['error' => 'Tipo di notifica non supportata'], 400);
        }
    }
}
