<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('destroy');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $user = \App\Models\User::where('username', $request->username)->first();
    
        if ($user && $user->status === 'Inactive') {
            return back()->withErrors([
                'username' => 'Your account is deactivated. Please contact your Super Admin.',
            ]);
        }
    
        // Check if the user exists and handle password verification
        if ($user) {
            // Check if password is AES encrypted
            if ($this->isPasswordEncryptedWithAES($user->password)) {
                // If AES-encrypted password, decrypt and compare
                if (decrypt($user->password) === $request->password) {
                    // Password matches, log in the user
                    Auth::login($user);
                    // Mark password reset notifications as read
                    $this->markPasswordResetNotificationsAsRead($user);
    
                    // Log the user and attempt to mark status as viewed
                    Log::info("Attempting to mark status as viewed for user: {$user->username}");
    
                    // Mark the reset/recover request as viewed
                    $this->markStatusAsViewed($user);  // New method to mark as viewed
    
                    return redirect()->intended($this->redirectTo); // Redirect to intended route
                }
            } else {
                // Otherwise, use bcrypt to validate password
                if (Hash::check($request->password, $user->password)) {
                    // Password matches, log in the user
                    Auth::login($user);
                    // Mark password reset notifications as read
                    $this->markPasswordResetNotificationsAsRead($user);
    
                    // Log the user and attempt to mark status as viewed
                    Log::info("Attempting to mark status as viewed for user: {$user->username}");
    
                    // Mark the reset/recover request as viewed
                    $this->markStatusAsViewed($user);  // New method to mark as viewed
    
                    return redirect()->intended($this->redirectTo); // Redirect to intended route
                }
            }
        }
    
        // If authentication fails
        return back()->withErrors([
            'username' => 'Invalid login credentials.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username'; // Use "username" for login instead of "email"
    }
    
    /**
     * Mark any unread password reset notifications as read
     *
     * @param \App\Models\User $user
     * @return void
     */
    protected function markPasswordResetNotificationsAsRead($user)
    {
        try {
            // Find all unread password reset notifications with direct DB query
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

    protected function markStatusAsViewed($user)
    {
        // Find the most recent reset/recover request and mark it as viewed
        $latestRequest = \App\Models\PasswordResetRequest::where('user_id', $user->id)
                                                         ->whereIn('type', ['reset', 'recover'])
                                                         ->latest('created_at')
                                                         ->first();
    
        if ($latestRequest) {
            // Log the current status before updating
            Log::info("Marking request as viewed for user: {$user->username}, Request ID: {$latestRequest->id}, Current status_viewed: {$latestRequest->status_viewed}");
    
            // Update the status_viewed field
            $latestRequest->update(['status_viewed' => true]);
    
            // Log the update
            Log::info("Successfully marked status as viewed for request ID: {$latestRequest->id}");
        } else {
            Log::warning("No reset/recover request found for user: {$user->username}");
        }
    }

    /**
     * Check if the password is encrypted using AES encryption.
     *
     * @param string $password
     * @return bool
     */
    private function isPasswordEncryptedWithAES($password)
    {
        // Check if password starts with the AES encryption identifier
        return (strpos($password, 'eyJpdiI6') === 0);  // Typical starting string for AES-encrypted data
    }
}
