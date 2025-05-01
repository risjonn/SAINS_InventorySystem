<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str; 
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PasswordRecoverNotice;

class PasswordResetLinkController extends Controller
{

    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],  // Use username instead of email
        ]);
    
        $user = User::where('username', $request->username)->first();  // Look for the user by username
    
        // Always respond with a generic message
        $statusMessage = 'If that username exists, your request has been sent to Super Admin.';
    
        if (! $user) {
            return back()->with('status', $statusMessage);
        }
    
        // Only create a new request if none is pending
        PasswordResetRequest::firstOrCreate(
            ['user_id' => $user->id, 'completed' => 'Incomplete'],
            ['user_id' => $user->id]
        );
    
        return back()->with('status', $statusMessage);
    }

    public function recover(Request $request)
    {
        // 1) Validate the username
        $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
        ]);
    
        // 2) Find the user
        $user = User::where('username', $request->username)
                    ->firstOrFail();
    
        // 3) Record a “recover” request
        PasswordResetRequest::create([
            'user_id'   => $user->id,
            'completed' => 'Incomplete',
            'type'      => 'recover',
        ]);
    
        // 4) Send the notice
        $user->notify(new PasswordRecoverNotice($user->password));
    
        // 5) Redirect back with a generic status message
        return back()->with('status',
            'If that username exists, your request has been sent to Super Admin.'
        );
    }
}
