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
        $user = auth()->user();

        // If the user is a Super Admin, allow division selection from query string; otherwise, use the user's division.
        if ($user->role === 'Super Admin') {
            // Get selected division from query or session; default to 'all' or first division as needed.
            $selectedDivision = $request->query('division', session('selectedDivision', 'all'));
        } else {
            $selectedDivision = $user->division_id;
        }
        // Save selected division in session
        session(['selectedDivision' => $selectedDivision]);

        // Retrieve data based on selected division
        if ($selectedDivision === 'all') {
            $totalStocks = Stock::where('status', 'available')->count();

            $lowStockItems = Stock::where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 5')
                ->get()
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
            $totalStocks = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->count();

            $lowStockItems = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 5')
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

        // If the user is not a Super Admin, we can retrieve their division directly.
        $divisionName = $selectedDivision === 'all'
            ? 'All Divisions'
            : $divisions->find($selectedDivision)->name;

        return view('dashboard', compact(
            'divisions',
            'selectedDivision',
            'totalStocks',
            'lowStockItems',
            'userCount',
            'stockSummary',
            'recentTransactions',
            'divisionName'
        ));
    }

    public function selectDivision(Request $request)
    {
        // This method is now only used by Super Admins.
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}