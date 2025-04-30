<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\PasswordResetNotice;
use Illuminate\Support\Facades\DB;

class TestNotification extends Command
{
    protected $signature = 'test:notification {username}';
    protected $description = 'Test password reset notification for a user';

    public function handle()
    {
        $username = $this->argument('username');
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            $this->error("User not found: {$username}");
            return;
        }
        
        // Clear existing notifications
        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->where('type', 'App\Notifications\PasswordResetNotice')
            ->delete();
            
        $this->info("Cleared existing notifications for {$username}");
        
        // Create a new notification
        $testPassword = 'TestPass123';
        $user->notify(new PasswordResetNotice($testPassword));
        
        $this->info("Sent new notification with password: {$testPassword}");
        
        // Verify it was created
        $notification = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->where('type', 'App\Notifications\PasswordResetNotice')
            ->latest()
            ->first();
            
        if ($notification) {
            $data = json_decode($notification->data);
            $this->info("Notification created successfully:");
            $this->table(['Field', 'Value'], [
                ['ID', $notification->id],
                ['Created', $notification->created_at],
                ['Read', $notification->read_at ?? 'Not read'],
                ['Message', $data->message ?? 'No message'],
                ['Reset At', $data->resetAt ?? 'No date'],
                ['Password', $data->newPassword ?? 'No password']
            ]);
        } else {
            $this->error("Failed to create notification!");
        }
    }
}