<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    // Only Super Admin should be able to manage divisions
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index(Request $request)
    {
        $query = Division::query();
    
        if ($search = $request->query('q')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
    
        if ($request->has('status') && $request->status != "") {
            $status = $request->status === 'Active' ? true : false;
            $query->where('active', $status);
        }
    
        $sortBy = $request->query('sort_by', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');
    
        $divisions = $query->orderBy($sortBy, $sortDirection)->paginate(10);
    
        if ($request->ajax()) {
            return view('division.partials.division_table_body', compact('divisions'))->render();
        }
    
        return view('division.index', compact('divisions'));
    }

    public function create()
    {
        return view('division.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name',
        ]);

        Division::create([
            'name' => $request->name,
            'active' => $request->input('active') == "1",
        ]);

        return redirect()->route('division.index')->with([
            'success' => 'Division Created Successfully.',
            'resource' => 'division'
        ]);
    }

    public function edit(Division $division)
    {
        return view('division.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name,' . $division->id,
        ]);

        $division->update([
            'name' => $request->name,
            'active' => $request->input('active') == "1",
        ]);

        return redirect()->route('division.index')->with([
            'success' => 'Division Updated Successfully',
            'resource' => 'division'
        ]);
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('division.index')->with([
            'success' => 'Division Removed Successfully.',
            'resource' => 'division'
        ]);
    }
}