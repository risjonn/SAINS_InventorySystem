<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;

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
}
