<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProductThreshold;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ThresholdController extends Controller
{
    public function update(Request $request, Product $product)
    {
        // 1) Validate with new caps
        $data = $request->validate([
            'low'      => 'required|integer|min:0|max:30',
            'critical' => 'required|integer|min:0|max:20',
            'user_id'  => 'sometimes|integer|exists:users,id',
        ]);

        // 2) Determine target user: if Super Admin and a user_id was passed, use that.
        $current = Auth::user();
        if ($current->role === 'Super Admin' && isset($data['user_id'])) {
            $targetUser = $data['user_id'];
        } else {
            $targetUser = $current->id;
        }

        // 3) Update or create the per-product threshold
        $threshold = UserProductThreshold::updateOrCreate(
            [
            'user_id'    => $targetUser,
            'product_id' => $product->id,
            ],
            [
            'low_threshold'      => $data['low'],
            'critical_threshold' => $data['critical'],
            ]
        );

        // 4) Return JSON
        return response()->json([
            'success'   => true,
            'threshold' => [
                'low'      => $threshold->low_threshold,
                'critical' => $threshold->critical_threshold,
            ],
        ]);
    }
}