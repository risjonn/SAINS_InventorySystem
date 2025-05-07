<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

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
        // Force regular admins to use their assigned division
        if (auth()->user()->role !== 'Super Admin') {
            $selectedDivision = auth()->user()->division_id;
        } else {
            $selectedDivision = $request->query('division', session('selectedDivision', 'all'));
        }

        if ($selectedDivision === 'all') {
            // Summary view for Super Admin: Group by division
            $stocks = Stock::with('product')
                ->where('status', 'available')
                ->select('division_id', DB::raw('COUNT(DISTINCT product_id) as total'))
                ->groupBy('division_id')
                ->get();

            $divisionsSummary = $stocks->map(function ($item) {
                $division = Division::find($item->division_id);
                return [
                    'id'    => $item->division_id,
                    'name'  => $division ? $division->name : 'Unknown',
                    'total' => $item->total,
                ];
            });

            return view('stocks.low_summary', compact('divisionsSummary', 'selectedDivision'));
        } else {
            // Detailed view for a specific division:
            $division = Division::find($selectedDivision);
            $divisions = Division::all();
            $stocks = Stock::with('product')
                ->where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->get();
            
            // Group stocks by product category via the product relationship
            $stocksGrouped = $stocks->groupBy(function ($stock) {
                // Normalize the category string (trim and lowercase) to avoid duplicates due to casing or spaces
                return strtolower(trim(optional($stock->product)->category ?? 'Unknown'));
            });
            
            // Aggregate data per category so each category appears only once
            $aggregatedStocks = $stocksGrouped->map(function ($group, $category) {
                return (object)[
                    'category' => ucfirst($category), // Capitalize for display
                    'total' => $group->count(),
                    'updated_at' => $group->first()->updated_at ?? null, // Optionally, take the first updated_at for tooltip
                ];
            })->values();

            $distinctCategories = $aggregatedStocks->pluck('category')->toArray();
            
            // Prepare chart data arrays based on the aggregated data
            $categoriesLabels = $aggregatedStocks->pluck('category')->toArray();
            $categoriesData = $aggregatedStocks->pluck('total')->toArray();
            
            return view('stocks.low', compact(
                'stocks', 
                'division', 
                'aggregatedStocks', 
                'categoriesLabels', 
                'categoriesData', 
                'distinctCategories',
                'divisions',
                'selectedDivision'
            ));
        }
    }

    public function transactions(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $search           = $request->query('search', '');
        $sortBy           = $request->query('sortBy', 'transaction_date');
        $sortOrder        = $request->query('sortOrder', 'desc');
    
        if ($selectedDivision === 'all') {
            $transactionsQuery = StockTransaction::leftJoin('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                ->leftJoin('users', 'stock_transactions.handled_by', '=', 'users.id')
                ->leftJoin('products', 'stock_transactions.product_id', '=', 'products.id')
                ->leftJoin('divisions', 'stocks.division_id', '=', 'divisions.id')
                ->select(
                    'stock_transactions.*',
                    'users.name as handled_by_name',
                    'stocks.division_id',
                    'divisions.name as division_name',
                    'products.category as product_category',
                    'products.brand as product_brand',
                    'products.model as product_model'
                );
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $transactionsQuery = StockTransaction::leftJoin('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                ->leftJoin('users', 'stock_transactions.handled_by', '=', 'users.id')
                ->leftJoin('products', 'stock_transactions.product_id', '=', 'products.id')
                ->leftJoin('divisions', 'stocks.division_id', '=', 'divisions.id')
                ->where('stocks.division_id', $selectedDivision)
                ->select(
                    'stock_transactions.*',
                    'users.name as handled_by_name',
                    'stocks.division_id',
                    'divisions.name as division_name',
                    'products.category as product_category',
                    'products.brand as product_brand',
                    'products.model as product_model'
                );
        }
    
        // Force filter to be IN, OUT, or LOAN; default to IN if missing/invalid
        $filter = $request->query('filter', 'IN');
        if (! in_array($filter, ['IN', 'OUT', 'LOAN'])) {
            $filter = 'IN';
        }
    
        if ($filter !== 'all') {
            $transactionsQuery->where('stock_transactions.type', $filter);
            if ($filter === 'IN') {
                $transactionsQuery->where('stocks.status', 'available');
            }
        }
    
        // Apply search filter on serial number, stock_model, requisition no, and optionally product fields.
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
    
        $transactionsQuery->orderBy($sortBy, $sortOrder);
        $transactions = $transactionsQuery->paginate(10)->appends($request->all());
    
        return view('stocks.transactions', compact('transactions', 'division'));
    }
    

    public function management(Request $request)
{
    // ←– NEW: read ?division=… or fallback, then persist
    $selectedDivision = $request->query(
        'division',
        session('selectedDivision', auth()->user()->division_id)
    );
    session(['selectedDivision' => $selectedDivision]);
    // –––––––––––––––––––––––––––––––––––––––––––––––––––––––––

    // --- RETURN logic (unchanged) ---
    if ($request->isMethod('post') && $request->input('force_operation_type') === 'RETURN') {
        $stock = Stock::findOrFail($request->input('stock_id'));

        if ($stock->status !== 'LOAN') {
            return redirect()->route('stocks.management')
                             ->with('error', 'This item is not on loan.');
        }

        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => optional($stock->product)->model,
            'category'         => optional($stock->product)->category,
            'brand'            => optional($stock->product)->brand,
            'product_code'     => optional($stock->product)->product_code,
            'product_id'       => $stock->product_id,
            'type'             => 'RETURN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);

        $stock->status = 'IN';
        $stock->save();

        return redirect()->route('stocks.management')
                         ->with('success', 'Stock returned successfully.');
    }

    // --- GET: build the page ---
    $division    = Division::find($selectedDivision);
    $search      = $request->query('search', '');

    // Available
    $availableStocks = Stock::with('product')
        ->where('division_id', $selectedDivision)
        ->where('status','available')
        ->when($search, fn($q) => $q->where('serial_number','LIKE',"%{$search}%")
                                     ->orWhereHas('product', fn($p) => $p->where('model','LIKE',"%{$search}%")))
        ->orderBy('updated_at','desc')
        ->paginate(10, ['*'], 'available_page');

    // Loaned
    $loanedStocks = Stock::with('product')
        ->where('division_id', $selectedDivision)
        ->where('status','LOAN')
        ->when($search, fn($q) => $q->where('serial_number','LIKE',"%{$search}%")
                                     ->orWhereHas('product', fn($p) => $p->where('model','LIKE',"%{$search}%")))
        ->paginate(10, ['*'], 'loan_page');

    // Compute only this division’s overdue loans
    $overdueLoans = collect($loanedStocks->items())->filter(function($stock) {
        $start     = $stock->loan_start_date ?? $stock->updated_at;
        $period    = $stock->loan_period   ?? 90;
        $dueDate   = \Carbon\Carbon::parse($start)->addDays($period);
        return $dueDate->isPast();
    });
    $overdueCount = $overdueLoans->count();

    return view('stocks.management', [
        'division'        => $division,
        'availableStocks' => $availableStocks,
        'loanedStocks'    => $loanedStocks,
        'overdueLoans'    => $overdueLoans,
        'overdueCount'    => $overdueCount,
    ]);
}
    
    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        
        // Retrieve active products from the products table
        $products = \App\Models\Product::where('status', 'active')->orderBy('product_code')->get();
    
        // Optionally, if you still need separate lists for brands and categories (for manual entry),
        // you can extract them from the products collection:
        $brands = $products->pluck('brand')->unique();
        $categories = $products->pluck('category')->unique();
    
        return view('stocks.transaction_in', compact('division', 'products', 'brands', 'categories'));
    }
    
    public function storeTransactionIn(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
    
        $validated = $request->validate([
            'serial_number'   => 'required|string',
            'brand'           => 'nullable|string',
            'product_code'    => 'nullable|string',
            'requisition_no'  => 'nullable|string',
            'erp_number'     => 'nullable|string',
            'model'           => 'required|string',
            'category'        => 'required|string',
            'remarks'         => 'nullable|string',
            'sla_category'    => 'nullable|string',
            'sla'            => 'nullable|string', // <-- if you're adding SLA
        ]);
    
        // Look up the product based on the provided product_code.
        $product = \App\Models\Product::where('product_code', $validated['product_code'])->first();
    
        // Optionally, handle the case where the product isn't found.
        if (!$product) {
            return redirect()->back()->withErrors(['product_code' => 'Product not found.']);
        }
    
        // Update or create Stock record including product_id.
        $stock = Stock::updateOrCreate(
            ['requisition_no' => trim($validated['requisition_no'])],
            [
                'serial_number'   => trim($validated['serial_number']),
                'brand'           => $validated['brand'] ?? null,
                'product_code'    => $validated['product_code'] ?? null,
                'requisition_no'  => $validated['requisition_no'] ?? null,
                'erp_number'      => $validated['erp_number'] ?? null,
                'model'           => $validated['model'],
                'category'        => $validated['category'],
                'division_id'     => $selectedDivision,
                'remarks'         => $validated['remarks'] ?? null,
                'status'          => 'available',
                'updated_at'      => now(),
                'product_id'      => $product->id, // Set the related product_id here.
                'sla_category'    => $validated['sla_category'] ?? null,
                'sla'             => $validated['sla'] ?? null,      
            ]
        );
    
        $stock->status = 'available';
        $stock->save();
    
        // Create Stock Transaction record.
        StockTransaction::create([
            'serial_number'    => trim($validated['serial_number']),
            'stock_model'      => $validated['model'],
            'brand'            => $validated['brand'] ?? null,
            'product_code'     => $validated['product_code'] ?? null,
            'requisition_no'   => $validated['requisition_no'] ?? null,
            'category'         => $validated['category'],
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
            'product_id' => $stock->product_id
        ]);
    
        return redirect()->route('stocks.management')->with([
            'success'  => 'Stock Added In Inventory Successfully.',
            'resource' => 'stock'
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
            ->whereHas('product', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->where('stocks.status', 'available')  // ← FIX applied here
            ->when($divisionId !== 'all', function ($query) use ($divisionId) {
                $query->where('stocks.division_id', $divisionId);
            })
            ->when($search, function ($query, $search) use ($divisionId) {
                $query->where(function ($q) use ($search, $divisionId) {
                    $q->where('stocks.serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('stocks.requisition_no', 'LIKE', "%{$search}%")
                      ->orWhereHas('product', function ($q2) use ($search) {
                          $q2->where('products.model', 'LIKE', "%{$search}%")
                             ->orWhere('products.category', 'LIKE', "%{$search}%");
                      });
    
                    if ($divisionId === 'all') {
                        $q->orWhereHas('division', function($q3) use ($search) {
                            $q3->where('divisions.name', 'LIKE', "%{$search}%");
                        });
                    }
                });
            });
    
        switch ($sort) {
            case 'model_asc':
                $stocksQuery->join('products', 'stocks.product_id', '=', 'products.id')
                            ->orderBy('products.model', 'asc');
                break;
            case 'model_desc':
                $stocksQuery->join('products', 'stocks.product_id', '=', 'products.id')
                            ->orderBy('products.model', 'desc');
                break;
            case 'updated_at_asc':
                $stocksQuery->orderBy('stocks.updated_at', 'asc')->orderBy('stocks.id', 'asc');
                break;
            case 'updated_at_desc':
            default:
                $stocksQuery->orderBy('stocks.updated_at', 'desc')->orderBy('stocks.id', 'desc');
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
    
        // Build a query for stocks with related product data.
        $stocksQuery = Stock::with(['division', 'product'])
            ->where('status', 'available')
            ->whereHas('product', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc');
    
        // If a specific division is provided, filter by division_id.
        if ($division !== 'all') {
            $divisionModel = Division::find($division);
            $stocksQuery->where('division_id', $division);
        } else {
            $divisionModel = null;
        }
    
        // Apply search filtering on serial_number and product fields.
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
            'stocks'   => $stocks,
            'category' => $category,
            'division' => $divisionModel,
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
    $stockId = $request->input('stock_id');
    $stock = Stock::findOrFail($stockId);

    // Ensure the stock belongs to the selected division
    if ($stock->division_id != $selectedDivision) {
        return redirect()->back()->with('error', 'This stock does not belong to the selected division.');
    }

    // Check if this is a forced RETURN
    $forceOperationType = $request->input('force_operation_type');

    // Common validation for remarks
    $validated = $request->validate([
        'remarks' => 'required|string',
    ]);

    // If forced to RETURN (unchanged code)
    if ($forceOperationType === 'RETURN') {
        if (!in_array($stock->status, ['LOAN', 'OUT'])) {
            return redirect()->route('stocks.management')
                ->with('error', 'This item is neither on loan nor out.');
        }

        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,
            'product_code'     => $stock->product_code,
            'requisition_no'   => $stock->requisition_no,
            'category'         => $stock->category,
            'type'             => 'IN', // Using "IN" instead of "RETURN"
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'] . ' (Returned to inventory)',
            'transaction_date' => now(),
            'product_id'       => $stock->product_id,
            'manual_date'        => $request->input('manual_date'),
            'delivery_note'      => $request->input('delivery_note'),
            'document_number'    => $request->input('document_number'),
            'name'               => $request->input('name'),
            'ticket_number'      => $request->input('ticket_number'),
            'manual_requisition' => $request->input('manual_requisition'),
        ]);

        $stock->status  = 'available';
        $stock->remarks = $validated['remarks'];
        $stock->save();

        return redirect()->route('stocks.management')->with([
            'success'  => 'Stock returned successfully.',
            'resource' => 'stock'
        ]);
    }

    // Otherwise, handle normal OUT or LOAN operation
    $operationType = $request->input('operation_type');

    if ($operationType === 'LOAN') {
        // LOAN branch code (unchanged)
        $loanData = $request->validate([
            'taken_by'    => 'required|string',
            'loan_period' => 'required|integer|min:1|max:90',
            'manual_date' => 'required|date',
        ]);

        $extendedRemarks = $validated['remarks'] . ' (Taken by: ' . $request->input('taken_by') . ')';
        $loanStartDate = \Carbon\Carbon::parse($loanData['manual_date']);
        $dueDate = $loanStartDate->copy()->addDays($loanData['loan_period']);

        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,
            'product_code'     => $stock->product_code,
            'requisition_no'   => $stock->requisition_no,
            'category'         => $stock->category,
            'type'             => 'LOAN',
            'handled_by'       => auth()->id(),
            'remarks'          => $extendedRemarks,
            'transaction_date' => now(),
            'loan_start_date'  => $loanStartDate,
            'due_date'         => $dueDate,
            'loan_period'      => $loanData['loan_period'],
            'product_id'       => $stock->product_id,
            'manual_date'      => $request->input('manual_date'),
            'delivery_note'    => $request->input('delivery_note'),
            'document_number'  => $request->input('document_number'),
            'name'             => $request->input('name'),
            'taken_by'         => $request->input('taken_by'),
            'ticket_number'      => $request->input('ticket_number'),
            'manual_requisition' => $request->input('manual_requisition'),
        ]);

        $stock->status = 'LOAN';
        $stock->remarks = $extendedRemarks;
        $stock->loan_period = $loanData['loan_period'];
        $stock->loan_start_date = $loanStartDate;
        $stock->save();

        return redirect()->route('stocks.management')->with([
            'success'  => 'Loan transaction recorded and stock marked as loaned.',
            'resource' => 'stock'
        ]);
    } else {
        // Updated OUT branch: validate additional fields
        $request->validate([
            'taken_by' => 'required|string',
            // Set additional fields as optional:
            'manual_date' => 'nullable|date',
            'delivery_note' => 'nullable|string',
            'document_number' => 'nullable|string',
            'name' => 'nullable|string',
            'ticket_number' => 'nullable|string',
            'manual_requisition' => 'nullable|string',
        ]);

        $takenBy = $request->input('taken_by');
        $extendedRemarks = !empty($takenBy)
            ? $validated['remarks'] . ' (Taken by: ' . $takenBy . ')'
            : $validated['remarks'];

        // Create OUT transaction including extra fields
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,
            'product_code'     => $stock->product_code,
            'requisition_no'   => $stock->requisition_no,
            'category'         => $stock->category,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $extendedRemarks,
            'transaction_date' => now(),
            'product_id'       => $stock->product_id,
            'manual_date'        => $request->input('manual_date'),
            'delivery_note'      => $request->input('delivery_note'),
            'document_number'    => $request->input('document_number'),
            'name'               => $request->input('name'),
            'ticket_number'      => $request->input('ticket_number'),
            'manual_requisition' => $request->input('manual_requisition'),
        ]);

        // Update stock status to OUT and update remarks
        $stock->status = 'OUT';
        $stock->remarks = $extendedRemarks;
        $stock->save();

        return redirect()->route('stocks.management')->with([
            'success'  => 'OUT transaction recorded and stock removed.',
            'resource' => 'stock'
        ]);
    }
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
    
        // Retrieve the transaction history for the same serial number
        $history = StockTransaction::where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->get();
    
        // Retrieve the stock record by serial number
        $stock = Stock::where('serial_number', $serial)->first();
    
        return view('stocks.partials.req_modal', compact('transaction', 'stock', 'history'));
    }

    public function getStockDetails(Request $request)
    {
        $serial = $request->input('serial');
        if (!$serial) {
            return response()->json(['error' => 'Invalid parameters.'], 400);
        }
        
        // Get the stock with its product relationship
        $stock = Stock::with('product')->where('serial_number', $serial)->first();
        
        if (!$stock) {
            return '<div class="alert alert-warning">Stock not found.</div>';
        }
        
        // Get the latest transaction
        $transaction = StockTransaction::where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->first();
        
        // Use product data as fallback
        $brand = $transaction->brand ?? $stock->product->brand ?? 'N/A';
        $model = $transaction->stock_model ?? $stock->product->model ?? 'N/A';
        $category = $transaction->category ?? $stock->product->category ?? 'N/A';
        $productCode = $transaction->product_code ?? $stock->product->product_code ?? 'N/A';
        
        return view('stocks.partials.stock_details_card', compact('stock', 'transaction', 'brand', 'model', 'category', 'productCode'));
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