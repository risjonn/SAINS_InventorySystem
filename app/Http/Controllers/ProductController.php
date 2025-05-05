<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::paginate(10);
        $productCategories = Product::where('status', 'active')->distinct()->pluck('category');
        // $activeCategories = collect($productCategories)->map(function($cat) {
        //     return (object)['name' => $cat];
        // });
    
        // Fetch all categories from the categories table as well.
        $allCategories = Category::all();

        $activeCategories = Category::where('active', '1')->paginate(10);
    
        return view('products.index', compact('products', 'activeCategories', 'allCategories'));
    }

    // Show the form for creating a new product.
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Store a newly created product in storage.
    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'category'     => 'required',
            'brand'        => 'required',
            'model'        => 'required',
            'status'       => 'required|in:active,inactive',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    // Show the form for editing the specified product.
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // Update the specified product in storage.
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code,'.$product->id,
            'category'     => 'required',
            'brand'        => 'required',
            'model'        => 'required',
            'status'       => 'required|in:active,inactive',
        ]);
    
        $product->update([
            'product_code' => $request->product_code,
            'category'     => $request->category,
            'brand'        => $request->brand,
            'model'        => $request->model,
            'status'       => $request->status,
        ]);
    
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Remove the specified product from storage.
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}