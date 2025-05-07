@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">

    <!-- Header & Add-User Button -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Products</h2>
        <div class="flex gap-3">
            <a href="{{ route('category.index') }}"
                   class="bg-indigo-500 hover:bg-indigo-600 transition-colors text-white px-5 py-3 rounded-md shadow">
                <i class="fas fa-tags mr-2"></i> Manage Categories
            </a>
            <button type="button" class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="fas fa-plus mr-2"></i>
                Add New Product
            </button>
        </div>
    </div>
    
    <table class="min-w-full border rounded shadow bg-white">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">Product Code</th>
                <th class="p-3">Category</th>
                <th class="p-3">Brand</th>
                <th class="p-3">Model</th>
                <th class="p-3">Status</th>
                <th class="p-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="border-b">
                <td class="p-3">{{ $product->product_code }}</td>
                <td class="p-3">{{ $product->category }}</td>
                <td class="p-3">{{ $product->brand }}</td>
                <td class="p-3">{{ $product->model }}</td>
                <td class="p-3 {{ $product->status === 'active' ? 'text-green-500 font-bold' : 'text-red-500 font-bold' }}">
                    {{ ucfirst($product->status) }}
                </td>
                <td class="p-3">
                    <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                            data-product-code="{{ $product->product_code }}"
                            data-category="{{ $product->category }}"
                            data-brand="{{ $product->brand }}"
                            data-model="{{ $product->model }}"
                            data-status="{{ $product->status }}"
                            data-update-url="{{ route('products.update', $product->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                            data-action="{{ route('products.destroy', $product->id) }}"
                            data-type="product">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $products->links() }}
</div>

<form id="globalDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Global categories cache to be used by all modals
    let categoriesCache = [];
    
    // Flag to check if categories have been loaded
    let categoriesLoaded = false;
    
    // Function to load categories via AJAX
    function loadCategories() {
        if (categoriesLoaded && categoriesCache.length > 0) {
            return Promise.resolve(categoriesCache);
        }
        
        return fetch('{{ route("api.categories.active") }}')
            .then(response => response.json())
            .then(data => {
                categoriesCache = data;
                categoriesLoaded = true;
                console.log('Categories loaded:', categoriesCache.length);
                return categoriesCache;
            })
            .catch(error => {
                console.error('Error loading categories:', error);
                // Fallback to hardcoded categories if available
                categoriesCache = [
                    @foreach(App\Models\Category::where('active', '1')->get() as $category)
                        { id: {{ $category->id }}, name: "{{ $category->name }}" },
                    @endforeach
                ];
                categoriesLoaded = true;
                return categoriesCache;
            });
    }
    
    // Function to populate any category dropdown
    function populateCategoryDropdown(selectElementId, selectedCategory = null) {
        const categorySelect = document.getElementById(selectElementId);
        
        if (!categorySelect) {
            console.error('Category select element not found:', selectElementId);
            return;
        }
        
        categorySelect.innerHTML = '';
        
        categoriesCache.forEach(function(category) {
            const option = document.createElement('option');
            option.value = category.name;
            option.textContent = category.name;
            
            if (selectedCategory && category.name === selectedCategory) {
                option.selected = true;
            }
            
            categorySelect.appendChild(option);
        });
    }
    
    // Load categories when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadCategories().then(() => {
            // Populate any dropdowns that are visible on page load
            const createCategorySelect = document.getElementById('category');
            const editCategorySelect = document.getElementById('editCategory');
            
            if (createCategorySelect) {
                populateCategoryDropdown('category');
            }
            
            if (editCategorySelect) {
                populateCategoryDropdown('editCategory');
            }
        });
    });
</script>

<!-- Include the Create and Edit modals -->
@include('products.partials.create_modal')
@include('products.partials.edit_modal')
@endsection
