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

        // Manually check if the user is an admin for sensitive operations
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'Admin') {
                abort(403, 'Unauthorized Action');
            }
            return $next($request);
        })->only(['edit', 'update', 'destroy']);
    }

    // Example index method for listing stocks
    public function index(Request $request)
    {
        $category = $request->get('category'); // Optionally filter by category

        $stocks = Stock::with('division')
                    ->when($category, function($query, $category) {
                        return $query->where('category', $category);
                    })
                    ->get();

        return view('stocks.index', compact('stocks'));
    }

    /**
     * Stock Overview page.
     * If the selected division is "all", retrieve stocks from all divisions.
     */
    public function overview(Request $request)
    {
        // Get the selected division from the query string or session (default to 'all')
        $selectedDivision = $request->get('division') ?? session('selectedDivision', 'all');
        session(['selectedDivision' => $selectedDivision]);

        if ($selectedDivision === 'all') {
            // Retrieve stocks for all divisions.
            $stocks = Stock::with('division')->get();
            $division = null; // Indicates that no specific division is selected.
        } else {
            // Retrieve stocks filtered by the selected division.
            $division = Division::find($selectedDivision);
            $stocks = Stock::with('division')
                           ->where('division_id', $selectedDivision)
                           ->get();
        }
        
        return view('stocks.overview', compact('stocks', 'division'));
    }

    /**
     * Stock Transactions page.
     * Supports "all" divisions by omitting the division filter.
     */
    public function transactions()
    {
        // Retrieve the selected division from session (or default to the user's division)
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        if ($selectedDivision === 'all') {
            // Retrieve all transactions without filtering by division.
            $transactions = StockTransaction::join('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                            ->join('users', 'stock_transactions.handled_by', '=', 'users.id')
                            ->select('stock_transactions.*', 'users.name as handled_by_name')
                            ->get();
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $transactions = StockTransaction::join('stocks', 'stock_transactions.serial_number', '=', 'stocks.serial_number')
                            ->join('users', 'stock_transactions.handled_by', '=', 'users.id')
                            ->where('stocks.division_id', $selectedDivision)
                            ->select('stock_transactions.*', 'users.name as handled_by_name')
                            ->get();
        }

        return view('stocks.transactions', compact('transactions', 'division'));
    }

    /**
     * Stock Management page.
     * If "all" is selected, retrieve all available stocks.
     */
    public function management()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);

        if ($selectedDivision === 'all') {
            $stocks = Stock::with('division')
                           ->where('status', 'available')
                           ->get();
            $division = null;
        } else {
            $division = Division::find($selectedDivision);
            $stocks = Stock::with('division')
                           ->where('division_id', $selectedDivision)
                           ->where('status', 'available')
                           ->get();
        }

        return view('stocks.management', compact('stocks', 'division'));
    }

    /**
     * Process the OUT operation.
     * When a specific division is selected, enforce that the stock belongs to that division.
     * If "all" is selected, do not perform this check.
     */
    public function outOperation(Request $request, Stock $stock)
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        
        // If a specific division is selected, check that the stock belongs to that division.
        if ($selectedDivision !== 'all' && $stock->division_id != $selectedDivision) {
            return redirect()->back()->with('error', 'This stock does not belong to the selected division.');
        }
        
        // Validate the provided remark.
        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);
        
        // Record the OUT transaction.
        StockTransaction::create([
            'serial_number'    => $stock->serial_number,
            'stock_model'      => $stock->model,
            'category'         => $stock->category,
            'type'             => 'OUT',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);
        
        // Mark the stock as removed.
        $stock->status = 'OUT';
        $stock->save();
        
        return redirect()->route('stocks.management')->with('success', 'OUT transaction recorded and stock removed.');
    }

    /**
     * Show the form for an IN operation.
     */
    public function createTransactionIn()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        // If "all" is selected, you might choose to set division to null or handle it as needed.
        $division = ($selectedDivision === 'all') ? null : Division::find($selectedDivision);
        return view('stocks.transaction_in', compact('division'));
    }

    /**
     * Process the IN operation form.
     */
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
            // Create new stock and assign the selected division (if not "all").
            $stock = Stock::create([
                'serial_number' => $validated['serial_number'],
                'model'         => $validated['model'],
                'category'      => $validated['category'],
                'division_id'   => ($selectedDivision === 'all') ? null : $selectedDivision,
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
            'stock_model'      => $validated['model'],
            'category'         => $validated['category'],
            'type'             => 'IN',
            'handled_by'       => auth()->id(),
            'remarks'          => $validated['remarks'],
            'transaction_date' => now(),
        ]);

        return redirect()->route('stocks.management')->with('success', 'IN transaction recorded.');
    }

    /**
     * Show the form for an OUT operation.
     */
    public function createTransactionOut()
    {
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = ($selectedDivision === 'all') ? null : Division::find($selectedDivision);
        return view('stocks.transaction_out', compact('division'));
    }

    // Other methods like create(), store(), edit(), update(), destroy(), show() can remain as you already have them.
}
