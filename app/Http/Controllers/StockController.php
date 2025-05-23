<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;
use App\Models\UserProductThreshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Facade;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure the user is authenticated

        // Manually check if the user is an admin or super admin
        $this->middleware(function ($request, $next) {
            if (auth()->check() && !in_array(auth()->user()->role, ['Admin', 'Super Admin'])) {
                abort(403, 'Unauthorized Action');
            }
            return $next($request);
        })->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $category = $request->get('category');
        $sortBy   = $request->query('sort', 'updated_at_desc');

        $stocksQuery = Stock::with(['division', 'product']);

        if ($category) {
            $stocksQuery->whereHas('product', function($query) use ($category) {
                $query->where('category', $category);
            });
        }

        $stocks = $stocksQuery->orderBy('updated_at', 'desc')->paginate(10);

        return view('stocks.index', compact('stocks'));
    }

    public function lowStock(Request $request)
    {
        $user = auth()->user();

        // 1. Determine which division to show
        if ($user->role !== 'Super Admin') {
            $selectedDivision = $user->division_id;
        } else {
            $selectedDivision = $request->query('division', session('selectedDivision', 'all'));
        }

        // 2. "All divisions" summary view
        if ($selectedDivision === 'all') {
            $stocks = Stock::with('product')
                ->where('status', 'available')
                ->get();

            $divisionsSummary = $stocks
                ->groupBy('division_id')
                ->map(function ($divisionStocks, $divisionId) use ($user) {
                    $selectedAdmin = User::where('division_id', $divisionId)
                        ->where('role', 'Admin')
                        ->first();
                    $adminThresholds = $selectedAdmin
                        ? UserProductThreshold::where('user_id', $selectedAdmin->id)
                            ->get()
                            ->keyBy(fn($item) => strtolower(trim($item->category)))
                        : collect();

                    $defaultLowThreshold      = 10;
                    $defaultCriticalThreshold = 3;
                    $lowCount     = 0;
                    $criticalCount = 0;

                    $divisionStocks
                        ->groupBy(fn($stock) => strtolower(trim(optional($stock->product)->category ?? 'Unknown')))
                        ->each(function ($group, $category) use (
                            $adminThresholds,
                            $defaultLowThreshold,
                            $defaultCriticalThreshold,
                            &$lowCount,
                            &$criticalCount
                        ) {
                            $ct = $group->count();

                            $lowThr  = $adminThresholds->has($category)
                                ? $adminThresholds->get($category)->low_threshold
                                : $defaultLowThreshold;
                            $critThr = $adminThresholds->has($category)
                                ? $adminThresholds->get($category)->critical_threshold
                                : $defaultCriticalThreshold;

                            if ($ct <= $lowThr) {
                                $ct <= $critThr ? $criticalCount++ : $lowCount++;
                            }
                        });

                    $divisionObj = Division::find($divisionId);
                    return [
                        'id'             => $divisionId,
                        'name'           => $divisionObj?->name ?? 'Unknown',
                        'nonCriticalLow' => $lowCount,
                        'critical'       => $criticalCount,
                    ];
                })
                ->filter(fn($div) => ($div['nonCriticalLow'] + $div['critical']) > 0)
                ->values();

            return view('stocks.low_summary', compact('divisionsSummary', 'selectedDivision'));
        }

        // 3. Detailed low-stock view for one division
        $division = Division::findOrFail($selectedDivision);
        $divisions = Division::all();
        $stocks    = Stock::with('product')
            ->where('division_id', $selectedDivision)
            ->where('status', 'available')
            ->get();

        $aggregatedStocks = $stocks
            ->groupBy(fn($stock) => strtolower(trim(optional($stock->product)->category ?? 'Unknown')))
            ->map(function ($group, $category) {
                return (object)[
                    'category'   => ucfirst($category),
                    'total'      => $group->count(),
                    'updated_at' => $group->first()->updated_at ?? null,
                ];
            })
            ->values();

        $distinctCategories = $aggregatedStocks->pluck('category')->toArray();
        $categoriesLabels   = $aggregatedStocks->pluck('category')->toArray();
        $categoriesData     = $aggregatedStocks->pluck('total')->toArray();

        if ($user->role === 'Super Admin') {
            $selectedAdmin = User::where('division_id', $selectedDivision)
                ->where('role', 'Admin')
                ->first() ?: $user;
        } else {
            $selectedAdmin = $user;
        }

        $thresholdsForAggregation = UserProductThreshold::where('user_id', $selectedAdmin->id)
            ->get()
            ->keyBy(fn($item) => strtolower(trim($item->category)));
        $thresholdsForUser = $thresholdsForAggregation;

        $lowThresholdsJs = $aggregatedStocks
            ->map(fn($sg) => $thresholdsForAggregation->has(strtolower($sg->category))
                ? $thresholdsForAggregation->get(strtolower($sg->category))->low_threshold
                : 10
            )
            ->toArray();

        $criticalThresholdsJs = $aggregatedStocks
            ->map(fn($sg) => $thresholdsForAggregation->has(strtolower($sg->category))
                ? $thresholdsForAggregation->get(strtolower($sg->category))->critical_threshold
                : 3
            )
            ->toArray();

        return view('stocks.low', compact(
            'stocks',
            'division',
            'divisions',
            'aggregatedStocks',
            'distinctCategories',
            'categoriesLabels',
            'categoriesData',
            'lowThresholdsJs',
            'criticalThresholdsJs',
            'selectedDivision',
            'selectedAdmin',
            'thresholdsForUser'
        ));
    }

    public function transactions(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $search           = $request->query('search', '');
        $sortBy           = $request->query('sortBy', 'transaction_date');
        $sortOrder        = $request->query('sortOrder', 'desc');

        $transactionsQuery = StockTransaction::leftJoin(
                'stocks',
                'stock_transactions.stock_id',
                '=',
                'stocks.id'
            )
            ->leftJoin('users', 'stock_transactions.handled_by', '=', 'users.id')
            ->leftJoin('products', 'stock_transactions.product_code', '=', 'products.product_code')
            ->leftJoin('divisions', 'stocks.division_id', '=', 'divisions.id')
            ->select(
                'stock_transactions.*',
                'stocks.id as stock_id',
                DB::raw('COALESCE(stock_transactions.quantity, stocks.quantity, 1) as trans_qty'),
                'users.name as handled_by_name',
                'divisions.name as division_name',
                'products.category as product_category',
                'products.brand    as product_brand',
                'products.model    as product_model'
            );

        if ($selectedDivision !== 'all') {
            $transactionsQuery->where(function($q) use ($selectedDivision) {
                $q->where('stocks.division_id', $selectedDivision)
                  ->orWhereNull('stocks.division_id');
            });
            $division = Division::find($selectedDivision);
        } else {
            $division = null;
        }

        $filter = strtoupper($request->query('filter', 'IN'));
        if ($filter !== 'ALL') {
            $transactionsQuery->where('stock_transactions.type', $filter);

            if ($filter === 'IN') {
                $transactionsQuery->where(function($q) {
                    $q->where('stocks.status', 'available')
                      ->orWhereNull('stocks.status');
                });
            } elseif ($filter === 'OUT') {
                $transactionsQuery
                    ->where('stock_transactions.type', 'OUT')
                    ->where('stock_transactions.remarks', 'NOT LIKE', '%Reverted OUT%')
                    ->where(function($q) {
                        $q->where('stocks.status', 'OUT')
                          ->orWhereNull('stocks.status');
                    });
            } elseif ($filter === 'LOAN') {
                $transactionsQuery->where(function($q) {
                    $q->where('stocks.status', 'LOAN')
                      ->orWhereNull('stocks.status');
                });
            }
        }

        if ($search) {
            $transactionsQuery->where(function ($q) use ($search, $selectedDivision) {
                $q->where('stock_transactions.serial_number', 'LIKE', $search.'%')
                  ->orWhere('stock_transactions.requisition_no', 'LIKE', "%{$search}%")
                  ->orWhere('products.category', 'LIKE', "%{$search}%")
                  ->orWhere('products.model', 'LIKE', "%{$search}%");
                if ($selectedDivision === 'all') {
                    $q->orWhere('divisions.name', 'LIKE', $search.'%');
                }
            });
        }

        $transactions = $transactionsQuery
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->appends($request->all());

        $bulkList = []; // bulk removed, so empty array

        return view('stocks.transactions', compact('transactions','division','bulkList'));
    }

    public function management(Request $request)
    {
        $selectedDivision = $request->query(
            'division',
            session('selectedDivision', auth()->user()->division_id)
        );
        session(['selectedDivision' => $selectedDivision]);

        \Log::debug("Management page loaded with division ID: {$selectedDivision}");

        if ($request->isMethod('post') && $request->input('force_operation_type') === 'RETURN') {
            // existing RETURN logic unchanged
        }

        $division = Division::find($selectedDivision);
        $search   = $request->query('search', '');
        $sort     = $request->query('sort', 'updated_at_desc');

        $availableStocks = Stock::with('product')
            ->where('division_id', $selectedDivision)
            ->where('status', 'available')
            ->when($search, fn($q) =>
                $q->where('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', fn($p) =>
                      $p->where('model', 'LIKE', "%{$search}%")
                  )
            )
            ->paginate(10);

        $bulkList = []; // bulk removed

        // loaned stocks unchanged
        $loanedStocks = Stock::with(['product','latestLoan'])
            ->where('division_id', $selectedDivision)
            ->where('status', 'LOAN')
            ->when($search, fn($q) =>
                $q->where('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', fn($p) =>
                      $p->where('model', 'LIKE', "%{$search}%")
                  )
            )
            ->paginate(10, ['*'], 'loan_page');

        $allLoaned = Stock::with('product')
            ->where('division_id', $selectedDivision)
            ->where('status', 'LOAN')
            ->when($search, fn($q) =>
                $q->where('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', fn($p) =>
                      $p->where('model', 'LIKE', "%{$search}%")
                  )
            )
            ->get();

        $overdueLoans = $allLoaned->filter(function($stock) {
            $start   = Carbon::parse($stock->loan_start_date ?? $stock->updated_at);
            $period  = $stock->loan_period ?? 90;
            $dueDate = $start->copy()->addWeekdays($period - 1);
            return $dueDate->lt(Carbon::today());
        });
        $overdueCount = $overdueLoans->count();

        return view('stocks.management', [
            'division'               => $division,
            'availableStocks'        => $availableStocks,
            'loanedStocks'           => $loanedStocks,
            'overdueLoans'           => $overdueLoans,
            'overdueCount'           => $overdueCount,
            'bulkList'               => $bulkList,
        ]);
    }

    public function storeTransactionIn(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        // Bulk removed, so no is_bulk check
        $rules = [
            'product_code'   => 'required|string|exists:products,product_code',
            'brand'          => 'nullable|string',
            'requisition_no' => 'nullable|string',
            'erp_number'     => 'nullable|string',
            'model'          => 'required|string',
            'category'       => 'required|string',
            'remarks'        => 'nullable|string',
            'sla_category'   => 'nullable|string',
            'sla'            => 'nullable|string',
            'serial_number'  => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\-]+$/',
                Rule::unique('stocks')->where(function ($query) use ($request) {
                    return $query->where('product_code', $request->input('product_code'));
                }),
            ],
        ];

        $validated = $request->validate(
            $rules,
            [
                'serial_number.required' => 'Serial number is required.',
                'serial_number.unique'   => 'That serial number is already in use for this product.',
                'serial_number.regex'    => 'Serial number format is invalid. Only letters, numbers, and dashes allowed.',
            ]
        );

        $product = Product::where('product_code', $validated['product_code'])->firstOrFail();

        $serial   = $validated['serial_number'];
        $quantity = 1;

        $model    = $validated['model'] ?? $product->model;
        $category = $validated['category'] ?? $product->category;
        $brand    = $validated['brand'] ?? $product->brand;

        $stock = Stock::updateOrCreate(
            ['serial_number' => $serial],
            [
                'serial_number'  => $serial,
                'quantity'       => $quantity,
                'brand'          => $brand,
                'product_code'   => $validated['product_code'],
                'requisition_no' => $validated['requisition_no'] ?? null,
                'erp_number'     => $validated['erp_number'] ?? null,
                'model'          => $model,
                'category'       => $category,
                'division_id'    => $selectedDivision,
                'remarks'        => $validated['remarks'] ?? null,
                'status'         => 'available',
                'updated_at'     => now(),
                'product_id'     => $product->id,
                'sla_category'   => $validated['sla_category'] ?? null,
                'sla'            => $validated['sla'] ?? null,
            ]
        );

        $stock->status = 'available';
        $stock->save();

        StockTransaction::create([
            'stock_id'         => $stock->id,
            'serial_number'    => $serial,
            'stock_model'      => $model,
            'brand'            => $brand,
            'product_code'     => $validated['product_code'],
            'requisition_no'   => $validated['requisition_no'] ?? null,
            'category'         => $category,
            'quantity'         => 1,
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'] ?? null,
            'transaction_date' => now(),
            'product_id'       => $stock->product_id,
            'sla_category'     => $validated['sla_category'] ?? null,
            'sla'              => $validated['sla'] ?? null,
        ]);

        return redirect()->route('stocks.management')->with([
            'success'  => 'Stock Added In Inventory Successfully.',
            'resource' => 'stock',
        ]);
    }

    public function createTransactionOut()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        return view('stocks.transaction_out', compact('division'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('stocks.create', compact('divisions'));
    }

    public function show(Request $request, $category)
    {
        $divisionId = $request->query('division', 'all');
        $search     = $request->query('search');
        $sort       = $request->query('sort', 'updated_at_desc');

        $division = ($divisionId !== 'all') ? Division::find($divisionId) : null;

        $stocksQuery = Stock::with(['division', 'product'])
            ->whereHas('product', fn($q) => $q->where('category', $category))
            ->where('stocks.status', 'available')
            ->when($divisionId !== 'all', fn($q) =>
                $q->where('stocks.division_id', $divisionId)
            )
            ->when($search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('stocks.serial_number', 'like', "%{$search}%")
                       ->orWhere('stocks.requisition_no', 'like', "%{$search}%")
                       ->orWhereHas('product', fn($q3) =>
                           $q3->where('products.model', 'like', "%{$search}%")
                              ->orWhere('products.category', 'like', "%{$search}%")
                       )
                )
            );

        switch ($sort) {
            case 'brand_asc':
                $stocksQuery
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->orderBy('products.brand', 'asc');
                break;
            case 'brand_desc':
                $stocksQuery
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->orderBy('products.brand', 'desc');
                break;
            case 'model_asc':
                $stocksQuery
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->orderBy('products.model', 'asc');
                break;
            case 'model_desc':
                $stocksQuery
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->orderBy('products.model', 'desc');
                break;
            case 'updated_at_asc':
                $stocksQuery->orderBy('stocks.updated_at', 'asc')
                            ->orderBy('stocks.id', 'asc');
                break;
            case 'updated_at_desc':
            default:
                $stocksQuery->orderBy('stocks.updated_at', 'desc')
                            ->orderBy('stocks.id', 'desc');
                break;
        }

        $stocks = $stocksQuery->select('stocks.*')->paginate(10);

        $divisionName = $division ? $division->name : 'All Divisions';

        return view('stocks.show', compact('stocks', 'category', 'division', 'divisionName'))
               ->with('sort', $sort);
    }

    public function overview(Request $request)
    {
        $divisionId = $request->get('division_id') ?? session('selectedDivision', 'all');
        $selectedDivision = $divisionId;

        if ($divisionId === 'all') {
            $divisionName = 'All Divisions';
            $division = null;
        } else {
            $division = Division::find($divisionId);
            if (!$division) {
                return redirect()->route('dashboard')->with('message', 'Invalid division selected.');
            }
            $divisionName = $division->name;
        }

        $search = $request->get('search', '');

        $groupedStocks = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->select('products.category as category', DB::raw('COUNT(*) as total'))
            ->where('stocks.status', 'available')
            ->when($divisionId !== 'all', function ($query) use ($divisionId) {
                return $query->where('stocks.division_id', $divisionId);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('stocks.serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('products.model', 'LIKE', "%{$search}%")
                      ->orWhere('products.category', 'LIKE', "%{$search}%");
                });
            })
            ->groupBy('products.category')
            ->paginate(10);

        return view('stocks.overview', compact(
            'groupedStocks',
            'divisionName',
            'divisionId',
            'selectedDivision',
            'division'
        ));
    }

    public function ajaxSearch(Request $request)
    {
        $selectedDivision = session('selectedDivision', 'all');
        $query = StockTransaction::query();

        if ($selectedDivision !== 'all') {
            $query->where('division_id', $selectedDivision);
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($subQuery) use ($q) {
                $subQuery->where('serial_number', 'LIKE', "%{$q}%")
                         ->orWhere('stock_model', 'LIKE', "%{$q}%");
            });
        }

        $sort = $request->input('sort', 'transaction_date_desc');
        switch ($sort) {
            case 'model_asc':
                $query->orderBy('stock_model', 'asc');
                break;
            case 'model_desc':
                $query->orderBy('stock_model', 'desc');
                break;
            case 'transaction_date_asc':
                $query->orderBy('transaction_date', 'asc');
                break;
            case 'transaction_date_desc':
            default:
                $query->orderBy('transaction_date', 'desc');
                break;
        }

        $transactions = $query->paginate(20);

        if ($request->ajax()) {
            return view('stocks.partials.transactions_container', compact('transactions', 'selectedDivision'))->render();
        }

        return view('stocks.transactions', compact('transactions', 'selectedDivision'));
    }

    public function storeOutLoanReturn(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        $stockId = $request->input('stock_id');
        $stock = Stock::findOrFail($stockId);
        $entityOwnerDivision = $stock->division_id;

        if ($entityOwnerDivision != $selectedDivision) {
            return redirect()->back()->with('error', 'This item does not belong to the selected division.');
        }

        $forceOperationType = $request->input('force_operation_type');

        $rules = [
            'remarks'         => 'required|string',
            'collection_note' => 'nullable|string',
            'quantity'        => 'nullable|integer|min:1',
        ];

        $validated = $request->validate($rules);

        if ($forceOperationType === 'RETURN') {
            $quantity = 1;

            StockTransaction::create([
                'stock_id'         => $stock->id,
                'product_id'       => $stock->product_id,
                'bulk_batch_id'    => null,
                'serial_number'    => $stock->serial_number,
                'stock_model'      => $stock->model,
                'brand'            => $stock->brand,
                'product_code'     => $stock->product_code,
                'requisition_no'   => $stock->requisition_no,
                'category'         => $stock->category,
                'collection_note'  => $validated['collection_note'] ?? null,
                'type'             => 'IN',
                'handled_by'       => auth()->id(),
                'remarks'          => $validated['remarks'] . ' (Returned to inventory)',
                'transaction_date' => now(),
                'quantity'         => $quantity,
            ]);

            $stock->status = 'available';
            $stock->save();

            $originalLoanTransaction = StockTransaction::where('serial_number', $stock->serial_number)
                ->where('type', 'LOAN')
                ->first();

            if ($originalLoanTransaction) {
                $originalLoanTransaction->delete();
            }

            return redirect()->route('stocks.management')->with('success', 'Returned successfully.');
        }

        $operationType = $request->input('operation_type');

        if ($operationType === 'LOAN') {
            $loanData = $request->validate([
                'taken_by'    => 'required|string',
                'loan_period' => 'required|integer|min:1|max:90',
                'manual_date' => 'required|date',
            ]);

            $remarks        = $validated['remarks'] . ' (Taken by: ' . $loanData['taken_by'] . ')';
            $loanStartDate  = Carbon::parse($loanData['manual_date']);
            $dueDate        = $loanStartDate->copy()->addWeekdays($loanData['loan_period'] - 1);

            StockTransaction::create([
                'stock_id'          => $stock->id,
                'product_id'        => $stock->product_id,
                'bulk_batch_id'     => null,
                'serial_number'     => $stock->serial_number,
                'stock_model'       => $stock->model,
                'brand'             => $stock->brand,
                'product_code'      => $stock->product_code,
                'requisition_no'    => $stock->requisition_no,
                'category'          => $stock->category,
                'collection_note'   => $validated['collection_note'] ?? null,
                'type'              => 'LOAN',
                'handled_by'        => auth()->id(),
                'remarks'           => $remarks,
                'transaction_date'  => now(),
                'loan_start_date'   => $loanStartDate,
                'due_date'          => $dueDate,
                'loan_period'       => $loanData['loan_period'],
                'taken_by'          => $loanData['taken_by'],
                'manual_date'       => $loanData['manual_date'],
                'ticket_number'     => $request->input('ticket_number'),
                'manual_requisition'=> $request->input('manual_requisition'),
                'quantity'          => 1,
            ]);

            $stock->status          = 'LOAN';
            $stock->remarks         = $remarks;
            $stock->loan_start_date = $loanStartDate;
            $stock->loan_period     = $loanData['loan_period'];
            $stock->save();

            return redirect()->route('stocks.management')->with('success', 'Loan recorded.');
        }

        if ($operationType === 'OUT') {
            $outData = $request->validate([
                'taken_by'    => 'required|string',
                'manual_date' => 'required|date',
                'remarks'     => 'required|string',
            ]);

            $extendedRemarks = $outData['remarks'] . ' (Taken by: ' . $outData['taken_by'] . ')';

            StockTransaction::create([
                'stock_id'         => $stock->id,
                'product_id'       => $stock->product_id,
                'bulk_batch_id'    => null,
                'serial_number'    => $stock->serial_number,
                'stock_model'      => $stock->model,
                'brand'            => $stock->brand,
                'product_code'     => $stock->product_code,
                'requisition_no'   => $stock->requisition_no,
                'category'         => $stock->category,
                'collection_note'  => $validated['collection_note'] ?? null,
                'type'             => 'OUT',
                'handled_by'       => auth()->id(),
                'remarks'          => $extendedRemarks,
                'transaction_date' => now(),
                'quantity'         => 1,
            ]);

            $stock->status  = 'OUT';
            $stock->remarks = $extendedRemarks;
            $stock->save();

            return redirect()->route('stocks.management')->with('success', 'OUT recorded.');
        }

        return redirect()->route('stocks.management')->with('error', 'Invalid operation.');
    }

    public function loanedStocks(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);

        $loanedStocks = Stock::with('division')
            ->where('division_id', $selectedDivision)
            ->where('status', 'LOAN')
            ->paginate(10);

        return view('stocks.loan', compact('loanedStocks', 'division'));
    }

    public function getTransactionDetails(Request $request)
    {
        $serial = $request->input('serial');

        if (!$serial) {
            return response()->json(['error' => 'Invalid parameters.'], 400);
        }

        $transaction = StockTransaction::where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->first();

        if (!$transaction) {
            return '<div class="alert alert-warning">Transaction not found.</div>';
        }

        $stock = Stock::with('product')->where('serial_number', $serial)->first();

        $history = StockTransaction::where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $brand = $stock->product->brand ?? $transaction->brand ?? 'N/A';
        $model = $stock->product->model ?? $transaction->stock_model ?? 'N/A';
        $category = $stock->product->category ?? $transaction->category ?? 'N/A';
        $productCode = $stock->product->product_code ?? $transaction->product_code ?? 'N/A';

        return view('stocks.partials.stock_details_card', compact(
            'transaction',
            'stock',
            'history',
            'brand',
            'model',
            'category',
            'productCode'
        ));
    }

    public function getStockDetails(Request $request)
    {
        \Log::debug('Stock details request params:', $request->all());

        if ($request->filled('id')) {
            $stock = Stock::with('product')->find($request->id);
            \Log::debug('Looking up stock by ID: ' . $request->id);
        } elseif ($request->filled('serial')) {
            $stock = Stock::with('product')
                ->where('serial_number', $request->serial)
                ->first();
            \Log::debug('Looking up stock by serial: ' . $request->serial);
        } else {
            \Log::warning('Stock details requested without ID or serial');
            return '<div class="alert alert-warning">Invalid parameters. Please provide either an ID or serial number.</div>';
        }

        if (!$stock) {
            \Log::warning('Stock not found for details request');
            return '<div class="alert alert-warning">Stock not found.</div>';
        }

        $transaction = StockTransaction::where('serial_number', $stock->serial_number)
            ->orderBy('transaction_date', 'desc')
            ->first();

        $history = StockTransaction::where('serial_number', $stock->serial_number)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $brand = $stock->product->brand ?? $transaction->brand ?? 'N/A';
        $model = $stock->product->model ?? $transaction->stock_model ?? 'N/A';
        $category = $stock->product->category ?? $transaction->category ?? 'N/A';
        $productCode = $stock->product->product_code ?? $transaction->product_code ?? 'N/A';

        return view('stocks.partials.stock_details_card', compact(
            'transaction',
            'stock',
            'history',
            'brand',
            'model',
            'category',
            'productCode'
        ));
    }

    public function extendLoan(Request $request)
    {
        $data = $request->validate([
            'stock_id'       => 'nullable|exists:stocks,id',
            'transaction_id' => 'nullable|exists:stock_transactions,id',
            'borrower_id'    => 'nullable|exists:organizations,id',
            'quantity'       => 'nullable|integer|min:1',
            'extension_days' => 'required|integer|min:1|max:90',
        ]);

        $now = Carbon::now();

        if (!empty($data['stock_id'])) {
            $stock = Stock::findOrFail($data['stock_id']);

            $originalStart  = $stock->loan_start_date ? Carbon::parse($stock->loan_start_date) : Carbon::parse($stock->updated_at);
            $originalPeriod = $stock->loan_period ?? 90;
            $originalDue    = $originalStart->copy()->addWeekdays($originalPeriod);

            if ($originalDue->isPast()) {
                $stock->loan_start_date = $now;
                $stock->loan_period     = $data['extension_days'];
            } else {
                $stock->loan_period = $originalPeriod + $data['extension_days'];
            }

            $stock->save();

            \Cache::flush();

            return redirect()->route('stocks.management')
                ->with('success', "Loan period extended by {$data['extension_days']} days.")
                ->with('refresh', true);
        }

        return back()->with('error', 'Invalid request.');
    }

    public function revertOut(Request $request)
    {
        $data = $request->validate([
            'transaction_id' => 'required|integer|exists:stock_transactions,id',
            'remarks'        => 'required|string',
        ]);

        $txn = StockTransaction::findOrFail($data['transaction_id']);

        $stock = Stock::where('serial_number', $txn->serial_number)->firstOrFail();

        $stock->status = 'available';
        $stock->save();

        StockTransaction::create([
            'stock_id'         => $stock->id,
            'serial_number'    => $stock->serial_number,
            'stock_model'      => optional($stock->product)->model,
            'brand'            => optional($stock->product)->brand,
            'product_code'     => $stock->product_code,
            'requisition_no'   => $stock->requisition_no,
            'category'         => optional($stock->product)->category,
            'product_id'       => $stock->product_id,
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $data['remarks'] . ' (Reverted OUT)',
            'transaction_date' => now(),
        ]);

        $txn->delete();

        return back()->with('success', 'OUT operation successfully reverted and inventory has been updated.');
    }

    public function checkSerial(Request $request)
    {
        $serial      = $request->query('serial');
        $productCode = $request->query('product_code');

        $other = Stock::where('serial_number', $serial)
                      ->where('product_code', '!=', $productCode)
                      ->first();

        return response()->json([
            'duplicate'      => (bool)$other,
            'other_product'  => $other->product_code ?? null,
        ]);
    }

    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division         = Division::find($selectedDivision);

        // grab active products
        $products   = Product::where('status','active')->orderBy('product_code')->get();

        // grab all categories (no bulk column used)
        $categories = Category::orderBy('name')->get();

        return view('stocks.transaction_in', compact(
            'division','products','categories'
        ));
    }

    public function stockReport(Request $request)
    {
        $reportType = $request->query('report_type', 'available');
        $divisionFromQuery = $request->query('division');

        if ($divisionFromQuery) {
            $selectedDivision = $divisionFromQuery;
            session(['selectedReportDivision' => $selectedDivision]);
        } else {
            $selectedDivision = session('selectedReportDivision', 'all_divisions');
        }

        \Log::debug('Selected Division in controller: ' . $selectedDivision);
        \Log::debug('Report Type: ' . $reportType);

        $availableStocksQuery = Stock::with(['product', 'division'])
            ->where('status', 'available');

        $loanedStocksQuery = Stock::with(['product', 'division', 'latestTransaction'])
            ->where('status', 'LOAN');

        if ($selectedDivision !== 'all_divisions') {
            if (is_numeric($selectedDivision)) {
                $availableStocksQuery->where('division_id', $selectedDivision);
                $loanedStocksQuery->where('division_id', $selectedDivision);
            } else {
                $division = Division::where('name', $selectedDivision)->first();
                if ($division) {
                    $availableStocksQuery->where('division_id', $division->id);
                    $loanedStocksQuery->where('division_id', $division->id);
                }
            }
        }

        $availableStocks = $availableStocksQuery->get();
        $loanedStocks = $loanedStocksQuery->get();

        \Log::debug('Available Stocks Count: ' . $availableStocks->count());
        \Log::debug('Loaned Stocks Count: ' . $loanedStocks->count());

        return view('stocks.stock_report', compact(
            'availableStocks',
            'loanedStocks',
            'selectedDivision',
            'reportType'
        ));
    }
}
