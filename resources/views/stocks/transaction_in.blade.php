@extends('layouts.layout')

@section('title', 'Stock Transaction - IN Operation')

@section('content')
<div class="container my-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('stocks.management') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <h2 class="text-center mb-4">
        Stock Transaction - IN Operation
        @if(isset($division))
            <span class="text-sm ml-2 text-gray-600">(Division: {{ $division->name }})</span>
        @endif
    </h2>

    <form action="{{ route('stocks.transactions.storeIn') }}" method="POST" class="mt-4">
        @csrf

        <!-- Product Code Selection -->
        <div class="mb-4">
            <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code</label>
            <select name="product_code" id="product_code" class="mt-1 block w-full border rounded p-2">
                <option value="">Select Product Code</option>
                @foreach($products as $product)
                    <option value="{{ $product->product_code }}">
                        {{ $product->product_code }} - {{ $product->brand }} {{ $product->model }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Brand Field (Text Field) -->
        <div class="mb-4">
            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
            <input type="text" name="brand" id="brand" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Model Field (Text Field) -->
        <div class="mb-4">
            <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
            <input type="text" name="model" id="model" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Category Field (Dropdown) -->
        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" id="category" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <!-- Serial Number Field -->
        <div class="mb-4">
            <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Requisition No. Field (Manually Entered) -->
        <div class="mb-2">
            <label for="requisition_no" class="block text-sm font-medium text-gray-700">Requisition No.</label>
            <input type="text" name="requisition_no" id="requisition_no" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Requisition/ERP Number Field (New Manual Field) -->
        <div class="mb-4">
            <label for="erp_number" class="block text-sm font-medium text-gray-700">Requisition/ERP Number</label>
            <input type="text" name="erp_number" id="erp_number" class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- SLA Field (New Field) -->
        <div class="mb-4">
            <label for="sla" class="block text-sm font-medium text-gray-700">SLA</label>
            <input type="text" name="sla" id="sla" class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- SLA CATEGORY Field (Changed to a text field) -->
        <div class="mb-4">
            <label for="sla_category" class="block text-sm font-medium text-gray-700">SLA CATEGORY</label>
            <input type="text" name="sla_category" id="sla_category" class="mt-1 block w-full border rounded p-2" placeholder="Enter SLA Category">
        </div>

        <!-- Remarks Field -->
        <div class="mb-4">
            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
            <textarea name="remarks" id="remarks" class="mt-1 block w-full border rounded p-2"></textarea>
        </div>

        <!-- Submit Button (aligned left) -->
        <div class="flex">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Record IN Transaction
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    // Predefined product templates as a JavaScript array of objects.
    var productTemplates = @json($products);

    $("#product_code").on("change", function(){
        var code = $(this).val();
        // Find the product that matches the selected product code.
        var selectedProduct = productTemplates.find(function(prod) {
            return prod.product_code === code;
        });
        if (selectedProduct) {
            $("#brand").val(selectedProduct.brand);
            $("#model").val(selectedProduct.model);
            $("#category").val(selectedProduct.category);
        } else {
            $("#brand").val("");
            $("#model").val("");
            $("#category").val("");
        }
    });
    
    // Removed auto-generation of requisition number so user can manually input it.
});
</script>
@endsection
