<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB; // Import DB facade

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
        $stocks = Stock::with('division')
                        ->when($category, function ($query, $category) {
                            return $query->where('category', $category);
                        })
                        ->paginate(10);
        return view('stocks.index', compact('stocks'));
    }

    public function lowStock(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        if ($selectedDivision === 'all') {
            $stocks = Stock::where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as total'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 5')
                ->get();
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $stocks = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as total'))
                ->groupBy('category')
                ->havingRaw('COUNT(*) < 5')
                ->get();
        }
        return view('stocks.low', compact('stocks', 'division'));
    }

    public function transactions(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $search = $request->query('search', '');
        $sortBy = $request->query('sortBy', 'transaction_date');
        $sortOrder = $request->query('sortOrder', 'desc');

        if ($selectedDivision === 'all') {
            $transactionsQuery = StockTransaction::join('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                ->join('users', 'stock_transactions.handled_by', '=', 'users.id')
                ->select('stock_transactions.*', 'users.name as handled_by_name', 'stocks.division_id');
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $transactionsQuery = StockTransaction::join('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                ->join('users', 'stock_transactions.handled_by', '=', 'users.id')
                ->where('stocks.division_id', $selectedDivision)
                ->select('stock_transactions.*', 'users.name as handled_by_name', 'stocks.division_id');
        }

        // Force filter to be IN, OUT, or LOAN; default to IN if missing/invalid
        $filter = $request->query('filter', 'IN');
        if (! in_array($filter, ['IN', 'OUT', 'LOAN'])) {
            $filter = 'IN';
        }

        if ($filter !== 'all') {
            $transactionsQuery->where('stock_transactions.type', $filter);
        }

        // Apply search filter
        if ($search) {
            $transactionsQuery->where(function($q) use ($search, $selectedDivision) {
                $q->where('stock_transactions.serial_number', 'LIKE', $search.'%')
                  ->orWhere('stock_transactions.stock_model', 'LIKE', $search.'%');
                if ($selectedDivision === 'all') {
                    $q->orWhereHas('stock.division', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', $search.'%');
                    });
                }
            });
        }

        $transactionsQuery->orderBy($sortBy, $sortOrder);
        $transactions = $transactionsQuery->paginate(10)->appends($request->all());
        return view('stocks.transactions', compact('transactions', 'division'));
    }

    public function management(Request $request)
    {
        // If this is a POST request for returning a loaned item
        if ($request->isMethod('post') && $request->input('force_operation_type') === 'RETURN') {
            $stockId = $request->input('stock_id');
            $stock = Stock::findOrFail($stockId);

            // Verify that the stock is currently on loan
            if ($stock->status !== 'LOAN') {
                return redirect()->route('stocks.management')->with('error', 'This item is not on loan.');
            }

            $validated = $request->validate([
                'remarks' => 'required|string',
            ]);

            // Create a transaction record of type "RETURN"
            StockTransaction::create([
                'serial_number'    => $stock->serial_number,
                'stock_model'      => $stock->model,
                'category'         => $stock->category,
                'type'             => 'RETURN', // or "IN" if you prefer
                'handled_by'       => auth()->id(),
                'remarks'          => $validated['remarks'],
                'transaction_date' => now(),
            ]);

            // Update the stock status back to "IN" (or 'available' as needed)
            $stock->status = 'IN';
            $stock->save();

            return redirect()->route('stocks.management')->with('success', 'Stock returned successfully.');
        }

        // Otherwise, process the GET request to display management page
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        $search = $request->query('search');

        // Available stocks (using a unique pagination parameter)
        $availableStocks = Stock::with('division')
            ->where('division_id', $selectedDivision)
            ->where('status', 'available')
            ->when($search, function ($query, $search) {
                return $query->where('serial_number', 'LIKE', "%{$search}%")
                             ->orWhere('model', 'LIKE', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc')  // Add this line
            ->paginate(10, ['*'], 'available_page');

        // Loaned stocks (using a different pagination parameter)
        $loanedStocks = Stock::with('division')
            ->where('division_id', $selectedDivision)
            ->where('status', 'LOAN')
            ->when($search, function ($query, $search) {
                return $query->where('serial_number', 'LIKE', "%{$search}%")
                             ->orWhere('model', 'LIKE', "%{$search}%");
            })
            ->paginate(10, ['*'], 'loan_page');

        return view('stocks.management', compact('availableStocks', 'loanedStocks', 'division'));
    }

    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        // Fetch active brands and categories sorted by name
        $brands = \App\Models\Brand::where('active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::where('active', true)->orderBy('name')->get();

        return view('stocks.transaction_in', compact('division', 'brands', 'categories'));
    }

    public function storeTransactionIn(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        // 1) Expand validation to include new fields (brand, product_code, requisition_no, etc.)
        //    Note: Adjust 'required' vs. 'nullable' as needed for your scenario.
        $validated = $request->validate([
            'serial_number'   => 'required',
            'brand'           => 'nullable|string',
            'product_code'    => 'nullable|string',
            'requisition_no'  => 'nullable|string',
            'model'           => 'required|string',
            'category'        => 'required|string',
            'remarks'         => 'nullable|string',
        ]);

        // 2) Update or create the Stock record with new fields
        $stock = Stock::updateOrCreate(
            ['serial_number' => trim($validated['serial_number'])],
            [
                'brand'          => $validated['brand']          ?? null,
                'product_code'   => $validated['product_code']   ?? null,
                'requisition_no' => $validated['requisition_no'] ?? null,
                'model'          => $validated['model'],
                'category'       => $validated['category'],
                'division_id'    => $selectedDivision,
                'remarks'        => $validated['remarks'] ?? null,
                // Optionally set status to 'available' so it appears under "Available Stocks"
                'status'         => 'available',
                'updated_at'     => now(),

            ]
        );

        // Ensure the stock is marked as available (this line is optional if updateOrCreate sets it correctly)
        $stock->status = 'available';
        $stock->save();

        // 3) Create a matching transaction record with new fields
        StockTransaction::create([
            'serial_number'    => trim($validated['serial_number']),
            'stock_model'      => $validated['model'],
            'brand'            => $validated['brand']          ?? null,
            'product_code'     => $validated['product_code']   ?? null,
            'requisition_no'   => $validated['requisition_no'] ?? null,
            'category'         => $validated['category'],
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);

        return redirect()->route('stocks.management')->with('success', 'IN transaction recorded.');
    }

    public function createTransactionOut()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        return view('stocks.transaction_out', compact('division'));
    }

    public function outOperation(Request $request, Stock $stock)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        if ($stock->division_id != $selectedDivision) {
            return redirect()->back()->with('error', 'This stock does not belong to the selected division.');
        }

        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'category'         => $stock->category,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);

        $stock->status = 'OUT';
        $stock->save();

        return redirect()->route('stocks.management')->with('success', 'OUT transaction recorded and stock removed.');
    }

    public function create()
    {
        $divisions = Division::all();
        return view('stocks.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|unique:stocks',
            'model'         => 'required',
            'category'      => 'required',
            'division_id'   => 'required|exists:divisions,id',
            'remarks'       => 'nullable'
        ]);

        Stock::create($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(Stock $stock)
    {
        $divisions = Division::all();
        return view('stocks.edit', compact('stock', 'divisions'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'model'         => 'required',
            'category'      => 'required',
            'division_id'   => 'required|exists:divisions,id',
            'remarks'       => 'nullable'
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }

    public function show(Request $request, $category)
    {
        $divisionId = $request->query('division', 'all');
        $search = $request->query('search');
    
        // 1) Read the 'sort' param from the query, default to 'updated_at_desc'
        $sort = $request->query('sort', 'updated_at_desc');
    
        if ($divisionId !== 'all') {
            $division = Division::find($divisionId);
        } else {
            $division = null;
        }
    
        // 2) Build the query for stocks
        $stocks = Stock::where('category', $category)
                        ->where('status', 'available')
                        ->when($divisionId !== 'all', function ($query) use ($divisionId) {
                            return $query->where('division_id', $divisionId);
                        })
                        ->when($search, function ($query, $search) {
                            return $query->where(function ($q) use ($search) {
                                $q->where('serial_number', 'LIKE', "%{$search}%")
                                  ->orWhere('model', 'LIKE', "%{$search}%")
                                  ->orWhere('category', 'LIKE', "%{$search}%");
                            });
                        })
    
                        // 3) Add server-side sorting based on $sort
                        ->when($sort === 'updated_at_asc', function($query){
                            return $query->orderBy('updated_at', 'asc');
                        })
                        ->when($sort === 'updated_at_desc', function($query){
                            return $query->orderBy('updated_at', 'desc');
                        })
                        ->when($sort === 'model_asc', function($query){
                            return $query->orderBy('model', 'asc');
                        })
                        ->when($sort === 'model_desc', function($query){
                            return $query->orderBy('model', 'desc');
                        })
    
                        // Finally paginate
                        ->paginate(10);
    
        return view('stocks.show', compact('stocks', 'category', 'division'));
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

        // Group stocks by category and count the total for each group.
        $groupedStocks = Stock::select('category', DB::raw('COUNT(*) as total'))
            ->where('status', 'available')
            ->when($divisionId !== 'all', function ($query) use ($divisionId) {
                return $query->where('division_id', $divisionId);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('model', 'LIKE', "%{$search}%")
                      ->orWhere('category', 'LIKE', "%{$search}%");
                });
            })
            ->groupBy('category')
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
        
        if ($division === 'all') {
            // Retrieve stocks by category across all divisions.
            $stocks = Stock::where('category', $category)
                           ->where('status', 'available')
                           ->when($search, function ($query, $search) {
                               $query->where(function ($q) use ($search) {
                                   $q->where('serial_number', 'LIKE', "%{$search}%")
                                     ->orWhere('model', 'LIKE', "%{$search}%")
                                     ->orWhere('category', 'LIKE', "%{$search}%");
                               });
                           })
                           ->paginate(10);
            $divisionModel = null;
        } else {
            $stocks = Stock::where('category', $category)
                           ->where('division_id', $division)
                           ->where('status', 'available')
                           ->when($search, function ($query, $search) {
                               $query->where(function ($q) use ($search) {
                                   $q->where('serial_number', 'LIKE', "%{$search}%")
                                     ->orWhere('model', 'LIKE', "%{$search}%")
                                     ->orWhere('category', 'LIKE', "%{$search}%");
                               });
                           })
                           ->paginate(10);
            $divisionModel = Division::find($division);
        }
    
        return view('stocks.show', [
            'stocks' => $stocks,
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
            $query->where(function($query) use ($q) {
                $query->where('serial_number', 'LIKE', "%{$q}%")
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

    /**
     * Extended storeOutOrLoan to handle "RETURN"
     */
    public function storeOutOrLoan(Request $request)
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

    // If forced to RETURN
    if ($forceOperationType === 'RETURN') {
        // Ensure current status is LOAN
        if ($stock->status !== 'LOAN') {
            return redirect()->route('stocks.management')->with('error', 'This item is not on loan.');
        }

        // Record transaction as "IN" to avoid DB error if type is an ENUM
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,             // Added brand
            'product_code'     => $stock->product_code,      // Added product code
            'requisition_no'   => $stock->requisition_no,    // Added requisition no.
            'category'         => $stock->category,
            'type'             => 'IN', // Using "IN" instead of "RETURN"
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'] . ' (Returned to inventory)',
            'transaction_date' => now(),
        ]);

        // Update stock status and update remarks
        $stock->status = 'available';
        $stock->remarks = $validated['remarks'];
        $stock->save();

        return redirect()->route('stocks.management')->with('success', 'Stock returned successfully.');
    }

    // Otherwise, handle normal OUT or LOAN operation
    $operationType = $request->input('operation_type');

    if ($operationType === 'LOAN') {
        // Additional validation for "taken_by"
        $request->validate([
            'taken_by' => 'required|string',
        ]);

        // Combine "taken_by" info in remarks so you can see who physically took it
        $extendedRemarks = $validated['remarks'] . ' (Taken by: ' . $request->input('taken_by') . ')';

        // Create LOAN transaction with brand included
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,            // Added brand
            'product_code'     => $stock->product_code,     // Added product code
            'requisition_no'   => $stock->requisition_no,   // Added requisition no.
            'category'         => $stock->category,
            'type'             => 'LOAN',
            'handled_by'       => auth()->id(),
            'remarks'          => $extendedRemarks,
            'transaction_date' => now(),
        ]);

        // Update stock status to LOAN
        $stock->status = 'LOAN';
        $stock->save();

        return redirect()->route('stocks.management')->with('success', 'Loan transaction recorded and stock marked as loaned.');
    } else {
        // -----------------------------
        // ADD THESE LINES FOR "OUT":
        // -----------------------------
        // If user also wants to fill "taken_by" for OUT, we can optionally validate it:
        // (Remove 'required' if you want it optional.)
        $request->validate([
            'taken_by' => 'nullable|string',  // or 'required|string' if you want to force them
        ]);

        // If user typed a 'taken_by', append it to remarks:
        $takenBy = $request->input('taken_by');
        if (!empty($takenBy)) {
            $extendedRemarks = $validated['remarks'] . ' (Taken by: ' . $takenBy . ')';
        } else {
            $extendedRemarks = $validated['remarks'];
        }
        // -----------------------------
        // The rest is unchanged
        // -----------------------------
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'brand'            => $stock->brand,            // Added brand
            'product_code'     => $stock->product_code,     // Added product code
            'requisition_no'   => $stock->requisition_no,   // Added requisition no.
            'category'         => $stock->category,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $extendedRemarks,
            'transaction_date' => now(),
        ]);

        // Update stock status to OUT
        $stock->status = 'OUT';
        $stock->save();

        return redirect()->route('stocks.management')->with('success', 'OUT transaction recorded and stock removed.');
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
}
