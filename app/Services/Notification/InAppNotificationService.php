<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InAppNotificationService
{
    /**
     * Store an in-app notification for a user.
     * Uses a simple JSON-based storage approach with a notifications meta table.
     * For a production system, consider a dedicated notifications table.
     */
    public function notify(User $user, string $message, string $type = 'info'): void
    {
        $notification = [
            'id' => uniqid('notif_', true),
            'type' => $type,
            'message' => $message,
            'read' => false,
            'created_at' => now()->toDateTimeString(),
        ];

        $existing = DB::table('user_meta')
            ->where('user_id', $user->id)
            ->where('key', 'notifications')
            ->first();

        if ($existing) {
            $notifications = json_decode($existing->value, true) ?? [];
            array_unshift($notifications, $notification);
            // Keep only last 100 notifications
            $notifications = array_slice($notifications, 0, 100);

            DB::table('user_meta')
                ->where('id', $existing->id)
                ->update(['value' => json_encode($notifications)]);
        } else {
            DB::table('user_meta')->insert([
                'user_id' => $user->id,
                'key' => 'notifications',
                'value' => json_encode([$notification]),
            ]);
        }
    }

    /**
     * Get all notifications for a user.
     */
    public function getNotifications(User $user, bool $unreadOnly = false): array
    {
        $existing = DB::table('user_meta')
            ->where('user_id', $user->id)
            ->where('key', 'notifications')
            ->first();

        if (!$existing) {
            return [];
        }

        $notifications = json_decode($existing->value, true) ?? [];

        if ($unreadOnly) {
            return array_filter($notifications, fn($n) => !($n['read'] ?? false));
        }

        return $notifications;
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $existing = DB::table('user_meta')
            ->where('user_id', $user->id)
            ->where('key', 'notifications')
            ->first();

        if (!$existing) {
            return;
        }

        $notifications = json_decode($existing->value, true) ?? [];

        foreach ($notifications as &$notification) {
            if ($notification['id'] === $notificationId) {
                $notification['read'] = true;
                break;
            }
        }

        DB::table('user_meta')
            ->where('id', $existing->id)
            ->update(['value' => json_encode($notifications)]);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $existing = DB::table('user_meta')
            ->where('user_id', $user->id)
            ->where('key', 'notifications')
            ->first();

        if (!$existing) {
            return;
        }

        $notifications = json_decode($existing->value, true) ?? [];

        foreach ($notifications as &$notification) {
            $notification['read'] = true;
        }

        DB::table('user_meta')
            ->where('id', $existing->id)
            ->update(['value' => json_encode($notifications)]);
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return count($this->getNotifications($user, true));
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