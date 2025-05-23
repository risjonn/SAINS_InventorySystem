<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product; 
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;
use App\Models\UserProductThreshold;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Facade;
use Carbon\Carbon;   
use Illuminate\Validation\Rule;
use App\Models\BulkBatch; 
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

    // 0. Get your bulk‐handled categories from the Product model
  $bulkCategories = \App\Models\Category::getBulkList();


    $thresholdsForAggregation = collect();

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
            ->map(function ($divisionStocks, $divisionId) use ($bulkCategories) {
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
                        $bulkCategories,
                        &$lowCount,
                        &$criticalCount
                    ) {
                        // if this category is bulk, sum quantities; otherwise count items
                        $isBulk = in_array($category, $bulkCategories, true);
                        $ct     = $isBulk ? $group->sum('quantity') : $group->count();

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

    // Group and aggregate, summing quantity for bulk categories
    $aggregatedStocks = $stocks
        ->groupBy(fn($stock) => strtolower(trim(optional($stock->product)->category ?? 'Unknown')))
        ->map(function ($group, $category) use ($bulkCategories) {
            $isBulk = in_array($category, $bulkCategories, true);

            return (object)[
                'category'   => ucfirst($category),
                'total'      => $isBulk ? $group->sum('quantity') : $group->count(),
                'updated_at' => $group->first()->updated_at ?? null,
            ];
        })
        ->values();

    $distinctCategories = $aggregatedStocks->pluck('category')->toArray();
    $categoriesLabels   = $aggregatedStocks->pluck('category')->toArray();
    $categoriesData     = $aggregatedStocks->pluck('total')->toArray();

    // 4. Always define $selectedAdmin
    if ($user->role === 'Super Admin') {
        $selectedAdmin = User::where('division_id', $selectedDivision)
            ->where('role', 'Admin')
            ->first() ?: $user;
    } else {
        $selectedAdmin = $user;
    }

    // Build thresholds map for table logic
    $thresholdsForAggregation = UserProductThreshold::where('user_id', $selectedAdmin->id)
        ->get()
        ->keyBy(fn($item) => strtolower(trim($item->category)));
    $thresholdsForUser = $thresholdsForAggregation;

    // 5. Build JS arrays so Blade/Chart.js can color & filter correctly
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

    // 6. Render the detailed view
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

    // ⚡️ JOIN on stock_id instead of serial_number
    $transactionsQuery = StockTransaction::leftJoin(
            'stocks',
            'stock_transactions.stock_id',
            '=',
            'stocks.id'
        )
        ->leftJoin('users',         'stock_transactions.handled_by', '=', 'users.id')
        ->leftJoin('products', 'stock_transactions.product_code', '=', 'products.product_code')
        ->leftJoin('divisions',     'stocks.division_id',             '=', 'divisions.id')
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

    // Division filter
    if ($selectedDivision !== 'all') {
        $transactionsQuery->where(function($q) use ($selectedDivision) {
            $q->where('stocks.division_id', $selectedDivision)
              ->orWhereNull('stocks.division_id');
        });
        $division = Division::find($selectedDivision);
    } else {
        $division = null;
    }

    // Type filter
    $filter = strtoupper($request->query('filter', 'IN'));
    if ($filter !== 'ALL') {
        $transactionsQuery->where('stock_transactions.type', $filter);

        if ($filter === 'IN') {
            $transactionsQuery->where(function($q) {
                $q->where('stocks.status', 'available')
                  ->orWhereNull('stocks.status');
            });
        }      elseif ($filter === 'OUT') {
            $transactionsQuery
                // only raw OUTs
                ->where('stock_transactions.type', 'OUT')
                // skip ones already reverted
                ->where('stock_transactions.remarks', 'NOT LIKE', '%Reverted OUT%')
                // still only show stocks whose status is OUT
                ->where(function($q) {
                    $q->where('stocks.status', 'OUT')
                      ->orWhereNull('stocks.status');
                });
            }elseif ($filter === 'LOAN') {
            $transactionsQuery->where(function($q) {
                $q->where('stocks.status', 'LOAN')
                  ->orWhereNull('stocks.status');
            });
        }
    }
    // Search
    if ($search) {
        $transactionsQuery->where(function ($q) use ($search, $selectedDivision) {
            $q->where('stock_transactions.serial_number',   'LIKE', $search.'%')
              ->orWhere('stock_transactions.requisition_no', 'LIKE', "%{$search}%")
              ->orWhere('products.category',                'LIKE', "%{$search}%")
              ->orWhere('products.model',                   'LIKE', "%{$search}%");
            if ($selectedDivision === 'all') {
                $q->orWhere('divisions.name', 'LIKE', $search.'%');
            }
        });
    }

    // Order & paginate
    $transactions = $transactionsQuery
        ->orderBy($sortBy, $sortOrder)
        ->paginate(10)
        ->appends($request->all());

    $bulkList = Category::where('is_bulk', true)
        ->pluck('name')
        ->map(fn($n) => strtolower($n))
        ->toArray();

    return view('stocks.transactions', compact('transactions','division','bulkList'));
}

public function management(Request $request)
{
    // ←– NEW: prefer URL ?division=… over session (default to user's division)
    $selectedDivision = $request->query(
        'division',
        session('selectedDivision', auth()->user()->division_id)
    );
    session(['selectedDivision' => $selectedDivision]);
    
    \Log::debug("Management page loaded with division ID: {$selectedDivision}");
    
    // --- RETURN logic (unchanged) ---
    if ($request->isMethod('post') && $request->input('force_operation_type') === 'RETURN') {
        // ... your existing RETURN logic here ...
    }

    // --- GET: build the page ---
    $division = Division::find($selectedDivision);
    $search   = $request->query('search', '');
    $sort     = $request->query('sort', 'updated_at_desc');

    // Available stocks - non-bulk (unchanged)
    $availableStocks = Stock::with('product')
        ->where('division_id', $selectedDivision)
        ->where('status', 'available')
        ->when($search, fn($q) =>
            $q->where('serial_number', 'LIKE', "%{$search}%")
              ->orWhereHas('product', fn($p) =>
                  $p->where('model', 'LIKE', "%{$search}%")
              )
        )
        ->get(); // Changed from paginate(10) for proper merging

    // Bulk‐list for UI toggles & available batches (unchanged)
    $bulkList = Category::where('is_bulk', true)
        ->pluck('name')
        ->map(fn($name) => strtolower($name))
        ->toArray();

    // Get bulk batches with positive remaining quantity (unchanged)
    $availableBatches = BulkBatch::with('product')
        ->where('division_id', $selectedDivision)
        ->where('remaining_quantity', '>', 0)
        ->when($search, function ($q) use ($search, $bulkList) {
            $q->whereHas('product', function($q2) use ($search, $bulkList) {
                $q2->whereIn('category', $bulkList)
                   ->where(function($q3) use ($search) {
                        $q3->where('model', 'LIKE', "%{$search}%")
                           ->orWhere('brand', 'LIKE', "%{$search}%")
                           ->orWhere('category', 'LIKE', "%{$search}%");
                   });
            });
        })
        ->get();

    $totalBatchQty = $availableBatches->sum('remaining_quantity');

    // --- Map bulk batches and regular stocks for consistent display ---
    $mappedBulk = $availableBatches->map(function ($batch) {
        return (object)[
            'id'             => 'bulk-' . $batch->id,
            'serial_number'  => $batch->remaining_quantity,
            'model'          => $batch->product->model ?? 'N/A',
            'brand'          => $batch->product->brand ?? 'N/A',
            'category'       => $batch->product->category ?? 'N/A',
            'requisition_no' => $batch->manual_requisition ?? null,
            'erp_number'     => $batch->collection_note ?? null,
            'sla_category'   => $batch->sla_category ?? null,
            'remarks'        => $batch->remarks ?? null,
            'updated_at'     => $batch->received_at,
            'is_bulk'        => true,
            'product_id'     => $batch->product_id,
            'quantity'       => $batch->remaining_quantity,
            'received_at'    => $batch->received_at,
            'product'        => $batch->product,
        ];
    });

    $mappedStocks = $availableStocks->map(function ($stock) {
        return (object)[
            'id'             => $stock->id,
            'serial_number'  => $stock->serial_number,
            'model'          => $stock->product->model ?? 'N/A',
            'brand'          => $stock->product->brand ?? 'N/A',
            'category'       => $stock->product->category ?? 'N/A',
            'requisition_no' => $stock->requisition_no ?? null,
            'erp_number'     => $stock->erp_number ?? null,
            'sla_category'   => $stock->sla_category ?? null,
            'remarks'        => $stock->remarks ?? null,
            'updated_at'     => $stock->updated_at,
            'is_bulk'        => false,
            'product'        => $stock->product,
        ];
    });

    // Combine bulk and regular stocks here before sorting
    $combinedAvailable = $mappedBulk->concat($mappedStocks);

    // Default sort by updated_at descending
    $combinedAvailable = $combinedAvailable->sortByDesc('updated_at');

    // Apply sorting based on request or default to updated_at descending
    switch ($sort) {
        case 'model_asc':
            $combinedAvailable = $combinedAvailable->sortBy('model');
            break;
        case 'model_desc':
            $combinedAvailable = $combinedAvailable->sortByDesc('model');
            break;
        case 'category_asc':
            $combinedAvailable = $combinedAvailable->sortBy('category');
            break;
        case 'category_desc':
            $combinedAvailable = $combinedAvailable->sortByDesc('category');
            break;
        case 'sla_category_asc':
            $combinedAvailable = $combinedAvailable->sortBy('sla_category');
            break;
        case 'sla_category_desc':
            $combinedAvailable = $combinedAvailable->sortByDesc('sla_category');
            break;
        case 'updated_at_asc':
            $combinedAvailable = $combinedAvailable->sortBy('updated_at');
            break;
        case 'updated_at_desc':
        default:
            $combinedAvailable = $combinedAvailable->sortByDesc('updated_at');
            break;
    }

    // Manual pagination for combined available stocks
    $page = LengthAwarePaginator::resolveCurrentPage('available_page');
    $perPage = 10;
    $items = $combinedAvailable->slice(($page - 1) * $perPage, $perPage)->values();

    $paginatedAvailable = new LengthAwarePaginator(
        $items,
        $combinedAvailable->count(),
        $perPage,
        $page,
        [
            'path'     => request()->url(),
            'query'    => request()->query(),
            'pageName' => 'available_page',
        ]
    );

    // Rest of the loaned stocks logic...
    // (the code for loaned stocks remains mostly unchanged)
    
    // Regular loaned stocks (unchanged)
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

    // Overdue counting (unchanged)
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

    // =================== BULK LOAN FETCHING ===================

    // fetch all LOAN‐type bulk transactions for this division
    $loanedBulkTransactions = DB::table('stock_transactions AS st')
        ->join('bulk_batches AS bb', 'st.bulk_batch_id', '=', 'bb.id')
        ->leftJoin('products AS p', 'bb.product_id', '=', 'p.id')
        ->select(
            'st.id',
            'st.bulk_batch_id',
            'st.transaction_date',
            'st.quantity',
            'st.remarks',
            'st.taken_by',
            'st.requisition_no',
            'st.ticket_number',
            'st.loan_period',
            'bb.collection_note',
            'bb.manual_requisition',
            'p.category',
            'p.brand',
            'p.model'
        )
        ->where('st.type', '=', 'LOAN')
        ->where('bb.division_id', '=', $selectedDivision)
        ->where('st.quantity', '>', 0)
        ->whereNotNull('st.bulk_batch_id')
        ->orderBy('st.transaction_date','desc')
        ->get();

    \Log::debug("Found {$loanedBulkTransactions->count()} bulk loan transactions for division {$selectedDivision}");

    // map into the same structure as regular loans
    $bulkAsStocks = collect();
    foreach ($loanedBulkTransactions as $txn) {
        $category = $txn->category ?? 'N/A';
        $brand    = $txn->brand    ?? 'N/A';
        $model    = $txn->model    ?? 'N/A';

        $takenBy = $txn->taken_by ?? 'N/A';
        if (!$takenBy && $txn->remarks && preg_match('/Taken by:\s*([^)]+)/', $txn->remarks, $m)) {
            $takenBy = trim($m[1]);
        }

        \Log::debug("Processing bulk loan: ID={$txn->id}, Batch={$txn->bulk_batch_id}, Qty={$txn->quantity}, Model={$model}");

        $bulkAsStocks->push((object)[
            'id'              => 'bulk-'.$txn->id,
            'serial_number'   => null,
            'quantity'        => $txn->quantity,
            'model'           => $model,
            'brand'           => $brand,
            'category'        => $category,
            'requisition_no'  => $txn->requisition_no ?? $txn->manual_requisition ?? null,
            'erp_number'      => $txn->collection_note ?? null,
            'remarks'         => $txn->remarks ?? null,
            'loan_start_date' => $txn->transaction_date,
            'loan_period'     => $txn->loan_period ?? 90,
            'updated_at'      => $txn->transaction_date,
            'taken_by'        => $takenBy,
            'is_bulk'         => true,
            'transaction_id'  => $txn->id,
            'bulk_batch_id'   => $txn->bulk_batch_id,
        ]);
    }

    // 1) Map regular loans to objects
    $loanedStocksCollection = $loanedStocks->getCollection()->map(function($s) {
        $takenBy = 'N/A';
        if ($s->remarks && preg_match('/Taken by:\s*([^)]+)/', $s->remarks, $m)) {
            $takenBy = trim($m[1]);
        }

        return (object)[
            'id'              => $s->id,
            'serial_number'   => $s->serial_number,
            'quantity'        => 1,
            'model'           => $s->product->model    ?? 'N/A',
            'brand'           => $s->product->brand    ?? 'N/A',
            'category'        => $s->product->category ?? 'N/A',
            'requisition_no'  => $s->requisition_no    ?? null,
            'erp_number'      => $s->erp_number        ?? null,
            'remarks'         => $s->remarks           ?? null,
            'loan_start_date' => $s->loan_start_date,
            'loan_period'     => $s->loan_period       ?? 90,
            'updated_at'      => $s->updated_at,
            'taken_by'        => $takenBy,
            'is_bulk'         => false,
        ];
    });

    // 2) Combine & sort
    $combined = $loanedStocksCollection
        ->concat($bulkAsStocks)
        ->sortByDesc(fn($item) => $item->loan_start_date ?? $item->updated_at);

    \Log::debug("Regular loaned items: {$loanedStocksCollection->count()}");
    \Log::debug("Bulk loaned items:    {$bulkAsStocks->count()}");
    \Log::debug("Combined items:       {$combined->count()}");

    // 3) Manual pagination
    $page    = LengthAwarePaginator::resolveCurrentPage('loan_page');
    $perPage = 10;
    $items   = $combined->slice(($page - 1) * $perPage, $perPage)->values();

    $combinedLoanedStocks = new LengthAwarePaginator(
        $items,
        $combined->count(),
        $perPage,
        $page,
        [
            'path'     => request()->url(),
            'query'    => request()->query(),
            'pageName' => 'loan_page',
        ]
    );

    // render view
    return view('stocks.management', [
        'division'               => $division,
        'availableStocks'        => $paginatedAvailable,
        'loanedStocks'           => $loanedStocks,
        'loanedBulkTransactions' => $loanedBulkTransactions,
        'overdueLoans'           => $overdueLoans,
        'overdueCount'           => $overdueCount,
        'bulkList'               => $bulkList,
        'availableBatches'       => $availableBatches,
        'totalBatchQty'          => $totalBatchQty,
        'combinedLoanedStocks'   => $combinedLoanedStocks,
        'sortOption'             => $sort,
    ]);
}



public function storeTransactionIn(Request $request)
{
    $selectedDivision = session('selectedDivision', auth()->user()->division_id);

    // Determine if the selected category is marked as bulk in the DB
    $categoryInput = $request->input('category');
    $isBulk = Category::where('name', $categoryInput)
                      ->value('is_bulk');  // true or false from your categories table

    // Build validation rules dynamically based on whether this is a bulk item
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
    ];

    if ($isBulk) {
        // Bulk items: require quantity, serial is optional
        $rules['quantity']      = ['required', 'integer', 'min:1'];
        $rules['serial_number'] = ['nullable', 'string', 'regex:/^[A-Za-z0-9\-]+$/'];
    } else {
        // Non-bulk: require serial, quantity optional, composite unique by product_code
        $rules['serial_number'] = [
            'required',
            'string',
            'regex:/^[A-Za-z0-9\-]+$/',
            Rule::unique('stocks')->where(function ($query) use ($request) {
                return $query->where('product_code', $request->input('product_code'));
            }),
        ];
        $rules['quantity'] = ['nullable'];
    }

    $validated = $request->validate(
        $rules,
        [
            'serial_number.required' => 'Serial number is required for non-bulk items.',
            'serial_number.unique'   => 'That serial number is already in use for this product.',
            'serial_number.regex'    => 'Serial number format is invalid. Only letters, numbers, and dashes allowed.',
        ]
    );

    // Look up the product
    $product = Product::where('product_code', $validated['product_code'])->firstOrFail();
    if (!$product) {
        return redirect()->back()->withErrors(['product_code' => 'Product not found.']);
    }

    // Determine serial vs quantity
    $serial   = isset($validated['serial_number']) ? $validated['serial_number'] : null;
    $quantity = isset($validated['quantity']) ? $validated['quantity'] : 1;

    // Ensure we use product data where available, falling back to form inputs if needed
    $model = isset($validated['model']) ? $validated['model'] : $product->model;
    $category = isset($validated['category']) ? $validated['category'] : $product->category;
    $brand = isset($validated['brand']) ? $validated['brand'] : $product->brand;

    // Update or create the stock row
    $stock = Stock::updateOrCreate(
        ['requisition_no' => trim($validated['requisition_no'])],
        [
            'serial_number'   => $serial,
            'quantity'        => $quantity,
            'brand'           => $brand,
            'product_code'    => $validated['product_code'],
            'requisition_no'  => isset($validated['requisition_no']) ? $validated['requisition_no'] : null,
            'erp_number'      => isset($validated['erp_number']) ? $validated['erp_number'] : null,
            'model'           => $model,
            'category'        => $category,
            'division_id'     => $selectedDivision,
            'remarks'         => isset($validated['remarks']) ? $validated['remarks'] : null,
            'status'          => 'available',
            'updated_at'      => now(),
            'product_id'      => $product->id,
            'sla_category'    => isset($validated['sla_category']) ? $validated['sla_category'] : null,
            'sla'             => isset($validated['sla']) ? $validated['sla'] : null,
        ]
    );
    $stock->status = 'available';
    $stock->save();

    // --- NEW: if bulk, try to merge into the oldest existing batch first ---
    if ($isBulk) {
        // 1) Look for an existing batch in this division with remaining stock
        $existingBatch = BulkBatch::where('product_id', $product->id)
            ->where('division_id', $selectedDivision)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('received_at', 'asc')
            ->first();

        if ($existingBatch) {
            // preserve SLA if provided
            if (isset($validated['sla_category']) || isset($validated['sla'])) {
                $existingBatch->sla_category = isset($validated['sla_category']) ? $validated['sla_category'] : $existingBatch->sla_category;
                $existingBatch->sla          = isset($validated['sla']) ? $validated['sla'] : $existingBatch->sla;
            }

            $existingBatch->initial_quantity   += $quantity;
            $existingBatch->remaining_quantity += $quantity;
            $existingBatch->received_at         = now();
            $existingBatch->manual_requisition  = isset($validated['requisition_no']) ? $validated['requisition_no'] : $existingBatch->manual_requisition;
            $existingBatch->collection_note     = isset($validated['erp_number']) ? $validated['erp_number'] : $existingBatch->collection_note;
            $existingBatch->remarks             = isset($validated['remarks']) ? $validated['remarks'] : $existingBatch->remarks;
            $existingBatch->save();

            // 3) Log the IN transaction against that existing batch
            StockTransaction::create([
                'stock_id'         => $stock->id,
                'bulk_batch_id'    => $existingBatch->id,
                'serial_number'    => $serial,
                'stock_model'      => $model,
                'brand'            => $brand,
                'product_code'     => $validated['product_code'],
                'requisition_no'   => isset($validated['requisition_no']) ? $validated['requisition_no'] : null,
                'category'         => $category,
                'quantity'         => $quantity,
                'type'             => 'IN',
                'handled_by'       => auth()->id(),
                'remarks'          => isset($validated['remarks']) ? $validated['remarks'] : null,
                'transaction_date' => now(),
                'product_id'       => $stock->product_id,
                'sla_category'     => isset($validated['sla_category']) ? $validated['sla_category'] : null,
                'sla'              => isset($validated['sla']) ? $validated['sla'] : null,
            ]);

            return redirect()
                ->route('stocks.management')
                ->with('success', "Merged new quantity ({$quantity}) into existing batch #{$existingBatch->id}.");
        }

        // 4) No existing batch found → create a new one
        $batch = BulkBatch::create([
            'product_id'         => $product->id,
            'division_id'        => $selectedDivision,
            'initial_quantity'   => $quantity,
            'remaining_quantity' => $quantity,
            'received_at'        => now(),
            'manual_requisition' => isset($validated['requisition_no']) ? $validated['requisition_no'] : null,
            'collection_note'    => isset($validated['erp_number']) ? $validated['erp_number'] : null,
            'ticket_number'      => isset($request->ticket_number) ? $request->ticket_number : null,
            'remarks'            => isset($validated['remarks']) ? $validated['remarks'] : null,
            'sla_category'       => isset($validated['sla_category']) ? $validated['sla_category'] : null,
            'sla'                => isset($validated['sla']) ? $validated['sla'] : null,
        ]);

        // Log the SLA values for debugging
        \Log::debug("Creating new bulk batch with SLA Category: " . (isset($validated['sla_category']) ? $validated['sla_category'] : 'null'));
        
        // Make sure to eager load the product relation before using it in the view
        $batch->load('product');

        StockTransaction::create([
            'stock_id'         => $stock->id,
            'bulk_batch_id'    => $batch->id,
            'serial_number'    => $serial,
            'stock_model'      => $model,
            'brand'            => $brand,
            'product_code'     => $validated['product_code'],
            'requisition_no'   => isset($validated['requisition_no']) ? $validated['requisition_no'] : null,
            'category'         => $category,
            'quantity'         => $quantity,
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => isset($validated['remarks']) ? $validated['remarks'] : null,
            'transaction_date' => now(),
            'product_id'       => $stock->product_id,
            'sla_category'     => isset($validated['sla_category']) ? $validated['sla_category'] : null,
            'sla'              => isset($validated['sla']) ? $validated['sla'] : null,
        ]);

        return redirect()
            ->route('stocks.management')
            ->with('success', 'New bulk batch received and logged.');
    }

    // --- Otherwise, the original non-bulk transaction logic remains unchanged ---
    StockTransaction::create([
        'stock_id'         => $stock->id,
        'serial_number'    => $serial,
        'stock_model'      => $model,
        'brand'            => $brand,
        'product_code'     => $validated['product_code'],
        'requisition_no'   => isset($validated['requisition_no']) ? $validated['requisition_no'] : null,
        'category'         => $category,
        'quantity'         => $quantity,
        'type'             => 'IN',
        'handled_by'       => auth()->id(),
        'remarks'          => isset($validated['remarks']) ? $validated['remarks'] : null,
        'transaction_date' => now(),
        'product_id'       => $stock->product_id,
        'sla_category'     => isset($validated['sla_category']) ? $validated['sla_category'] : null,
        'sla'              => isset($validated['sla']) ? $validated['sla'] : null,
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
        // Get division from request or session; default to first division or 'all'
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
    
        // Build a query by joining products to stocks to access product details.
        $groupedStocks = \App\Models\Stock::join('products', 'stocks.product_id', '=', 'products.id')
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
public function showByCategory(Request $request, $category, $division)
{
    $search = $request->query('search');
    $bulkCategories = config('stocks.bulk_categories', []);
    $isBulkCategory = in_array($category, $bulkCategories, true);
    
    // Build query for stocks with related product data
    $stocksQuery = Stock::with(['division', 'product'])
        ->where('status', 'available')
        ->whereHas('product', function ($q) use ($category) {
            $q->where('category', $category);
        })
        ->orderBy('updated_at', 'desc')
        ->orderBy('id', 'desc');
    
    // Filter by division if specified
    if ($division !== 'all') {
        $divisionModel = Division::find($division);
        $stocksQuery->where('division_id', $division);
    } else {
        $divisionModel = null;
    }
    
    // Apply search filtering
    if ($search) {
        $stocksQuery->where(function ($q) use ($search) {
            $q->where('serial_number', 'LIKE', "%{$search}%")
              ->orWhereHas('product', function ($q) use ($search) {
                  $q->where('model', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%");
              });
        });
    }
    
    $stocks = $stocksQuery->paginate(10);
    
    return view('stocks.show', [
        'stocks'         => $stocks,
        'category'       => $category,
        'division'       => $divisionModel,
        'isBulkCategory' => $isBulkCategory,
    ]);
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

    // 0) Determine whether we're dealing with a bulk‐batch or a single stock
    $stockId = $request->input('stock_id');
    $batchId = $request->input('batch_id');

    if ($batchId) {
        // Bulk flow: load the batch directly
        $batch                = BulkBatch::findOrFail($batchId);
        $entityOwnerDivision  = $batch->division_id;
        $isBulk               = true;
    } else {
        // Single‐serial flow
        $stock                = Stock::findOrFail($stockId);
        $entityOwnerDivision  = $stock->division_id;
        $isBulk               = false;
    }

    // Ensure the item belongs to the selected division
    if ($entityOwnerDivision != $selectedDivision) {
        return redirect()->back()->with('error', 'This item does not belong to the selected division.');
    }

    // Check if this is a forced RETURN
    $forceOperationType = $request->input('force_operation_type');

    // Build our base validation rules
    $rules = [
        'remarks'         => 'required|string',
        'collection_note' => 'nullable|string',
    ];
    // If it's bulk, require quantity; otherwise quantity is optional
    if ($isBulk) {
        $rules['quantity'] = 'required|integer|min:1';
    } else {
        $rules['quantity'] = 'nullable|integer|min:1';
    }

    $validated = $request->validate($rules);

    // --- RETURN branch ---
    if ($forceOperationType === 'RETURN') {
        $model    = $isBulk ? $batch : $stock;
        $quantity = $isBulk ? $validated['quantity'] : 1;

        StockTransaction::create([
            'stock_id'         => $isBulk ? null : $stock->id,
            'product_id'       => $isBulk ? $batch->product_id : $stock->product_id,
            'bulk_batch_id'    => $isBulk ? $batch->id : null,
            'serial_number'    => $isBulk ? null : $stock->serial_number,
            'stock_model'      => $isBulk ? $batch->product->model : $stock->model,
            'brand'            => $isBulk ? $batch->product->brand : $stock->brand,
            'product_code'     => $isBulk ? $batch->product->product_code : $stock->product_code,
            'requisition_no'   => $isBulk ? $batch->manual_requisition : $stock->requisition_no,
            'category'         => $isBulk ? $batch->product->category : $stock->category,
            'collection_note'  => $validated['collection_note'] ?? null,
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'] . ' (Returned to inventory)',
            'transaction_date' => now(),
            'quantity'         => $quantity,
        ]);

        if ($isBulk) {
    $batch->remaining_quantity += $quantity;
    $batch->save();

    // DELETE original LOAN transaction for bulk batch after return
    $originalLoanTransaction = StockTransaction::where('bulk_batch_id', $batch->id)
        ->where('type', 'LOAN')
        ->first();

    if ($originalLoanTransaction) {
        $originalLoanTransaction->delete();
    }
} else {
    $stock->status = 'available';
    $stock->save();

    // DELETE original LOAN transaction for single serial after return
    $originalLoanTransaction = StockTransaction::where('serial_number', $stock->serial_number)
        ->where('type', 'LOAN')
        ->first();

    if ($originalLoanTransaction) {
        $originalLoanTransaction->delete();
    }
}


        return redirect()->route('stocks.management')->with('success', 'Returned successfully.');
    }

    // Otherwise, handle normal OUT or LOAN
    $operationType = $request->input('operation_type');

    // --- BULK LOAN (quantity based) ---
    if ($operationType === 'LOAN' && $isBulk) {
        $loanData = $request->validate([
            'taken_by'    => 'required|string',
            'loan_period' => 'required|integer|min:1|max:90',
            'manual_date' => 'required|date',
        ]);

        $qty      = $validated['quantity'];
        $remarks  = $validated['remarks'] . ' (Taken by: ' . $loanData['taken_by'] . ')';
        $loanStart = Carbon::parse($loanData['manual_date']);
        $dueDate   = $loanStart->copy()->addWeekdays($loanData['loan_period'] - 1);

        StockTransaction::create([
            'stock_id'          => null,
            'product_id'        => $batch->product_id,
            'bulk_batch_id'     => $batch->id,
            'serial_number'     => null,
            'stock_model'       => $batch->product->model,
            'brand'             => $batch->product->brand,
            'product_code'      => $batch->product->product_code,
            'requisition_no'    => $batch->manual_requisition,
            'category'          => $batch->product->category,
            'collection_note'   => $validated['collection_note'] ?? null,
            'type'              => 'LOAN',
            'handled_by'        => auth()->id(),
            'remarks'           => $remarks,
            'transaction_date'  => now(),
            'loan_start_date'   => $loanStart,
            'due_date'          => $dueDate,
            'loan_period'       => $loanData['loan_period'],
            'taken_by'          => $loanData['taken_by'],
            'manual_date'       => $loanData['manual_date'],
            'ticket_number'     => $request->input('ticket_number'),
            'manual_requisition'=> $request->input('manual_requisition'),
            'quantity'          => $qty,
        ]);

        $batch->remaining_quantity -= $qty;
        $batch->save();

        return redirect()->route('stocks.management')->with('success', 'Bulk loan recorded.');
    }

    // --- SINGLE‐SERIAL LOAN ---
    if ($operationType === 'LOAN' && ! $isBulk) {
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

    // --- BULK OUT (quantity based) ---
    if ($operationType === 'OUT' && $isBulk) {
        $qty      = $validated['quantity'];
        $remarks  = $validated['remarks'] . ' (Taken by: ' . $request->input('taken_by') . ')';

        StockTransaction::create([
            'stock_id'         => null,
            'product_id'       => $batch->product->id,
            'bulk_batch_id'    => $batch->id,
            'serial_number'    => null,
            'stock_model'      => $batch->product->model,
            'brand'            => $batch->product->brand,
            'product_code'     => $batch->product->product_code,
            'requisition_no'   => $batch->manual_requisition,
            'category'         => $batch->product->category,
            'collection_note'  => $validated['collection_note'] ?? null,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $remarks,
            'transaction_date' => now(),
            'quantity'         => $qty,
        ]);

        $batch->remaining_quantity -= $qty;
        $batch->save();

        return redirect()->route('stocks.management')->with('success', 'Bulk OUT recorded.');
    }

    // --- SINGLE‐SERIAL OUT ---
    if ($operationType === 'OUT' && ! $isBulk) {
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

    // Fallback
    return redirect()->route('stocks.management')->with('error', 'Invalid operation.');
}

   
    public function loanedStocks(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);

        // Retrieve stocks that are currently loaned
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
    
    // Retrieve the latest transaction matching the serial number
    $transaction = StockTransaction::where('serial_number', $serial)
        ->orderBy('transaction_date', 'desc')
        ->first();
    
    if (!$transaction) {
        return '<div class="alert alert-warning">Transaction not found.</div>';
    }
    
    // Retrieve the stock record by serial number with its product relation
    $stock = Stock::with('product')->where('serial_number', $serial)->first();
    
    // Retrieve the transaction history for the same serial number
    $history = StockTransaction::where('serial_number', $serial)
        ->orderBy('transaction_date', 'desc')
        ->get();
    
    // Extract product details, preferring stock->product data when available
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
    // Add logging to debug parameter issues
    \Log::debug('Stock details request params:', $request->all());
    
    // Try ID first, then serial
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

    // Get the latest transaction for this stock
    $transaction = StockTransaction::where('serial_number', $stock->serial_number)
        ->orderBy('transaction_date', 'desc')
        ->first();

    // Get the transaction history
    $history = StockTransaction::where('serial_number', $stock->serial_number)
        ->orderBy('transaction_date', 'desc')
        ->get();

    // Prepare product details, fallback to transaction data if product relation is missing
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
        'stock_id'        => 'nullable|exists:stocks,id',
        'bulk_batch_id'   => 'nullable|exists:bulk_batches,id',
        'transaction_id'  => 'nullable|exists:stock_transactions,id',
        'borrower_id'     => 'nullable|exists:organizations,id', // Add borrower filter
        'quantity'        => 'nullable|integer|min:1', // Add quantity filter
        'extension_days'  => 'required|integer|min:1|max:90',
    ]);

    $now = Carbon::now();

    if (!empty($data['bulk_batch_id'])) {
        // Use specific loan transaction ID if provided (to target correct bulk loan)
        if (!empty($data['transaction_id'])) {
            $loanTransaction = StockTransaction::findOrFail($data['transaction_id']);
        } else {
            // Build query for the correct loan transaction
            $query = StockTransaction::where('bulk_batch_id', $data['bulk_batch_id'])
                ->where('type', 'LOAN');
            
            // If borrower_id is provided, filter by that specific borrower
            if (!empty($data['borrower_id'])) {
                $query->where('borrower_id', $data['borrower_id']);
            }
            
            // If quantity is provided (to distinguish between different loan amounts)
            if (!empty($data['quantity'])) {
                $query->where('quantity', $data['quantity']);
            }
            
            // Get the specific loan transaction
            $loanTransaction = $query->latest('transaction_date')->firstOrFail();
        }

        $originalStart = Carbon::parse($loanTransaction->loan_start_date);
        $originalPeriod = $loanTransaction->loan_period;
        $originalDue = $originalStart->copy()->addWeekdays($originalPeriod);

        // Calculate new period and due date
        $newPeriod = $originalPeriod + $data['extension_days'];
        $newDueDate = $originalStart->copy()->addWeekdays($newPeriod);

        // Update the transaction
        $loanTransaction->loan_period = $newPeriod;
        $loanTransaction->due_date = $newDueDate;
        $loanTransaction->save();

        \Cache::flush();

        return redirect()->route('stocks.management')
            ->with('success', "Bulk loan period extended by {$data['extension_days']} days.")
            ->with('refresh', true);

    } elseif (!empty($data['stock_id'])) {
        // Existing code for single stock items
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

    // 1) Find the original OUT transaction
    $txn = StockTransaction::findOrFail($data['transaction_id']);

    if ($txn->bulk_batch_id) {
        // --- BULK REVERT ---
        $batch = BulkBatch::findOrFail($txn->bulk_batch_id);

        // 2a) Return quantity to the batch
        $batch->remaining_quantity += $txn->quantity;
        $batch->save();

        // 3a) Log a new IN transaction against that batch, populating all display fields
        StockTransaction::create([
            'bulk_batch_id'    => $batch->id,
            'stock_model'      => $batch->product->model,
            'brand'            => $batch->product->brand,
            'product_code'     => $batch->product->product_code,
            'requisition_no'   => $batch->manual_requisition,
            'category'         => $batch->product->category,
            'product_id'       => $batch->product_id,
            'collection_note'  => $batch->collection_note,
            'quantity'         => $txn->quantity,
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $data['remarks'] . ' (Reverted OUT)',
            'transaction_date' => now(),
        ]);

    } else {
        // --- SINGLE‐SERIAL REVERT ---
        $stock = Stock::where('serial_number', $txn->serial_number)->firstOrFail();

        // 2b) Restore its status
        $stock->status = 'available';
        $stock->save();

        // 3b) Log a new IN transaction for that stock
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
    }

    // 4) Remove the original OUT transaction so it no longer appears
    $txn->delete();

    return back()->with('success', 'OUT operation successfully reverted and inventory has been updated.');
}


/**
 * AJAX: tell me if this serial exists under a different product_code
 */
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
public function returnBulkLoan(Request $request)
{
    try {
        $transactionId = $request->input('transaction_id');
        DB::beginTransaction();
        
        // Find the loan transaction
        $loanTransaction = StockTransaction::findOrFail($transactionId);
        
        if ($loanTransaction->type !== 'LOAN') {
            return redirect()->back()->with('error', 'This transaction is not a loan.');
        }
        
        // Get the batch
        $batch = BulkBatch::findOrFail($loanTransaction->bulk_batch_id);
        
        // Create return transaction
        $returnTransaction = new StockTransaction();
        $returnTransaction->bulk_batch_id = $loanTransaction->bulk_batch_id;
        $returnTransaction->quantity = $loanTransaction->quantity;
        $returnTransaction->type = 'RETURN';
        $returnTransaction->handled_by = auth()->id();
        $returnTransaction->remarks = "Returned bulk items. Original loan remarks: " . ($loanTransaction->remarks ?? 'None');
        $returnTransaction->transaction_date = now();
        $returnTransaction->taken_by = $loanTransaction->taken_by;
        $returnTransaction->save();
        
        // Update batch quantity
        $batch->remaining_quantity += $loanTransaction->quantity;
        $batch->save();

        // Delete the loan transaction so it no longer appears in loan list
        $loanTransaction->delete();
        
        DB::commit();
        
        return redirect()->back()->with('success', 'Bulk items returned successfully.');
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error returning bulk loan: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error returning bulk loan: ' . $e->getMessage());
    }
}

public function createTransactionIn()
{
    $selectedDivision = session('selectedDivision', auth()->user()->division_id);
    $division         = Division::find($selectedDivision);

    // grab active products
    $products   = Product::where('status','active')->orderBy('product_code')->get();

    // grab all categories (now with is_bulk column)
    $categories = Category::orderBy('name')->get();

    // build a lowercase array of your bulk categories for JS
    $bulkList = $categories
                  ->where('is_bulk', true)
                  ->pluck('name')
                  ->map(fn($c) => strtolower($c))
                  ->toArray();

    // Make sure to include products relationship in the eager loading
    $availableBatches = BulkBatch::with('product')
        ->where('division_id', $selectedDivision)
        ->where('remaining_quantity','>',0)
        ->orderBy('received_at','desc')
        ->get();

    return view('stocks.transaction_in', compact(
        'division','products','categories','bulkList','availableBatches'
    )); 
}

    public function stockReport(Request $request)
    {
        // 1. First, get the report type from the query parameter or default to 'available'
        $reportType = $request->query('report_type', 'available');
        
        // 2. Get the division from the query parameter
        $divisionFromQuery = $request->query('division');
        
        // 3. If division is explicitly specified in the query, use it and update session
        if ($divisionFromQuery) {
            $selectedDivision = $divisionFromQuery;
            session(['selectedReportDivision' => $selectedDivision]);
        } 
        // 4. Otherwise, try to get it from session or default to 'all_divisions'
        else {
            $selectedDivision = session('selectedReportDivision', 'all_divisions');
        }
        
        // 5. Debug logging to check the selected division
        \Log::debug('Selected Division in controller: ' . $selectedDivision);
        \Log::debug('Report Type: ' . $reportType);
        
        // 6. Query for available stocks with the selected division
        $availableStocksQuery = Stock::with(['product', 'division'])
            ->where('status', 'available');
    
        // 7. Query for loaned stocks with the selected division
        $loanedStocksQuery = Stock::with(['product', 'division', 'latestTransaction'])
            ->where('status', 'LOAN');
    
        // 8. Apply division filter if not "all divisions"
        if ($selectedDivision !== 'all_divisions') {
            // Check if the division ID is numeric or a string representation
            if (is_numeric($selectedDivision)) {
                $availableStocksQuery->where('division_id', $selectedDivision);
                $loanedStocksQuery->where('division_id', $selectedDivision);
            } else {
                // If not numeric, it might be a division name - look it up first
                $division = Division::where('name', $selectedDivision)->first();
                if ($division) {
                    $availableStocksQuery->where('division_id', $division->id);
                    $loanedStocksQuery->where('division_id', $division->id);
                }
            }
        }
    
        // Execute the queries
        $availableStocks = $availableStocksQuery->get();
        $loanedStocks = $loanedStocksQuery->get();
    
        // Debug the query results
        \Log::debug('Available Stocks Count: ' . $availableStocks->count());
        \Log::debug('Loaned Stocks Count: ' . $loanedStocks->count());
    
        // 9. Return the view with all necessary data
        return view('stocks.stock_report', compact(
            'availableStocks',
            'loanedStocks',
            'selectedDivision',
            'reportType'
        ));
    }
}