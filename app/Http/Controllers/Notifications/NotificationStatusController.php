<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Notifications
 * @tags Status
 *
 * @authenticated
 *
 * APIs for retrieving the status of user notifications.
 */
class NotificationStatusController extends Controller
{
    /**
     * Get Unread Notifications Count
     *
     * Retrieves the count of unread notifications for the authenticated user.
     * This endpoint is designed to be lightweight for frequent polling by the UI,
     * for example, to update a notification badge.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {"unread_count": 5}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function getUnreadCount(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $count = $user->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }
}
