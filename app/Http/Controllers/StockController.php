<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
{
    $section = $request->input('section', session('selectedSection', 'Kuching')); // Default to Kuching if no section is set

    // Store the selected section in session
    session(['selectedSection' => $section]);

    $query = Stock::where('section', $section); // Filter stocks by section

    if ($request->filled('serial_number')) {
        $query->where('serial_number', $request->serial_number);
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('brand', 'like', '%' . $request->search . '%')
              ->orWhere('model', 'like', '%' . $request->search . '%')
              ->orWhere('serial_number', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('sort_by') && $request->filled('sort_direction')) {
        $query->orderBy($request->sort_by, $request->sort_direction);
    } else {
        $query->orderBy('id', 'asc');
    }

    $stocks = $query->paginate(10);

    return view('stocks.index', [
        'stocks' => $stocks,
        'categories' => ['Router', 'Switch', 'Modem', 'Access Points'],
        'current_category' => $request->get('category'),
        'current_section' => $section,
    ]);
}

// In StockController.php, create() method:
public function create()
{
    // Check if the user is logged in and has the 'admin' role
    if (auth()->check() && auth()->user()->role == 'admin') {
        return view('stocks.create'); // Only admins can access this page
    }

    // Redirect to the dashboard or another page if the user is not an admin
    return redirect()->route('dashboard')->with('error', 'You do not have admin access.');
}



public function store(Request $request)
{
    $request->validate([
        'serial_number' => 'required|unique:stocks,serial_number',
        'brand' => 'required',
        'model' => 'required',
        'category' => 'required|in:Router,Switch,Modem,Access Points',
        'quantity' => 'required|integer',
        'section' => 'required|string',  // Ensure 'section' is validated
    ]);

    // Create the new stock item
    Stock::create($request->all());

    return redirect()->route('stocks.index', ['section' => $request->section])
        ->with('success', 'Stock added successfully!');
}


    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'serial_number' => 'required|unique:stocks,serial_number,' . $stock->id,
            'brand' => 'required',
            'model' => 'required',
            'category' => 'required|in:Router,Switch,Modem,Access Points',
            'quantity' => 'required|integer',
            'section' => 'required|string'
        ]);
    
        $stock->update($request->all());
    
        return redirect()->route('stocks.index', ['section' => $request->section])
            ->with('success', 'Stock updated successfully!');
    }
    

    public function destroy(Request $request, Stock $stock)
    {
        $section = $request->input('section', session('selectedSection', 'Kuching'));
    
        // Ensure the stock belongs to the selected section before deleting
        if ($stock->section !== $section) {
            return redirect()->route('stocks.index', ['section' => $section])
                ->with('error', 'Stock does not belong to the selected section.');
        }
    
        $stock->delete();
    
        return redirect()->route('stocks.index', ['section' => $section])
            ->with('success', 'Stock deleted successfully.');
    }
    

    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');

        if ($query) {
            $stocks = Stock::where('brand', 'like', "%$query%")
                ->orWhere('model', 'like', "%$query%")
                ->orWhere('serial_number', 'like', "%$query%")
                ->take(10) // Limit the results to 10
                ->select('brand', 'model', 'serial_number') // Return only necessary columns
                ->get();

            return response()->json($stocks);
        }

        return response()->json([]);
    }
}
