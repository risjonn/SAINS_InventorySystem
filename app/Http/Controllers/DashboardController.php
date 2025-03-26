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

        if ($user->role === 'Super Admin') {
            $selectedDivision = $request->query('division', session('selectedDivision', 'all'));
        } else {
            $selectedDivision = $user->division_id;
        }

        session(['selectedDivision' => $selectedDivision]);

        // Shared queries
        $totalStocksQuery = Stock::with('product')->where('status', 'available');
        $stockSummaryQuery = Stock::with('product')->where('status', 'available');

        if ($selectedDivision !== 'all') {
            $totalStocksQuery->where('division_id', $selectedDivision);
            $stockSummaryQuery->where('division_id', $selectedDivision);
        }

        $totalStocks = $totalStocksQuery->count();

        // Get all available stocks, grouped by category via product relationship
        $stocks = $stockSummaryQuery->get();

        $lowStockItems = $stocks->groupBy(function ($stock) {
            return optional($stock->product)->category ?? 'Unknown';
        })->filter(function ($group) {
            return $group->count() < 5;
        })->count();

        $stockSummary = $stocks->groupBy(function ($stock) {
            return optional($stock->product)->category ?? 'Unknown';
        })->map(function ($group) {
            return [
                'category' => $group->first()->product->category ?? 'Unknown',
                'quantity' => $group->count()
            ];
        })->values();

        $userCount = $selectedDivision === 'all'
            ? User::count()
            : User::where('division_id', $selectedDivision)->count();

        $recentTransactions = StockTransaction::with('stock.product')
            ->when($selectedDivision !== 'all', function ($query) use ($selectedDivision) {
                $query->whereHas('stock', function ($q) use ($selectedDivision) {
                    $q->where('division_id', $selectedDivision);
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        $divisionName = $selectedDivision === 'all'
            ? 'All Divisions'
            : $divisions->find($selectedDivision)?->name ?? 'Unknown';

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
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}
