<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\User;
use App\Models\Division;
use App\Models\StockTransaction;
use App\Models\UserProductThreshold;
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

        // Build the base queries for available stocks.
        $totalStocksQuery = Stock::with('product')->where('status', 'available');
        $stockSummaryQuery = Stock::with('product')->where('status', 'available');

        if ($selectedDivision !== 'all') {
            $totalStocksQuery->where('division_id', $selectedDivision);
            $stockSummaryQuery->where('division_id', $selectedDivision);
        }

        $totalStocks = $totalStocksQuery->count();

        // Get all available stocks.
        $stocks = $stockSummaryQuery->get();

        // Load the logged-in user's thresholds and key them by normalized category.
        $userThresholds = UserProductThreshold::where('user_id', $user->id)
            ->get()
            ->keyBy(function ($item) {
                return strtolower(trim($item->category));
            });

        // Calculate low stock items based on division selection.
        if ($selectedDivision === 'all') {
            // For each division, group stocks by category and count low-stock categories.
            $lowStockItems = $stocks->groupBy('division_id')->map(function ($divisionStocks) use ($userThresholds) {
                return $divisionStocks->groupBy(function ($stock) {
                    return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
                })->filter(function ($group, $category) use ($userThresholds) {
                    $defaultThreshold = 5;
                    $threshold = $userThresholds->has($category)
                        ? $userThresholds->get($category)->low_threshold
                        : $defaultThreshold;
                    return $group->count() < $threshold;
                })->count();
            })->sum();

            // Build a breakdown per division.
            $divisionsSummary = $stocks->groupBy('division_id')->map(function ($divisionStocks, $divisionId) use ($userThresholds) {
                // Group by normalized category.
                $grouped = $divisionStocks->groupBy(function ($stock) {
                    return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
                });
                $defaultThreshold = 5;
                $defaultCritical = 5;
                // Count all categories in low stock.
                $lowCount = $grouped->filter(function ($group, $category) use ($userThresholds, $defaultThreshold) {
                    $threshold = $userThresholds->has($category)
                        ? $userThresholds->get($category)->low_threshold
                        : $defaultThreshold;
                    return $group->count() < $threshold;
                })->count();
                // Count categories that are critical.
                $criticalCount = $grouped->filter(function ($group, $category) use ($defaultCritical) {
                    return $group->count() < $defaultCritical;
                })->count();
                // Non-critical low count.
                $nonCritical = $lowCount - $criticalCount;
                $division = Division::find($divisionId);
                return [
                    'id'             => $divisionId,
                    'name'           => $division ? $division->name : 'Unknown',
                    'nonCriticalLow' => $nonCritical,
                    'critical'       => $criticalCount,
                ];
            })->values();
        } else {
            // For a specific division.
            $lowStockItems = $stocks->groupBy(function ($stock) {
                return optional($stock->product)->category ?? 'Unknown';
            })->filter(function ($group, $category) use ($userThresholds) {
                $defaultThreshold = 5;
                $threshold = $userThresholds->has($category)
                    ? $userThresholds->get($category)->low_threshold
                    : $defaultThreshold;
                return $group->count() < $threshold;
            })->count();
            $divisionsSummary = null;
        }

        // Build a summary of stock counts per category.
        $stockSummary = $stocks->groupBy(function ($stock) {
                return optional($stock->product)->category ?? 'Unknown';
            })->map(function ($group) {
                return [
                    'category' => $group->first()->product->category ?? 'Unknown',
                    'quantity' => $group->count()
                ];
            })->values();

        // Get a count of users.
        $userCount = $selectedDivision === 'all'
            ? User::count()
            : User::where('division_id', $selectedDivision)->count();

        // Load the recent transactions.
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
            'divisionName',
            'divisionsSummary' // Pass it to the view
        ));
    }

    public function selectDivision(Request $request)
    {
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}
