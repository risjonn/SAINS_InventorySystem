<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProductThreshold;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ThresholdController extends Controller
{
    // This method returns the current thresholds for the logged-in user.
    public function getUserThresholds(Request $request)
    {
        $currentUser = Auth::user();
        
        // If Super Admin and a target user is provided, use that target; xotherwise default to current user's thresholds
        if ($currentUser->role === 'Super Admin' && $request->has('user_id')) {
            $targetUser = $request->input('user_id');
        } else {
            $targetUser = $currentUser->id;
        }
    
        $thresholds = UserProductThreshold::where('user_id', $targetUser)
                         ->get()
                         ->keyBy('category');
    
        // If no thresholds exist for the user, initialize default thresholds.
        if ($thresholds->isEmpty()) {
            $categories = Product::distinct()->pluck('category');
            foreach ($categories as $category) {
                $record = UserProductThreshold::create([
                    'user_id'          => $targetUser,
                    'category'         => $category,
                    'low_threshold'    => 10,
                    'critical_threshold' => 3,
                ]);
                $thresholds->put($category, $record);
            }
        }
        return response()->json($thresholds);
    }

    // This method updates a threshold for a given category for the logged-in user.
    public function updateThreshold(Request $request)
    {
        $request->validate([
            'category'          => 'required|string',
            'low_threshold'     => 'required|integer|min:0|max:10',
            'critical_threshold'=> 'required|integer|min:0|max:3',
            'user_id'           => 'sometimes|integer|exists:users,id'
        ]);
    
        $currentUser = Auth::user();
        $targetUser = $currentUser->id;
    
        // Only Super Admins are allowed to update thresholds for other admins.
        if ($currentUser->role === 'Super Admin' && $request->has('user_id')) {
            $targetUser = $request->user_id;
        }
    
        $threshold = UserProductThreshold::updateOrCreate(
            ['user_id' => $targetUser, 'category' => $request->category],
            ['low_threshold' => $request->low_threshold, 'critical_threshold' => $request->critical_threshold]
        );
    
        return response()->json(['success' => true, 'threshold' => $threshold]);
    }
}