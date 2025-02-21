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

        <!-- Category Field -->
        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" id="category" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Category</option>
                <option value="Router">Router</option>
                <option value="Switch">Switch</option>
                <option value="Modem">Modem</option>
                <!-- Add additional categories if needed -->
            </select>
        </div>

        <!-- Brand Field -->
        <div class="mb-4">
            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
            <input type="text" name="brand" id="brand" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Model Field -->
        <div class="mb-4">
            <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
            <input type="text" name="model" id="model" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Serial Number Field -->
        <div class="mb-4">
            <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <!-- Product Code -->
        <div class="mb-4">
            <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code</label>
            <select name="product_code" id="product_code" class="mt-1 block w-full border rounded p-2">
                <option value="">Select Product Code</option>
                <option value="001">001 - Huawei MA5600T</option>
                <!-- Add additional product codes if needed -->
            </select>
        </div>

        <!-- Requisition No Field -->
        <div class="mb-4">
            <label for="requisition_no" class="block text-sm font-medium text-gray-700">Requisition No.</label>
            <input type="text" name="requisition_no" id="requisition_no" class="mt-1 block w-full border rounded p-2">
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
<!-- Include jQuery once -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    // Predefined product templates (keeps existing logic)
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
            $("#brand").val(productTemplates[code].brand);
            $("#model").val(productTemplates[code].model);
            $("#category").val(productTemplates[code].category);

            // Optionally set fields to read-only if you want them locked after selection
            $("#brand, #model, #category").attr("readonly", true);
        } else {
            // If no valid product code, clear fields and allow editing.
            $("#brand, #model, #category").val("").removeAttr("readonly");
        }
    });
});
</script>
@endsection
