<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Only Super Admin should be able to manage brands.
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $categories = Category::orderBy('name')->paginate(10);
        return view('category.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:category,name',
        ]);

        Category::create([
            'name' => $request->name,
            'active' => $request->has('active') ? true : false,
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $categories)
    {
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $categories)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $categories->id,
        ]);

        $categories->update([
            'name' => $request->name,
            'active' => $request->has('active') ? true : false,
        ]);

        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $categories)
    {
        $categories->delete();
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }
}
