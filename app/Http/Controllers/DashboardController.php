<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\User;
use App\Models\Division;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $divisions = Division::all();
        // Get selected division from the query parameter; default to the first division if none provided
        $selectedDivision = request()->query('division', $divisions->first()->id);
        // Store the selected division in session
        session(['selectedDivision' => $selectedDivision]);

        // Count total stocks in the selected division
        $totalStocks = Stock::where('division_id', $selectedDivision)->count();

        // Fix: Low stock items (assume low stock means < 2 items per category)
        $lowStockItems = Stock::select('category', DB::raw('COUNT(*) as count'))
            ->where('division_id', $selectedDivision)
            ->groupBy('category')
            ->havingRaw('COUNT(*) < 2')
            ->count(); // Corrected to count categories properly

        // Count users in the selected division
        $userCount = User::where('division_id', $selectedDivision)->count();

        // Fix: Stock summary by category (for the selected division)
        $stockSummary = Stock::where('division_id', $selectedDivision)
            ->select('category', DB::raw('COUNT(*) as quantity'))
            ->groupBy('category')
            ->get();

        // Fix: Recent transactions filtered by division
        $recentTransactions = StockTransaction::whereHas('stock', function ($query) use ($selectedDivision) {
                $query->where('division_id', $selectedDivision);
            })
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('divisions', 'selectedDivision', 'totalStocks', 'lowStockItems', 'userCount', 'stockSummary', 'recentTransactions'));
    }
    public function selectDivision(Request $request)
{
    // Assume $divisionId is determined by the user's selection
    $divisionId = $request->input('division_id'); 
    // Store the division in session
    session(['selectedDivision' => $divisionId]);

    // Redirect to the dashboard or the overview page
    return redirect()->route('dashboard')->with('success', 'Division selected.');
}

}
