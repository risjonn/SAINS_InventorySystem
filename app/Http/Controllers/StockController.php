<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure the user is authenticated

        // Manually check if the user is an admin
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'Admin') { // Check if the user's role is 'Admin'
                abort(403, 'Unauthorized Action');
            }
            return $next($request);
        })->only(['edit', 'update', 'destroy']); // Apply this check only to edit, update, destroy
    }

    public function index(Request $request)
    {
        $category = $request->get('category'); // Get the selected category from the request

        $stocks = Stock::with('division')
                        ->when($category, function ($query, $category) {
                            return $query->where('category', $category); // Filter based on selected category
                        })
                        ->paginate(10); // Paginate the results (10 per page)

        return view('stocks.index', compact('stocks'));
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

        // Apply filter if provided (e.g., type filter)
        $filter = $request->query('filter', 'all');
        if ($filter !== 'all') {
            $transactionsQuery->where('stock_transactions.type', $filter);
        }

        // Apply search filter across Serial Number, stock_model, and (if all divisions) Division name.
        if ($search) {
            $transactionsQuery->where(function($q) use ($search, $selectedDivision) {
                $q->where('stock_transactions.serial_number', 'LIKE', $search.'%')
                  ->orWhere('stock_transactions.stock_model', 'LIKE', $search.'%');
                if ($selectedDivision === 'all') {
                    // Join Division table to search by division name
                    $q->orWhereHas('stock.division', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', $search.'%');
                    });
                }
            });
        }

        // Apply sorting
        $transactionsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results (10 per page) and preserve query parameters.
        $transactions = $transactionsQuery->paginate(10)->appends($request->all());

        return view('stocks.transactions', compact('transactions', 'division'));
    }

    public function management(Request $request)
    {
        // Retrieve the selected division from session (or default to user's division)
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
    
        // Get the search query from the request
        $search = $request->query('search');
    
        // Retrieve stocks that belong to the selected division with search functionality
        // Use pagination instead of get()
        $stocks = Stock::with('division')
                        ->where('division_id', $selectedDivision)
                        ->where('status', 'available')
                        ->when($search, function ($query, $search) {
                            return $query->where('serial_number', 'LIKE', "%{$search}%")
                                         ->orWhere('model', 'LIKE', "%{$search}%");
                        })
                        ->paginate(10);  // Paginate the results with 10 per page
    
        // Return the view with stocks and division data
        return view('stocks.management', compact('stocks', 'division'));
    }
    
    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        return view('stocks.transaction_in', compact('division'));
    }
    
    public function storeTransactionIn(Request $request)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
    
        $validated = $request->validate([
            'serial_number' => 'required',
            'model'         => 'required',
            'category'      => 'required',
            'remarks'       => 'nullable',
        ]);
    
        // Create or update the stock record.
        $stock = Stock::where('serial_number', $validated['serial_number'])->first();
        if (!$stock) {
            // Create new stock and assign the selected division.
            $stock = Stock::create([
                'serial_number' => $validated['serial_number'],
                'model'         => $validated['model'],
                'category'      => $validated['category'],
                'division_id'   => $selectedDivision,
                'remarks'       => $validated['remarks'],
            ]);
        } else {
            // Optionally update the existing stock record.
            $stock->update([
                'model'    => $validated['model'],
                'category' => $validated['category'],
                'remarks'  => $validated['remarks'],
            ]);
        }
    
        // Record the IN transaction.
        StockTransaction::create([
            'serial_number'    => $validated['serial_number'],
            'stock_model'      => $validated['model'],  // using "stock_model" per your renaming
            'category'         => $validated['category'],
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);
    
        // After recording the IN transaction, apply the search query to the stock list
        $stocks = Stock::with('division')
            ->when($search, function ($query, $search) {
                return $query->where('serial_number', 'LIKE', "%{$search}%")
                             ->orWhere('model', 'LIKE', "%{$search}%");
            })
            ->paginate(10);  // Apply pagination with 10 items per page
    
        return redirect()->route('stocks.management')->with('success', 'IN transaction recorded.');
    }
    
    // Display the OUT Operation form
    public function createTransactionOut()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        return view('stocks.transaction_out', compact('division'));
    }

    // Process the OUT Operation form
    public function outOperation(Request $request, Stock $stock)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        // Verify the stock belongs to the selected division.
        if ($stock->division_id != $selectedDivision) {
            return redirect()->back()->with('error', 'This stock does not belong to the selected division.');
        }

        // Validate the remark from the modal.
        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        // Record the OUT transaction using the user-provided remark.
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'category'         => $stock->category,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);

        // Update the stock record to mark it as removed.
        $stock->status = 'OUT';
        $stock->save();

        return redirect()->route('stocks.management')->with('success', 'OUT transaction recorded and stock removed.');
    }

    public function create()
    {
        // Fetch all divisions to associate with the stock
        $divisions = Division::all();
        return view('stocks.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        // Validate stock data before storing it
        $validated = $request->validate([
            'serial_number' => 'required|unique:stocks',
            'model'         => 'required',
            'category'      => 'required',
            'division_id'   => 'required|exists:divisions,id',
            'remarks'       => 'nullable'
        ]);

        // Create the stock entry
        Stock::create($validated);

        // Redirect to the stock index page with a success message
        return redirect()->route('stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(Stock $stock)
    {
        // Fetch all divisions to allow editing of the stock division
        $divisions = Division::all();
        return view('stocks.edit', compact('stock', 'divisions'));
    }

    public function update(Request $request, Stock $stock)
    {
        // Validate the updated stock data
        $validated = $request->validate([
            'model'         => 'required',
            'category'      => 'required',
            'division_id'   => 'required|exists:divisions,id',
            'remarks'       => 'nullable'
        ]);

        // Update the stock details
        $stock->update($validated);

        // Redirect to the stock index page with a success message
        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(Stock $stock)
    {
        // Delete the stock entry
        $stock->delete();

        // Redirect to the stock index page with a success message
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }

    // Display a single stock detail
    public function show(Request $request, $category)
    {
        $divisionId = $request->query('division', 'all');
        $search = $request->query('search'); // Get the search term
    
        if ($divisionId === 'all') {
            $stocks = Stock::where('category', $category)
                            ->where('status', 'available')
                            ->when($search, function ($query) use ($search) {
                                $query->where('serial_number', 'LIKE', "%{$search}%")
                                       ->orWhere('model', 'LIKE', "%{$search}%")
                                       ->orWhere('category', 'LIKE', "%{$search}%");
                            })
                            ->paginate(10);
            $division = null;
        } else {
            $stocks = Stock::where('category', $category)
                            ->where('division_id', $divisionId)
                            ->where('status', 'available')
                            ->when($search, function ($query) use ($search) {
                                $query->where('serial_number', 'LIKE', "%{$search}%")
                                       ->orWhere('model', 'LIKE', "%{$search}%")
                                       ->orWhere('category', 'LIKE', "%{$search}%");
                            })
                            ->paginate(10);
            $division = Division::find($divisionId);
        }
    
        return view('stocks.show', compact('stocks', 'category', 'division'));
    }

public function overview(Request $request)
{
    $divisionId = $request->get('division_id') ?? session('selectedDivision');
        
    if (!$divisionId) {
        return redirect()->route('dashboard')->with('message', 'Please select a division.');
    }

    if ($divisionId === 'all') {
        $divisionName = 'All Divisions';
    } else {
        $division = Division::find($divisionId);
        if (!$division) {
            return redirect()->route('dashboard')->with('message', 'Invalid division selected.');
        }
        $divisionName = $division->name;
    }

    // Get the search query from the request
    $search = $request->get('search');

    // Query aggregated stocks grouped by category, applying search filter if present
    $groupedStocks = Stock::select('category', \DB::raw('count(*) as total'))
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
                            ->paginate(10); // Paginate results (10 per page)
        
    // Return the view with aggregated stocks, division name, and division ID
    return view('stocks.overview', compact('groupedStocks', 'divisionName', 'divisionId'));
}

    

    // Display stocks filtered by category.
    // Renamed from "show" to avoid redeclaration.
    public function showByCategory(Request $request, $category)
    {
        $divisionId = $request->query('division', 'all');

        if ($divisionId === 'all') {
            // Retrieve stocks by category across all divisions.
            $stocks = Stock::where('category', $category)
                        ->where('status', 'available')
                        ->paginate(10);
            $division = null; // No specific division selected.
        } else {
            $stocks = Stock::where('category', $category)
                        ->where('division_id', $divisionId)
                        ->where('status', 'available')
                        ->paginate(10);
            $division = Division::find($divisionId);
        }

        return view('stocks.show', compact('stocks', 'category', 'division'));
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
    
        // Sorting
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
    
        // Use pagination – Laravel automatically picks up the page parameter from the request
        $transactions = $query->paginate(20);
    
        if ($request->ajax()) {
            return view('stocks.partials.transactions_container', compact('transactions', 'selectedDivision'))->render();
        }
    
        return view('stocks.transactions', compact('transactions', 'selectedDivision'));
    }
    

}
