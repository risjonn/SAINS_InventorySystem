<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Http\Controllers\StockController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StockUpdate;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'stats' => $this->getDashboardStats()
        ]);
    }

    public function getDashboardStats()
    {
        return [
            'totalStocks' => Stock::count(),
            'lowStockItems' => Stock::lowStock()->count(),
            'totalUsers' => User::count(),
            'recentUpdates' => StockUpdate::withTrashed()
                ->where('created_at', '>', now()->subDay()) // Count updates in the last 24 hours
                ->count(),
        ];
    }
}
