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
    public function index(Request $request)
    {
        $divisions = Division::all();

        // Check for query parameter; if not present, use the session value (or default to the first division)
        $selectedDivision = $request->query('division', session('selectedDivision', $divisions->first()->id));
        // Save the selected division into the session so it persists across pages
        session(['selectedDivision' => $selectedDivision]);

        if ($selectedDivision === 'all') {
            // Aggregated data across all divisions
            $totalStocks = Stock::where('status', 'available')->count();

            $lowStockItems = Stock::where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 2')
                ->count();

            $userCount = User::count();

            $stockSummary = Stock::where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as quantity'))
                ->groupBy('category')
                ->get();

            $recentTransactions = StockTransaction::orderBy('transaction_date', 'desc')
                ->take(5)
                ->get();
        } else {
            // Data filtered by the selected division
            $totalStocks = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->count();

                $lowStockItems = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 2')
                ->get()
                ->count();

            $userCount = User::where('division_id', $selectedDivision)->count();

            $stockSummary = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as quantity'))
                ->groupBy('category')
                ->get();

            $recentTransactions = StockTransaction::whereHas('stock', function ($query) use ($selectedDivision) {
                    $query->where('division_id', $selectedDivision);
                })
                ->orderBy('transaction_date', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'divisions',
            'selectedDivision',
            'totalStocks',
            'lowStockItems',
            'userCount',
            'stockSummary',
            'recentTransactions'
        ));
    }

    public function selectDivision(Request $request)
    {
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}
