<?php

if (!function_exists('add_notification')) {
    /**
     * Add a notification for a user.
     *
     * @param int $userId Target user ID
     * @param string $type NEW_TICKET, STATUS_CHANGE, ASSIGNED, NEW_MESSAGE, RESOLVED
     * @param string $title
     * @param string $message
     * @param string|null $refId Ticket ID or other reference
     * @return bool
     */
    function add_notification(int $userId, string $type, string $title, string $message, string $refId = null): bool
    {
        $notificationModel = new \App\Models\NotificationModel();
        return (bool)$notificationModel->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'ref_id' => $refId,
            'is_read' => 0
        ]);
    }
}
