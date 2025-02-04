<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\User;
use App\Models\StockUpdate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->query('section', null); // Get selected section

        return view('dashboard', [
            'stats' => $this->getDashboardStats($section)
        ]);
    }

    public function getDashboardStats($section = null)
    {
        $query = Stock::query();

        if ($section) {
            $query->where('section', $section); // Filter by section
        }

        return [
            'totalStocks' => $query->count(),
            'lowStockItems' => $query->where('quantity', '<', 10)->count(),
            'totalUsers' => User::count(),
            'recentUpdates' => StockUpdate::withTrashed()
                ->where('created_at', '>', now()->subDay()) // Count updates in the last 24 hours
                ->count(),
        ];
    }

    public function getStockBySection(Request $request)
    {
        $section = $request->query('section', null);

        if (!$section) {
            return response()->json([]); // Return empty if no section selected
        }

        $stocks = Stock::where('section', $section)->get();

        return response()->json($stocks);
    }
}
