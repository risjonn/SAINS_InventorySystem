@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    <h2 class="text-2xl font-semibold mb-4">Manage Products</h2>
    
    <div class="flex flex-wrap gap-4 mb-4">
        <!-- Add New Product Button -->
        <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="fas fa-plus mr-2"></i> Add New Product
        </button>
        <!-- Manage Categories Button -->
        <a href="{{ route('category.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
            Manage Categories
        </a>
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
                        <button type="submit" class="text-red-500 hover:text-red-700">
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

<!-- Include the Create and Edit modals -->
@include('products.partials.create_modal')
@include('products.partials.edit_modal')
@endsection
