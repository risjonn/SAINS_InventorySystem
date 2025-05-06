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
        $stocks = $stockSummaryQuery->get();
    
        if ($selectedDivision === 'all') {
            // For each division, aggregate stocks using thresholds.
            $lowStockSummary = $stocks->groupBy('division_id')->map(function ($divisionStocks, $divisionId) use ($user) {
                if ($user->role === 'Super Admin') {
                    $selectedAdmin = User::where('division_id', $divisionId)
                        ->where('role', 'Admin')
                        ->first();
                    $thresholdsForAggregation = $selectedAdmin
                        ? UserProductThreshold::where('user_id', $selectedAdmin->id)
                              ->get()
                              ->keyBy(function ($item) {
                                  return strtolower(trim($item->category));
                              })
                        : collect();
                } else {
                    $thresholdsForAggregation = UserProductThreshold::where('user_id', $user->id)
                        ->get()
                        ->keyBy(function ($item) {
                            return strtolower(trim($item->category));
                        });
                }
    
                // Group stocks by normalized category.
                $grouped = $divisionStocks->groupBy(function ($stock) {
                    return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
                });
    
                // Set defaults to be consistent with low.blade.php.
                $defaultLowThreshold = 10;
                $defaultCriticalThreshold = 3;
                $lowCount = 0;
                $criticalCount = 0;
                foreach ($grouped as $category => $group) {
                    $lowThreshold = $thresholdsForAggregation->has($category)
                        ? $thresholdsForAggregation->get($category)->low_threshold
                        : $defaultLowThreshold;
                    $criticalThreshold = $thresholdsForAggregation->has($category)
                        ? $thresholdsForAggregation->get($category)->critical_threshold
                        : $defaultCriticalThreshold;
                    if ($group->count() <= $lowThreshold) {
                        if ($group->count() <= $criticalThreshold) {
                            $criticalCount++;
                        } else {
                            $lowCount++;
                        }
                    }
                }
                return [
                    'lowCount' => $lowCount,
                    'criticalCount' => $criticalCount,
                    'totalLow' => $lowCount + $criticalCount
                ];
            });
            
            $lowCount = $lowStockSummary->sum('lowCount');
            $criticalCount = $lowStockSummary->sum('criticalCount');
            $lowStockItems = $lowCount + $criticalCount;
    
            // Build a breakdown per division.
            $divisionsSummary = $stocks->groupBy('division_id')->map(function ($divisionStocks, $divisionId) use ($user) {
                if ($user->role === 'Super Admin') {
                    $selectedAdmin = User::where('division_id', $divisionId)
                        ->where('role', 'Admin')
                        ->first();
                    $thresholdsForAggregation = $selectedAdmin
                        ? UserProductThreshold::where('user_id', $selectedAdmin->id)
                              ->get()
                              ->keyBy(function ($item) {
                                  return strtolower(trim($item->category));
                              })
                        : collect();
                } else {
                    $thresholdsForAggregation = UserProductThreshold::where('user_id', $user->id)
                        ->get()
                        ->keyBy(function ($item) {
                            return strtolower(trim($item->category));
                        });
                }
                $grouped = $divisionStocks->groupBy(function ($stock) {
                    return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
                });
                $defaultLowThreshold = 10;
                $defaultCriticalThreshold = 3;
                $lowCount = 0;
                $criticalCount = 0;
                foreach ($grouped as $category => $group) {
                    $lowThreshold = $thresholdsForAggregation->has($category)
                        ? $thresholdsForAggregation->get($category)->low_threshold
                        : $defaultLowThreshold;
                    $criticalThreshold = $thresholdsForAggregation->has($category)
                        ? $thresholdsForAggregation->get($category)->critical_threshold
                        : $defaultCriticalThreshold;
                    if ($group->count() <= $lowThreshold) {
                        if ($group->count() <= $criticalThreshold) {
                            $criticalCount++;
                        } else {
                            $lowCount++;
                        }
                    }
                }
                $divisionObj = Division::find($divisionId);
                return [
                    'id'             => $divisionId,
                    'name'           => $divisionObj ? $divisionObj->name : 'Unknown',
                    'nonCriticalLow' => $lowCount,
                    'critical'       => $criticalCount,
                ];
            })->filter(function ($division) {
                return ($division['nonCriticalLow'] + $division['critical']) > 0;
            })->values();
        } else {
            // For a specific division.
            $grouped = $stocks->groupBy(function ($stock) {
                return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
            });
            if ($user->role === 'Super Admin') {
                $selectedAdmin = User::where('division_id', $selectedDivision)
                    ->where('role', 'Admin')
                    ->first();
                $thresholdsForAggregation = $selectedAdmin
                    ? UserProductThreshold::where('user_id', $selectedAdmin->id)
                          ->get()
                          ->keyBy(function ($item) {
                              return strtolower(trim($item->category));
                          })
                    : collect();
            } else {
                $thresholdsForAggregation = UserProductThreshold::where('user_id', $user->id)
                    ->get()
                    ->keyBy(function ($item) {
                        return strtolower(trim($item->category));
                    });
            }
            $defaultLowThreshold = 10;
            $defaultCriticalThreshold = 3;
            $lowCount = 0;
            $criticalCount = 0;
            foreach ($grouped as $category => $group) {
                $lowThreshold = $thresholdsForAggregation->has($category)
                    ? $thresholdsForAggregation->get($category)->low_threshold
                    : $defaultLowThreshold;
                $criticalThreshold = $thresholdsForAggregation->has($category)
                    ? $thresholdsForAggregation->get($category)->critical_threshold
                    : $defaultCriticalThreshold;
                if ($group->count() <= $lowThreshold) {
                    if ($group->count() <= $criticalThreshold) {
                        $criticalCount++;
                    } else {
                        $lowCount++;
                    }
                }
            }
            $divisionsSummary = null;
            $lowStockItems = $lowCount + $criticalCount;
        }
    
        // Build summary data for categories (charts, etc.)
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
            'lowCount',        // Added separate lowCount
            'criticalCount',   // Added separate criticalCount
            'userCount',
            'stockSummary',
            'recentTransactions',
            'divisionName',
            'divisionsSummary',
            'user'
        ));
    }

    public function selectDivision(Request $request)
    {
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}