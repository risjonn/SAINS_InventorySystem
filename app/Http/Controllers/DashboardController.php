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
        $user      = auth()->user();

        // 0) Determine selected division
        if ($user->role === 'Super Admin') {
            $selectedDivision = $request->query('division', session('selectedDivision', 'all'));
        } else {
            $selectedDivision = $user->division_id;
        }
        session(['selectedDivision' => $selectedDivision]);

        // 1) Base stock query
        $baseQuery = Stock::with('product')->where('status', 'available');
        if ($selectedDivision !== 'all') {
            $baseQuery->where('division_id', $selectedDivision);
        }
        $totalStocks = (clone $baseQuery)->count();
        $stocks      = $baseQuery->get();

        // ───────────────────────────────────────────────────────────
        // ALL-DIVISIONS VIEW
        // ───────────────────────────────────────────────────────────
        if ($selectedDivision === 'all') {
            // 2a) Low/critical per division
            $lowStockSummary = $stocks
                ->groupBy('division_id')
                ->map(function($divisionStocks, $divisionId) use ($user) {
                    // figure out whose thresholds to use
                    $selectedAdmin = $user->role==='Super Admin'
                        ? User::where('division_id',$divisionId)->where('role','Admin')->first()
                        : null;

                    $thresholdsForAggregation = $selectedAdmin
                        ? UserProductThreshold::where('user_id', $selectedAdmin->id)
                                            ->get()
                                            ->keyBy('product_id')
                        : collect();

                    $defaultLow      = 10;
                    $defaultCritical = 3;
                    $lowCount        = 0;
                    $criticalCount   = 0;

                    foreach ($divisionStocks->groupBy('product_id') as $prodId => $group) {
                        $qty = $group->count();
                        $thr = $thresholdsForAggregation->get($prodId);
                        $lowThr  = $thr->low_threshold      ?? $defaultLow;
                        $critThr = $thr->critical_threshold ?? $defaultCritical;

                        if ($qty <= $critThr) {
                            $criticalCount++;
                        } elseif ($qty <= $lowThr) {
                            $lowCount++;
                        }
                    }

                    return [
                        'lowCount'      => $lowCount,
                        'criticalCount' => $criticalCount,
                        'totalLow'      => $lowCount + $criticalCount,
                    ];
                });

            // 2b) Totals across all divisions
            $lowCount       = $lowStockSummary->sum('lowCount');
            $criticalCount  = $lowStockSummary->sum('criticalCount');
            $lowStockItems  = $lowCount + $criticalCount;

            // 3) Detailed per-division summary
            $divisionsSummary = $stocks
                ->groupBy('division_id')
                ->map(function($divisionStocks, $divisionId) use ($user) {
                    $selectedAdmin = $user->role==='Super Admin'
                        ? User::where('division_id',$divisionId)->where('role','Admin')->first() ?: $user
                        : $user;

                    $thresholdsForAggregation = UserProductThreshold::where('user_id', $selectedAdmin->id)
                        ->get()
                        ->keyBy('product_id');

                    $lowCount      = 0;
                    $criticalCount = 0;

                    foreach ($divisionStocks->groupBy('product_id') as $prodId => $group) {
                        $qty = $group->count();
                        $thr = $thresholdsForAggregation->get($prodId);
                        $lowThr  = $thr->low_threshold      ?? 10;
                        $critThr = $thr->critical_threshold ?? 3;

                        if ($qty <= $critThr) {
                            $criticalCount++;
                        } elseif ($qty <= $lowThr) {
                            $lowCount++;
                        }
                    }

                    $div = Division::find($divisionId);
                    return [
                        'id'             => $divisionId,
                        'name'           => $div?->name ?? 'Unknown',
                        'nonCriticalLow' => $lowCount,
                        'critical'       => $criticalCount,
                    ];
                })
                ->filter(fn($d) => ($d['nonCriticalLow'] + $d['critical']) > 0)
                ->values();

            // 4) All-divisions metrics
            $totalDivisions    = Division::count();
            $affectedDivisions = $divisionsSummary->count();
            $skuCount          = $lowStockItems;
            $topDivisions      = $divisionsSummary
                ->sortByDesc(fn($d) => $d['nonCriticalLow'] + $d['critical'])
                ->take(3)
                ->values();

            // 5) Shared view data
            $stockSummary = $stocks
                ->groupBy(fn($s) => optional($s->product)->category ?? 'Unknown')
                ->map(fn($g) => [
                    'category' => $g->first()->product->category ?? 'Unknown',
                    'quantity' => $g->count()
                ])->values();

            $userCount = User::when($selectedDivision!=='all', fn($q) =>
                $q->where('division_id', $selectedDivision)
            )->count();

            $recentTransactions = StockTransaction::with('stock.product')
                ->when($selectedDivision!=='all', fn($q) =>
                    $q->whereHas('stock', fn($qq) =>
                        $qq->where('division_id', $selectedDivision)
                    )
                )
                ->orderBy('transaction_date','desc')
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'divisions','selectedDivision','totalStocks',
                'lowStockItems','lowCount','criticalCount',
                'totalDivisions','affectedDivisions','skuCount','topDivisions',
                'userCount','stockSummary','recentTransactions',
                'divisionsSummary','user'
            ))
            ->with('divisionName','All Divisions');
        }

        // ───────────────────────────────────────────────────────────
        // SINGLE DIVISION VIEW
        // ───────────────────────────────────────────────────────────

        // determine whose thresholds to use
        if ($user->role === 'Super Admin') {
            $selectedAdmin = User::where('division_id',$selectedDivision)
                                ->where('role','Admin')
                                ->first() ?: $user;
        } else {
            $selectedAdmin = $user;
        }

        $thresholdsForAggregation = UserProductThreshold::where('user_id',$selectedAdmin->id)
            ->get()
            ->keyBy('product_id');

        // SKU‐level flags
        $lowCount      = 0;
        $criticalCount = 0;

        foreach ($stocks->groupBy('product_id') as $prodId => $group) {
            $qty = $group->count();
            $thr = $thresholdsForAggregation->get($prodId);
            $lowThr  = $thr->low_threshold      ?? 10;
            $critThr = $thr->critical_threshold ?? 3;

            if ($qty <= $critThr) {
                $criticalCount++;
            } elseif ($qty <= $lowThr) {
                $lowCount++;
            }
        }

        $lowStockItems   = $lowCount + $criticalCount;
        $divisionsSummary = null;

        // Category‐level metrics
        $catGroups = $stocks->groupBy(fn($s) =>
            strtolower(trim($s->product->category ?? 'Unknown'))
        );
        $totalCategories    = $catGroups->count();
        $affectedCategories = $catGroups
            ->filter(fn($g) => $g
                ->groupBy('product_id')
                ->filter(fn($stk) =>
                    $stk->first()->product->statusFlag($stk->count()) !== 'ok'
                )->isNotEmpty()
            )->count();

        $topCategories = $catGroups
            ->map(fn($group,$cat) => [
                'category'       => ucfirst($cat),
                'critical_count' => $group
                    ->groupBy('product_id')
                    ->filter(fn($stk) =>
                        $stk->first()->product->statusFlag($stk->count()) === 'critical'
                    )->count(),
                'low_count'      => $group
                    ->groupBy('product_id')
                    ->filter(fn($stk) =>
                        $stk->first()->product->statusFlag($stk->count()) === 'low'
                    )->count(),
            ])
            ->map(fn(array $c) => [
                'category'       => $c['category'],
                'critical_count' => $c['critical_count'],
                'low_count'      => $c['low_count'],
                'total'          => $c['critical_count'] + $c['low_count'],
            ])
            ->sortByDesc('total')
            ->take(3)
            ->values();

        $categoryCount = $affectedCategories;
        $skuCount      = $lowStockItems;

        // Shared view data
        $stockSummary = $stocks
            ->groupBy(fn($s) => optional($s->product)->category ?? 'Unknown')
            ->map(fn($g) => ['category'=>$g->first()->product->category,'quantity'=>$g->count()])
            ->values();

        $userCount = $user->role==='Super Admin'
            ? User::count()
            : User::where('division_id',$selectedDivision)->count();

        $recentTransactions = StockTransaction::with('stock.product')
            ->when($selectedDivision!=='all', fn($q) =>
                $q->whereHas('stock', fn($qq) =>
                    $qq->where('division_id',$selectedDivision)
                )
            )
            ->orderBy('transaction_date','desc')
            ->take(5)
            ->get();

        $divisionName = $divisions->find($selectedDivision)?->name ?? 'Unknown';

        return view('dashboard', compact(
            'divisions','selectedDivision','totalStocks',
            'lowStockItems','lowCount','criticalCount',
            'totalCategories','categoryCount','skuCount','topCategories',
            'userCount','stockSummary','recentTransactions','divisionName',
            'divisionsSummary','user'
        ));
    }

    public function selectDivision(Request $request)
    {
        $divisionId = $request->input('division_id'); 
        session(['selectedDivision' => $divisionId]);
        return redirect()->route('dashboard')->with('success', 'Division selected.');
    }
}