<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Services\Notification\InAppNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected InAppNotificationService $notificationService;

    public function __construct(InAppNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = $this->authenticatedUser();
        $notifications = $this->notificationService->getNotifications($user, $request->boolean('unread'));

        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => $this->notificationService->getUnreadCount($this->authenticatedUser()),
        ]);
    }

    public function markAsRead(Request $request, string $notification)
    {
        $this->notificationService->markAsRead($this->authenticatedUser(), $notification);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead($this->authenticatedUser());

        return response()->json(['success' => true]);
    }
}
