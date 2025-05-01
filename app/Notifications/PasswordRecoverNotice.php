<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordRecoverNotice extends Notification
{
    use Queueable;

    protected string $currentPassword;

    /**
     * Instantiate with the current (plaintext) password.
     */
    public function __construct(string $currentPassword)
    {
        $this->currentPassword = $currentPassword;
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
            'message'         => 'Your password was recovered by Super Admin.',
            'currentPassword' => $this->currentPassword,
            'recoveredAt'     => now()->toDateTimeString(),
        ];
    }
}
