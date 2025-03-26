<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProductThreshold;
use Illuminate\Support\Facades\Auth;

class ThresholdController extends Controller
{
    // This method returns the current thresholds for the logged-in user.
    public function getUserThresholds()
    {
        $user = Auth::user();
        $thresholds = \App\Models\UserProductThreshold::where('user_id', $user->id)
                            ->get()
                            ->keyBy('category');
    
        if ($thresholds->isEmpty()) {
            // Get distinct categories from products
            $categories = \App\Models\Product::distinct()->pluck('category');
            foreach ($categories as $category) {
                $record = \App\Models\UserProductThreshold::create([
                    'user_id' => $user->id,
                    'category' => $category,
                    'low_threshold' => 10,
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
            'category' => 'required|string',
            'low_threshold' => 'required|integer|min:0|max:10',
            'critical_threshold' => 'required|integer|min:0|max:3',
        ]);

        $user = Auth::user();
        $threshold = UserProductThreshold::updateOrCreate(
            ['user_id' => $user->id, 'category' => $request->category],
            ['low_threshold' => $request->low_threshold, 'critical_threshold' => $request->critical_threshold]
        );

        return response()->json(['success' => true, 'threshold' => $threshold]);
    }
}