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

        // Manually check if the user is an admin
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

        // Apply filter if provided
        $filter = $request->query('filter', 'all');
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
        $selectedDivision = session('selectedDivision', auth()->user()->division_id);
        $division = Division::find($selectedDivision);
        $search = $request->query('search');
    
        $stocks = Stock::with('division')
                        ->where('division_id', $selectedDivision)
                        ->where('status', 'available')
                        ->when($search, function ($query, $search) {
                            return $query->where('serial_number', 'LIKE', "%{$search}%")
                                         ->orWhere('model', 'LIKE', "%{$search}%");
                        })
                        ->paginate(10);
        return view('stocks.management', compact('stocks', 'division'));
        $operationType = $request->input('operation_type');
if ($operationType === 'LOAN') {
    // Process loan transaction, including storing expected_return_date
} else {
    // Process standard OUT transaction
}

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
    
        // This method will update if the stock exists, or create it if not.
        $stock = Stock::updateOrCreate(
            ['serial_number' => trim($validated['serial_number'])],
            [
                'model'       => $validated['model'],
                'category'    => $validated['category'],
                'division_id' => $selectedDivision,
                'remarks'     => $validated['remarks']
            ]
        );
    
        StockTransaction::create([
            'serial_number'    => trim($validated['serial_number']),
            'stock_model'      => $validated['model'],
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

        if ($divisionId !== 'all') {
            $division = Division::find($divisionId);
        } else {
            $division = null;
        }
    
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
        if ($division === 'all') {
            // Retrieve stocks by category across all divisions.
            $stocks = Stock::where('category', $category)
                           ->where('status', 'available')
                           ->paginate(10);
            $divisionModel = null;
        } else {
            $stocks = Stock::where('category', $category)
                           ->where('division_id', $division)
                           ->where('status', 'available')
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
}
