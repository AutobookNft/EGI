<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Facades\UltraError;

/**
 * @Oracode Controller: NotificationsController
 * 🎯 Purpose: Handle Notifications related operations
 * 🧱 Core Logic: Manages views and actions for Notifications section
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-21
 */
class NotificationsController extends Controller
{
    /**
     * Logger instance
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Display the Notifications dashboard
     *
     * @return View
     */
public function index(): View
    {
        $this->logger->info('Accessing Notifications dashboard');

        return view('Notifications.index');
    }

    /**
     * Mark notification as read
     *
     * @param Request $request
     * @param string $notification Notification ID
     * @return RedirectResponse
     */
public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($notification);
            $notification->markAsRead();

            $this->logger->info('Notification marked as read', [
                'notification_id' => $notification->id
            ]);

            return redirect()->route('notifications.index')
                ->with('success', __('notifications.marked_as_read'));

        } catch (\Exception $e) {
            return UltraError::handle('NOTIFICATION_MARK_READ_FAILED', [
                'error' => $e->getMessage()
            ], $e)->with('error', __('notifications.action_failed'));
        }
    }
}
