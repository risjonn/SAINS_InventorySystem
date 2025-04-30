<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();
        
        // Store a session flag that this user has successfully logged in
        $user = Auth::user();
        if ($user) {
            // Record successful login in session
            session(['password_reset_consumed' => true]);
            
            // Debug info
            Log::info("Processing login for user: {$user->username}");
            
            // Also try to mark notifications as read
            try {
                // Find any password reset notifications
                $notifications = DB::table('notifications')
                    ->where('notifiable_id', $user->id)
                    ->where('notifiable_type', get_class($user))
                    ->where('type', 'App\Notifications\PasswordResetNotice')
                    ->whereNull('read_at')
                    ->get();
                    
                Log::info("Found " . $notifications->count() . " unread password reset notifications for user: {$user->username}");
                
                // Mark notifications as read
                $updateCount = DB::table('notifications')
                    ->where('notifiable_id', $user->id)
                    ->where('notifiable_type', get_class($user))
                    ->where('type', 'App\Notifications\PasswordResetNotice')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                Log::info("Updated {$updateCount} password reset notifications for user: {$user->username}");
                
                // Also clear any completed password reset requests
                $deletedCount = DB::table('password_reset_requests')
                    ->where('user_id', $user->id)
                    ->where('completed', 'Complete')
                    ->delete();
                    
                Log::info("Deleted {$deletedCount} completed password reset requests for user: {$user->username}");
                
            } catch (\Exception $e) {
                Log::error("Failed to process notifications: " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}