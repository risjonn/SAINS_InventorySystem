<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'recentUpdates' => Stock::recentUpdates()->count()
        ];
    }
}
