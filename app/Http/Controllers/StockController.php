<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use App\Models\StockTransaction;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('division')->get();
        return view('stocks.index', compact('stocks'));
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
            'model' => 'required',
            'category' => 'required',
            'division_id' => 'required|exists:divisions,id',
            'remarks' => 'nullable'
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
            'model' => 'required',
            'category' => 'required',
            'division_id' => 'required|exists:divisions,id',
            'remarks' => 'nullable'
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}