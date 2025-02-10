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
                        ->get();
    
        return view('stocks.index', compact('stocks'));
    }

    public function transactions()
    {
        // Retrieve the division selected in the dashboard; if not present, default to the user's division.
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        // Retrieve the division data
        $division = Division::find($selectedDivision);

        // Join stock_transactions with stocks to filter by the division
        $transactionsQuery = StockTransaction::join('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
            ->join('users', 'stock_transactions.handled_by', '=', 'users.id')
            ->where('stocks.division_id', $selectedDivision)
            ->select('stock_transactions.*', 'users.name as handled_by_name'); // Only select columns from stock_transactions
            
            $filter = request('filter', 'all');
            if ($filter !== 'all') {
                $transactionsQuery->where('stock_transactions.type', $filter);
            }

            $transactions = $transactionsQuery->paginate(10)->appends(request()->query());
        return view('stocks.transactions', compact('transactions' , 'division'));
    }

    public function management()
    {
        // Retrieve the selected division from session (or default to user's division)
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);

        // Retrieve stocks that belong to the selected division.
        // (You might want to order them as needed.)
        $stocks = Stock::with('division')
                        ->where('division_id', $selectedDivision)
                        ->where('status', 'available')
                        ->get();

        return view('stocks.management', compact('stocks', 'division'));
    }

    // Display the IN Operation form
    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        return view('stocks.transaction_in', compact('division'));
    }

    // Process the IN Operation form
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
            'model' => 'required',
            'category' => 'required',
            'division_id' => 'required|exists:divisions,id',
            'remarks' => 'nullable'
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
            'model' => 'required',
            'category' => 'required',
            'division_id' => 'required|exists:divisions,id',
            'remarks' => 'nullable'
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
    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

public function overview(Request $request)
    {
        // Try to get the division_id from the request; if not available, check the session.
        $divisionId = $request->get('division_id') ?? session('selectedDivision');

        if (!$divisionId) {
            return redirect()->route('dashboard')->with('message', 'Please select a division.');
        }

        $division = Division::find($divisionId);
        if (!$division) {
            $stocks = collect();
            return view('stocks.overview', compact('stocks'))->with('message', 'Invalid division selected.');
        }

        $stocks = Stock::with('division')
                        ->where('division_id', $divisionId)
                        ->where('status', 'available')
                        ->get();

        return view('stocks.overview', compact('stocks', 'division'));
    }

}
