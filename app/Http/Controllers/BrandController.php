<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    // Only Super Admin should be able to manage brands.
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index(Request $request)
    {
        $query = Brand::query();
        
        if ($search = $request->query('q')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        if ($request->has('status') && $request->status != "") {
            $status = $request->status === 'Active' ? true : false;
            $query->where('active', $status);
        }
    
        $sortBy = $request->query('sort_by', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');
    
        $brands = $query->orderBy($sortBy, $sortDirection)->paginate(10);
        
        if ($request->ajax()) {
            return view('brands.partials.brand_table_body', compact('brands'))->render();
        }
        
        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name',
        ]);

        Brand::create([
            'name' => $request->name,
            'active' => $request->has('active') ? true : false,
        ]);

        return redirect()->route('brands.index')->with([
            'success' => 'Brand Created Sucessfully.',
            'resource' => 'brand'
        ]);
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id,
        ]);

        $brand->update([
            'name' => $request->name,
            'active' => $request->input('active') == "1",
        ]);

        return redirect()->route('brands.index')->with([
            'success' => 'Brand Updated Successfully.',
            'resource' => 'brand'
        ]);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('brands.index')->with([
            'success' => 'Brand Removed Successfully.',
            'resource' => 'brand'
        ]);
    }
}
