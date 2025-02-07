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
}
