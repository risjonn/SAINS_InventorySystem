<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query();

        // Filter by Serial Number (takes priority)
        if ($request->filled('serial_number')) {
            $query->where('serial_number', $request->serial_number);
        }

        // Apply filters for category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply general search
        if ($request->filled('search') && !$request->filled('serial_number')) {
            $query->where(function ($q) use ($request) {
                $q->where('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting
        if ($request->filled('sort_by') && $request->filled('sort_direction')) {
            $query->orderBy($request->sort_by, $request->sort_direction);
        } else {
            $query->orderBy('id', 'asc'); // Default sorting by ID in descending order
        }

        $stocks = $query->paginate(10);

        return view('stocks.index', [
            'stocks' => $stocks,
            'categories' => ['Router', 'Switch', 'Modem', 'Access Points'],
            'sort_by' => $request->get('sort_by', 'id'), // Default sort_by is 'id'
            'sort_direction' => $request->get('sort_direction', 'desc'), // Default sort_direction is 'desc'
            'current_category' => $request->get('category'),
        ]);
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|unique:stocks,serial_number',
            'brand' => 'required',
            'model' => 'required',
            'category' => 'required|in:Router,Switch,Modem,Access Points',
            'quantity' => 'required|integer',
            'section' => 'required|string'
        ]);

        Stock::create($request->all());

        return redirect()->route('stocks.index')->with([
            'success' => 'Stock added successfully!',
            'action' => 'add',
        ]);
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
        ]);

        $stock->update($request->all());

        return redirect()->route('stocks.index')->with([
            'success' => 'Stock updated successfully!',
            'action' => 'edit',
        ]);
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with([
            'success' => 'Stock deleted successfully!',
            'action' => 'delete',
        ]);
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
