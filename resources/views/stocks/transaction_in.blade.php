@extends('layouts.layout')

@section('title', 'Stock Transaction - IN Operation')

@section('content')
<div class="container my-5">
    <!-- Flex container with Back button on the top left and heading centered -->
    <div class="flex items-center mb-4">
        <a href="{{ route('stocks.management') }}" class="bg-gray-600 text-white text-sm px-3 py-1 rounded hover:bg-gray-700 transition-colors">
            &larr; Back
        </a>
        <h2 class="flex-grow text-center">
            Stock Transaction - IN Operation
            @if(isset($division))
                <span class="text-sm ml-2 text-gray-600">(Division: {{ $division->name }})</span>
            @endif
        </h2>
    </div>

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

        <!-- Model Field (Text) -->
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
                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
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

        <!-- SLA CATEGORY Field (New Field) -->
        <div class="mb-4">
            <label for="sla_category" class="block text-sm font-medium text-gray-700">SLA CATEGORY</label>
            <select name="sla_category" id="sla_category" class="mt-1 block w-full border rounded p-2">
                <option value="">Select SLA Category</option>
                <option value="Standard">Standard</option>
                <option value="Premium">Premium</option>
                <option value="Critical">Critical</option>
                <!-- Add additional SLA categories if needed -->
            </select>
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
    // Predefined product templates
    var productTemplates = {
        "001": {
            brand: "Huawei",
            model: "MA5600T",
            category: "Router"
        }
        // Add more product templates as needed
    };

    $("#product_code").on("change", function(){
        var code = $(this).val();
        if(productTemplates.hasOwnProperty(code)){
            $("#brand").val(productTemplates[code].brand).prop("disabled", false);
            $("#model").val(productTemplates[code].model).prop("readonly", true);
            $("#category").val(productTemplates[code].category).prop("disabled", false);
        } else {
            $("#brand").val("").removeAttr("disabled");
            $("#model").val("").removeAttr("readonly");
            $("#category").val("").removeAttr("disabled");
        }
    });
});
</script>
@endsection
