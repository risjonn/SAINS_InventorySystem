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
        
        // Determine the selected division:
        // If a division is provided in the query string, use it and store it in the session.
        // Otherwise, fall back to the stored session value, or default to the first division's id.
        if ($request->has('division')) {
            $selectedDivision = $request->query('division');
            session(['selectedDivision' => $selectedDivision]);
        } else {
            $selectedDivision = session('selectedDivision', $divisions->first()->id);
        }
        
        // Now build your queries conditionally.
        // If the user selected "all" then don't filter by division.
        $totalStocks = Stock::when($selectedDivision !== 'all', function($query) use ($selectedDivision) {
            return $query->where('division_id', $selectedDivision);
        })->count();
        
        $lowStockItems = Stock::select('category', DB::raw('COUNT(*) as count'))
            ->when($selectedDivision !== 'all', function($query) use ($selectedDivision) {
                return $query->where('division_id', $selectedDivision);
            })
            ->groupBy('category')
            ->havingRaw('COUNT(*) < 2')
            ->get()
            ->count();
        
        $userCount = User::when($selectedDivision !== 'all', function($query) use ($selectedDivision) {
            return $query->where('division_id', $selectedDivision);
        })->count();
        
        $stockSummary = Stock::when($selectedDivision !== 'all', function($query) use ($selectedDivision) {
            return $query->where('division_id', $selectedDivision);
        })
        ->select('category', DB::raw('COUNT(*) as quantity'))
        ->groupBy('category')
        ->get();
        
        $recentTransactions = StockTransaction::when($selectedDivision !== 'all', function($query) use ($selectedDivision) {
            return $query->whereHas('stock', function($query) use ($selectedDivision) {
                $query->where('division_id', $selectedDivision);
            });
        })
        ->orderBy('transaction_date', 'desc')
        ->take(5)
        ->get();
        
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
}