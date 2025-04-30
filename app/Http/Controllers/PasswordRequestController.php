<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetNotice;
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
        
        // Check if the current logged-in user is the same as the one being checked
        if (Auth::check() && Auth::id() === $user->id) {
            return response()->json([
                'status' => 'logged_in',
                'message' => 'You are currently logged in. Password reset history is not available.'
            ]);
        }
    
        // First check for pending request
        $pendingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('completed', 'Incomplete')
            ->latest()
            ->first();
    
        if ($pendingRequest) {
            return response()->json([
                'status' => 'pending',
                'requestedAt' => $pendingRequest->created_at->format('Y-m-d H:i:s'),
                'message' => 'Your password reset request is pending'
            ]);
        }
    
        // Log the search for notifications
        Log::info("Checking for reset notifications for user: {$user->username} (ID: {$user->id})");
        
        // Simplified approach: Check for ANY notifications of reset type within 30 days
        $completedReset = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->where('type', 'App\Notifications\PasswordResetNotice')
            ->where('created_at', '>', Carbon::now()->subDays(30))
            ->latest('created_at')
            ->first();
        
        if ($completedReset) {
            Log::info("Found reset notification for user: {$user->username}", [
                'notification_id' => $completedReset->id,
                'created_at' => $completedReset->created_at,
                'read_at' => $completedReset->read_at
            ]);
            
            $data = json_decode($completedReset->data);
            
            // Log the decoded data
            Log::info("Notification data for user: {$user->username}", [
                'data' => $data
            ]);
            
            // Check if user has already logged in after reset
            $hasLoggedIn = false;
            
            // If read_at is set, user has already seen this notification
            if ($completedReset->read_at) {
                Log::info("User {$user->username} has already read this notification");
                $hasLoggedIn = true;
            }
            
            // If not logged in, show the reset details
            if (!$hasLoggedIn) {
                return response()->json([
                    'status' => 'reset',
                    'resetAt' => $data->resetAt ?? $completedReset->created_at,
                    'newPassword' => $data->newPassword ?? 'Password data not available',
                    'message' => 'Your password has been reset'
                ]);
            }
        } else {
            Log::info("No reset notifications found for user: {$user->username}");
        }
    
        return response()->json([
            'status' => 'none',
            'message' => 'No password reset request found'
        ]);
    }
}