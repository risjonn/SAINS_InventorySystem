<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetNotice;
use App\Notifications\PasswordRecoverNotice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        return view('users.index', compact('requests'));
    }

    /**
     * Handle the password reset action.
     */
    public function update(Request $r)
    {
        $r->validate([
            'request_id' => 'required|exists:password_reset_requests,id',
            'new_password' => 'required|min:6|confirmed',
        ]);
    
        $pr = PasswordResetRequest::findOrFail($r->request_id);
        $user = $pr->user;
        $user->password = encrypt($r->new_password);  // Encrypt the new password
        $user->save();
    
        // Mark the request as completed
        $pr->update(['completed' => 'Complete']);  // Ensure this is updated to 'Complete'
    
        // Send the notification about the reset
        $user->notify(new PasswordResetNotice($r->new_password));
    
        return redirect()->route('users.index')->with('status', 'Password has been successfully updated and user notified.');
    }

    /**
     * Handle the password recover action (for Super Admin).
    */
    public function updateRecover(Request $r)
    {
        $r->validate([
            'request_id' => 'required|exists:password_reset_requests,id',
        ]);

        // Find the recover request
        $pr = PasswordResetRequest::findOrFail($r->request_id);

        // Ensure it's of type 'recover'
        if ($pr->type !== 'recover') {
            return redirect()->route('users.index')->with('error', 'This is not a valid recover request.');
        }

        // Mark the request as completed
        $pr->update(['completed' => 'Complete']);  // Ensure this is updated to 'Complete'

        // Notify the user that their password was recovered
        $user = $pr->user;
        $user->notify(new PasswordRecoverNotice($user->password));  // You may adjust the logic for notification

        return redirect()->route('users.index')->with('status', 'Password recovery request processed and user notified.');
    }

    /**
     * Check the status of a password reset request for a user.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);
    
        $user = User::where('username', $request->username)->first();
    
        if (!$user) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'User not found'
            ]);
        }
    
        // Check if the current logged-in user is the same as the one being checked
        if (Auth::check() && Auth::id() === $user->id) {
            return response()->json([
                'status' => 'logged_in',
                'message' => 'You are currently logged in. Password reset history is not available.'
            ]);
        }
    
        // Fetch the latest request (either reset or recover)
        $latestRequest = PasswordResetRequest::where('user_id', $user->id)
                                            ->whereIn('type', ['reset', 'recover'])
                                            ->latest('created_at')
                                            ->first();
    
        // Check if the request has already been viewed
        if ($latestRequest && $latestRequest->status_viewed) {
            return response()->json([
                'status' => 'viewed',
                'message' => 'You have already viewed your reset/recover request status.'
            ]);
        }
    
        if (!$latestRequest) {
            return response()->json([
                'status' => 'none',
                'message' => 'No password reset or recovery request found'
            ]);
        }
    
        // Decrypt the password if the request is completed
        $password = null;
        if ($latestRequest->completed === 'Complete') {
            $password = decrypt($latestRequest->user->password);  // Decrypt the password
        }
    
        // Prepare the status message
        $statusMessage = $latestRequest->completed === 'Complete'
                         ? ($latestRequest->type === 'reset' ? 'Your password has been reset' : 'Your password has been recovered')
                         : ($latestRequest->type === 'reset' ? 'Your password reset request is pending' : 'Your password recovery request is pending');
    
        return response()->json([
            'status' => $statusMessage,
            'requestedAt' => $latestRequest->created_at->format('l, j F Y - g:i a'),
            'password' => $password,
            'message' => $statusMessage
        ]);
    }     
}