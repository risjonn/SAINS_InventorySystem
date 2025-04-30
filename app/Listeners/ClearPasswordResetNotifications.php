<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClearPasswordResetNotifications
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the login event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        try {
            $user = $event->user;
            
            // Find all unread password reset notifications for this user with direct DB query
            $updatedCount = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->where('type', 'App\Notifications\PasswordResetNotice')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            if ($updatedCount > 0) {
                Log::info("Marked {$updatedCount} password reset notifications as read for user {$user->username}");
            }
        } catch (\Exception $e) {
            Log::error("Error clearing password reset notifications: " . $e->getMessage());
        }
    }
}