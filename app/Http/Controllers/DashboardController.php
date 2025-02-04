<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Method to get the stats for the dashboard
    public function getDashboardStats(Request $request)
    {
        $section = $request->input('section', session('selectedSection', 'Kuching')); // Default to Kuching if no section is set

        // Fetch stats based on the selected section
        $totalStocks = Stock::where('section', $section)->count();
        $lowStockItems = Stock::where('section', $section)->where('quantity', '<=', 5)->count();
        $totalUsers = User::count();  // Or fetch users for the specific section if applicable
        $recentUpdates = Stock::where('section', $section)->latest()->limit(5)->count(); // Adjust as per requirements

        return response()->json([
            'totalStocks' => $totalStocks,
            'lowStockItems' => $lowStockItems,
            'totalUsers' => $totalUsers,
            'recentUpdates' => $recentUpdates,
        ]);
    }

    // Method to get the stocks based on selected section
    public function getStocks(Request $request)
    {
        $section = $request->input('section', session('selectedSection', 'Kuching')); // Default to Kuching if no section is set

        // Fetch stock list based on selected section
        $stocks = Stock::where('section', $section)->get(['name', 'quantity', 'updated_at']);

        return response()->json($stocks);
    }

    // Dashboard main method that displays the dashboard view
    public function index(Request $request)
    {
        $section = $request->input('section', session('selectedSection', 'Kuching')); // Default to Kuching if no section is set
        session(['selectedSection' => $section]);  // Store section in session

        // Fetch the stock data filtered by section
        $stocks = Stock::where('section', $section)->get();

        // Fetch the total stock, low stock items, and users
        $totalStocks = $stocks->count();
        $lowStockItems = $stocks->where('quantity', '<=', 5)->count();  // Example for low stock filter
        $users = User::count();  // Example to count users

        return view('dashboard', [
            'stocks' => $stocks,
            'totalStocks' => $totalStocks,
            'lowStockItems' => $lowStockItems,
            'users' => $users,
            'selectedSection' => $section, // Pass selected section to the view
        ]);
    }
}
