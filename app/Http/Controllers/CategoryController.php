<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Only Super Admin should be able to manage categories.
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index(Request $request)
    {
        $query = Category::query();
    
        if ($search = $request->query('q')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
    
        if ($request->has('status') && $request->status != "") {
            $status = $request->status === 'Active' ? true : false;
            $query->where('active', $status);
        }
    
        $sortBy = $request->query('sort_by', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');
    
        $categories = $query->orderBy($sortBy, $sortDirection)->paginate(10);
    
        if ($request->ajax()) {
           return view('category.partials.category_table_body', compact('categories'))->render();
        }
    
        return view('category.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'active' => $request->input('active') == "1",
        ]);

        return redirect()->route('category.index')->with([
            'success' => 'Category Created Successfully.',
            'resource' => 'category'
        ]);
    }

    public function edit(Category $category)
    {
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'active' => $request->input('active') == "1",
        ]);

        return redirect()->route('category.index')->with([
            'success' => 'Category Updated Successfully',
            'resource' => 'category'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('category.index')->with([
            'success' => 'Category Removed Successfully.',
            'resource' => 'category'
        ]);
    }
}
