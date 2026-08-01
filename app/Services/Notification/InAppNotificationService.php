<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Database\Eloquent\Collection;

class InAppNotificationService
{
    /**
     * Store an in-app notification for a user via native database notifications.
     */
    public function notify(User $user, string $message, string $type = 'info'): void
    {
        $user->notify(new InAppNotification($message, $type));
    }

    /**
     * Get all notifications for a user.
     */
    public function getNotifications(User $user, bool $unreadOnly = false): Collection
    {
        $query = $user->notifications();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->latest()->limit(100)->get();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Send a notification to multiple users.
     */
    public function notifyMany(array $users, string $message, string $type = 'info'): void
    {
        foreach ($users as $user) {
            if ($user instanceof User) {
                $this->notify($user, $message, $type);
            }
        }
    }
}
