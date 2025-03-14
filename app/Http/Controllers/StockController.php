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
        
        $stocks = Stock::with('division')
            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->orderBy('updated_at', 'desc') // Ensure proper sorting
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
                ->get();
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $stocks = Stock::where('division_id', $selectedDivision)
                ->where('status', 'available')
                ->select('category', DB::raw('COUNT(*) as total'))
                ->groupBy('category')
                ->get();
        }
        
        $categoriesLabels = $stocks->pluck('category')->toArray();
        $categoriesData = $stocks->pluck('total')->toArray();
        
        return view('stocks.low', compact('stocks', 'division', 'categoriesLabels', 'categoriesData'));
    }

    public function transactions(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $search           = $request->query('search', '');
        $sortBy           = $request->query('sortBy', 'transaction_date');
        $sortOrder        = $request->query('sortOrder', 'desc');

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
            if ($filter === 'IN') {
                $transactionsQuery->where('stocks.status', 'available');
            }
        }

        // Apply search filter
        if ($search) {
            $transactionsQuery->where(function($q) use ($search, $selectedDivision) {
                $q->where('stock_transactions.serial_number', 'LIKE', $search.'%')
                  ->orWhere('stock_transactions.stock_model', 'LIKE', $search.'%')
                  ->orWhere('stock_transactions.requisition_no', 'LIKE', "%{$search}%");
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
            ->orderBy('updated_at', 'desc')  // Sort by newest updated first
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
        $validated = $request->validate([
            'serial_number'   => 'required|string', // Ensure serial_number is provided
            'brand'           => 'nullable|string',
            'product_code'    => 'nullable|string',
            'requisition_no'  => 'nullable|string',
            'model'           => 'required|string',
            'category'        => 'required|string',
            'remarks'         => 'nullable|string',
        ]);

        // 2) Update or create the Stock record with new fields
        $stock = Stock::updateOrCreate(
            ['requisition_no' => trim($validated['requisition_no'])],
            [
                'serial_number'   => trim($validated['serial_number']), // Ensure serial_number is saved
                'brand'           => $validated['brand'] ?? null,
                'product_code'    => $validated['product_code'] ?? null,
                'requisition_no'  => $validated['requisition_no'] ?? null,
                'model'           => $validated['model'],
                'category'        => $validated['category'],
                'division_id'     => $selectedDivision,
                'remarks'         => $validated['remarks'] ?? null,
                'status'          => 'available',
                'updated_at'      => now(),
            ]
        );

        // Ensure the stock is marked as available (this line is optional if updateOrCreate sets it correctly)
        $stock->status = 'available';
        $stock->save();

        // 3) Create a matching transaction record with new fields
        StockTransaction::create([
            'serial_number'   => trim($validated['serial_number']),
            'stock_model'     => $validated['model'],
            'brand'           => $validated['brand'] ?? null,
            'product_code'    => $validated['product_code'] ?? null,
            'requisition_no'  => $validated['requisition_no'] ?? null,
            'category'        => $validated['category'],
            'type'            => 'IN', // Or use 'IN' based on your system's logic
            'handled_by'      => auth()->id(),
            'remarks'         => $validated['remarks'],
            'transaction_date'=> now(),
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
        $divisionId = $request->query('division', 'all');  // Get division from query
        $search     = $request->query('search');           // Get search parameter
        // Use the default value if sort is not provided
        $sort = $request->query('sort', 'updated_at_desc');
    
        // Determine the Division model or null if 'all'
        if ($divisionId !== 'all') {
            $division = Division::find($divisionId);
        } else {
            $division = null;
        }
    
        // Build the query to filter stocks by category, division, and search
        // Added: ->with('division') to allow searching division name
        $stocksQuery = Stock::with('division')
            ->where('category', $category)
            ->where('status', 'available')  // Only available stocks
            ->when($divisionId !== 'all', function ($query) use ($divisionId) {
                return $query->where('division_id', $divisionId);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('model', 'LIKE', "%{$search}%")
                      ->orWhere('category', 'LIKE', "%{$search}%")
                      ->orWhere('requisition_no', 'LIKE', "%{$search}%")
                      // NEW: Search by division name as well
                      ->orWhereHas('division', function($q2) use ($search) {
                          $q2->where('name', 'LIKE', "%{$search}%");
                      });
                });
            });
    
        // Apply sorting based on the $sort value
        switch ($sort) {
            case 'model_asc':
                $stocksQuery->orderBy('model', 'asc');
                break;
            case 'model_desc':
                $stocksQuery->orderBy('model', 'desc');
                break;
            case 'updated_at_asc':
                // Secondary sort by 'id' for stability
                $stocksQuery->orderBy('updated_at', 'asc')->orderBy('id', 'asc');
                break;
            case 'updated_at_desc':
            default:
                $stocksQuery->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                break;
        }
    
        // Paginate the results
        $stocks = $stocksQuery->paginate(10);
    
        // Set division name for display
        $divisionName = $division ? $division->name : 'All Divisions';
    
        // Pass the sort value to the view as well (if needed)
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
                ->orderBy('updated_at', 'desc') // newest first
                ->orderBy('id', 'desc')         // secondary ordering for stability
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
            $divisionModel = Division::find($division);
            $stocks = Stock::with('division') // eager load division
                ->where('category', $category)
                ->where('division_id', $division)
                ->where('status', 'available')
                ->orderBy('updated_at', 'desc') // newest first
                ->orderBy('id', 'desc')         // secondary ordering for stability
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('serial_number', 'LIKE', "%{$search}%")
                          ->orWhere('model', 'LIKE', "%{$search}%")
                          ->orWhere('category', 'LIKE', "%{$search}%");
                    });
                })
                ->paginate(10);
        }
        
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
                'brand'            => $stock->brand,
                'product_code'     => $stock->product_code,
                'requisition_no'   => $stock->requisition_no,
                'category'         => $stock->category,
                'type'             => 'IN', // Using "IN" instead of "RETURN"
                'handled_by'       => auth()->id(),
                'remarks'          => $validated['remarks'] . ' (Returned to inventory)',
                'transaction_date' => now(),
            ]);

            // Update stock status and remarks
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
            // Additional validation for "taken_by"
            $request->validate([
                'taken_by' => 'required|string',
            ]);

            // Combine "taken_by" info in remarks so you can see who physically took it
            $extendedRemarks = $validated['remarks'] . ' (Taken by: ' . $request->input('taken_by') . ')';

            // Create LOAN transaction
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
            ]);

            // Update stock status to LOAN
            $stock->status = 'LOAN';
            $stock->save();

            return redirect()->route('stocks.management')->with([
                'success'  => 'Loan transaction recorded and stock marked as loaned.',
                'resource' => 'stock'
            ]);
        } else {
            // This covers OUT or any other type you may have
            $request->validate([
                'taken_by' => 'required|string',
            ]);

            // If user typed a 'taken_by', append it to remarks
            $takenBy = $request->input('taken_by');
            $extendedRemarks = !empty($takenBy)
                ? $validated['remarks'] . ' (Taken by: ' . $takenBy . ')'
                : $validated['remarks'];

            // Create OUT transaction
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
            ]);

            // Update stock status to OUT
            $stock->status = 'OUT';
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
        $requisitionNo = $request->input('requisition_no');
        $serial        = $request->input('serial');

        if (!$requisitionNo || !$serial) {
            return response()->json(['error' => 'Invalid parameters.'], 400);
        }

        // Retrieve the latest transaction matching both requisition_no & serial_number
        $transaction = StockTransaction::where('requisition_no', $requisitionNo)
            ->where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        // Retrieve ONLY the history for the same requisition_no & serial_number
        $history = StockTransaction::where('requisition_no', $requisitionNo)
            ->where('serial_number', $serial)
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Optionally load the stock record by both requisition_no & serial
        $stock = Stock::where('requisition_no', $requisitionNo)
            ->where('serial_number', $serial)
            ->first();

        return view('stocks.partials.transaction_details', compact('transaction', 'stock', 'history'));
    }
}
