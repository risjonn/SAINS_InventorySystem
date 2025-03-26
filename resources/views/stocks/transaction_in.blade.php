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

        <!-- Hidden Requisition No. Field (Auto-generated) -->
        <input type="hidden" name="requisition_no" id="requisition_no" readonly>

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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    // Output products data as a JavaScript array of objects.
    var productTemplates = @json($products);

    $(document).ready(function(){
        $("#product_code").on("change", function(){
            var code = $(this).val();
            // Find the product that matches the selected product code
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
        
        // Auto-generate requisition number in format: REQ-YYYYMMDD-XXXX
        function generateRequisitionNumber() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            var randomDigits = Math.floor(Math.random() * 9000) + 1000;
            return 'REQ-' + yyyy + mm + dd + '-' + randomDigits;
        }
        $("#requisition_no").val(generateRequisitionNumber());
    });
</script>
@endsection
