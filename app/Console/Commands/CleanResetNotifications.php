<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanResetNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean-resets 
                            {username? : The username to clean notifications for}
                            {--all : Clean for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all password reset notifications as read';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');
        $all = $this->option('all');
        
        if (!$username && !$all) {
            $this->error('Please specify a username or use --all option');
            return 1;
        }
        
        if ($all) {
            $users = User::all();
            $totalCleaned = 0;
            
            $this->info('Cleaning notifications for all users...');
            
            foreach ($users as $user) {
                $count = $this->cleanForUser($user);
                if ($count > 0) {
                    $this->line("Marked {$count} notifications as read for user {$user->username}");
                    $totalCleaned += $count;
                }
            }
            
            $this->info("Total notifications marked as read: {$totalCleaned}");
        } else {
            $user = User::where('username', $username)->first();
            
            if (!$user) {
                $this->error("User with username '{$username}' not found");
                return 1;
            }
            
            $count = $this->cleanForUser($user);
            $this->info("Marked {$count} notifications as read for user {$user->username}");
        }
        
        return 0;
    }
    
    /**
     * Clean notifications for a specific user
     * 
     * @param User $user
     * @return int
     */
    protected function cleanForUser($user)
    {
        $notifications = $user->notifications()
            ->where('type', 'App\Notifications\PasswordResetNotice')
            ->whereNull('read_at')
            ->get();
            
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        
        return $notifications->count();
    }
}