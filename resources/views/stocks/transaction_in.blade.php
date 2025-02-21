@extends('layouts.layout')

@section('title', 'Stock Transaction - IN Operation')

@section('content')
<div class="container my-5">
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
                <option value="001">001 - Huawei MA5600T</option>
                <!-- Add additional product codes if needed -->
            </select>
        </div>

        <!-- Brand Field (Dropdown) -->
        <div class="mb-4">
            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
            <select name="brand" id="brand" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Model Field (remains text input) -->
        <div class="mb-4">
            <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
            <input type="text" name="model" id="model" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Category Select (Dropdown) -->
        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" id="category" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Serial Number Field -->
        <div class="mb-4">
            <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Remarks Field -->
        <div class="mb-4">
            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
            <textarea name="remarks" id="remarks" class="mt-1 block w-full border rounded p-2"></textarea>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Record IN Transaction</button>
    </form>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    // Predefined product templates (can later be replaced with dynamic data)
    var productTemplates = {
        "001": {
            brand: "Huawei",
            model: "MA5600T",
            category: "Router"
        }
        // Add more product templates as needed
    };

    // When a product code is selected, auto-fill Brand, Model, and Category.
    $("#product_code").on("change", function(){
        var code = $(this).val();
        if(productTemplates.hasOwnProperty(code)){
            // For dropdowns, set the value to the matching option
            $("#brand").val(productTemplates[code].brand);
            $("#model").val(productTemplates[code].model);
            $("#category").val(productTemplates[code].category);
            // Optionally, disable the fields if you want to prevent editing
            $("#brand, #model, #category").attr("disabled", true);
        } else {
            // If no valid product code, clear fields and enable editing.
            $("#brand, #model, #category").val("").removeAttr("disabled");
        }
    });
});
</script>
@endsection
