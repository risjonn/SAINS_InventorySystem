<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        $user = \App\Models\User::where('username',$request->username)->first();

        if ($user && $user->status === 'Inactive') {
            return back()->withErrors([
                'username' => 'Your account is deactivated. Please contact your Super Admin.',
            ]);
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            // Mark password reset notifications as read
            $this->markPasswordResetNotificationsAsRead(Auth::user());
            
            return redirect()->intended($this->redirectTo);
        }

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
}