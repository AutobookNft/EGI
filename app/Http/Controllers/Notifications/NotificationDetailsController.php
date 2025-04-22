<?php

namespace App\Http\Controllers\Notifications;

use App\Models\CustomDatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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

        $viewKey = $notification->view ?? null;

        if (is_array($viewKey)) {
            $viewKey = array_map('strtolower', $viewKey); // Converte ogni elemento dell'array in minuscolo
        } elseif (is_string($viewKey)) {
            $viewKey = strtolower($viewKey); // Converte la stringa in minuscolo
        }

        $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];

        $view = $config['view'] ?? null;

        Log::channel('florenceegi')->info('NotificationDetailsController:show', [
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
                Log::channel('florenceegi')->info('Cosa sto aprendo', [
                    'render' => $render,
                    'view' => $view,
                    'notification' => $notification,
                ]);
                return view('livewire.' . $view, ['notification' => $notification]);
            } elseif ($render === 'controller' && $controller) {
                $data = app()->make($controller)->prepare($notification);
                Log::channel('florenceegi')->info('Cosa sto aprendo: ',[
                    'controller' => $controller,
                    'data' => $data,
                ]);
                return view($view, $data);
            } else {
                Log::channel('florenceegi')->info('Cosa sto aprendo', [
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

