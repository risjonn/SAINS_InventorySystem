<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetNotice extends Notification
{
    use Queueable;

    protected string $newPassword;

    /**
     * Instantiate with the new password.
     */
    public function __construct(string $newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Only use the "database" channel.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Payload stored in the notifications table.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message'     => 'Your password was reset by Super Admin.',
            'newPassword' => $this->newPassword,
            'resetAt'     => now()->toDateTimeString(),
        ];
    }
}
