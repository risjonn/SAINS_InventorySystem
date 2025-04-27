<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetNotice;

class PasswordRequestController extends Controller
{
    /**
     * Show all pending password reset requests.
     */
    public function index()
    {
        $requests = PasswordResetRequest::with('user')
                        ->where('completed', 'Incomplete')
                        ->latest()
                        ->get();

        return view('admin.password_requests.index', compact('requests'));
    }

    /**
     * Handle the password reset action.
     */
    public function update(Request $r)
    {
        $r->validate([
          'request_id'                 => 'required|exists:password_reset_requests,id',
          'new_password'               => 'required|min:6|confirmed',
        ]);
    
        $pr = PasswordResetRequest::findOrFail($r->request_id);
        $user = $pr->user;
        $user->password = bcrypt($r->new_password);
        $user->save();
    
        $pr->update(['completed'=>'Complete']);
        $user->notify(new PasswordResetNotice($r->new_password));
    
        return redirect()->route('users.index')->with([
            'success' => 'Password reset successfully and user notified.',
            'resources' => 'user'
        ]);
    }

    /**
     * Check the status of a password reset request for a user.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'username' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'User not found'
            ]);
        }

        // Check for completed password reset
        $completedReset = $user->notifications()
            ->where('type', \App\Notifications\PasswordResetNotice::class)
            ->latest()
            ->first();

        if ($completedReset) {
            return response()->json([
                'status' => 'reset',
                'resetAt' => $completedReset->data['resetAt'],
                'newPassword' => $completedReset->data['newPassword']
            ]);
        }

        // Check for pending request
        $pendingRequest = $user->password_reset_requests()
            ->where('completed', 'Incomplete')
            ->latest()
            ->first();

        if ($pendingRequest) {
            return response()->json([
                'status' => 'pending',
                'requestedAt' => $pendingRequest->created_at
            ]);
        }

        return response()->json([
            'status' => 'none',
            'message' => 'No password reset request found'
        ]);
    }
}