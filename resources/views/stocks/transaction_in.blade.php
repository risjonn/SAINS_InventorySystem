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

    <form action="{{ route('stocks.transactions.storeIn') }}" method="POST" class="mt-4" id="inTransactionForm" novalidate>
        @csrf

        <!-- Product Code Selection -->
        <div class="mb-4">
            <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code</label>
            <select name="product_code" id="product_code" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Product Code</option>
                @foreach($products as $product)
                    <option value="{{ $product->product_code }}"
                        {{ old('product_code') == $product->product_code ? 'selected' : '' }}>
                        {{ $product->product_code }} - {{ $product->brand }} {{ $product->model }}
                    </option>
                @endforeach
            </select>
            <span class="text-red-500" id="product_code_error"></span>
        </div>

        <!-- Brand Field -->
        <div class="mb-4">
            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="mt-1 block w-full border rounded p-2" required>
            <span class="text-red-500" id="brand_error"></span>
        </div>

        <!-- Model Field -->
        <div class="mb-4">
            <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
            <input type="text" name="model" id="model" value="{{ old('model') }}" class="mt-1 block w-full border rounded p-2" required>
            <span class="text-red-500" id="model_error"></span>
        </div>

        <!-- Category Field -->
        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" id="category" class="mt-1 block w-full border rounded p-2" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-red-500" id="category_error"></span>
        </div>

        <!-- Serial Number Field (always visible and required) -->
        <div id="serialField" class="mb-4">
            <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="mt-1 block w-full border rounded p-2" required>
            <span class="text-red-500" id="serial_number_error"></span>
        </div>

        <!-- Removed Quantity Field -->

        <!-- Requisition No -->
        <div class="mb-4">
            <label for="requisition_no" class="block text-sm font-medium text-gray-700">Requisition No.</label>
            <input type="text" name="requisition_no" id="requisition_no" value="{{ old('requisition_no') }}" class="mt-1 block w-full border rounded p-2" maxlength="255">
            <span class="text-red-500" id="requisition_no_error"></span>
        </div>

        <!-- Requisition/ERP Number -->
        <div class="mb-4">
            <label for="erp_number" class="block text-sm font-medium text-gray-700">Requisition/ERP Number</label>
            <input type="text" name="erp_number" id="erp_number" value="{{ old('erp_number') }}" class="mt-1 block w-full border rounded p-2" maxlength="255">
            <span class="text-red-500" id="erp_number_error"></span>
        </div>

        <!-- SLA -->
        <div class="mb-4">
            <label for="sla" class="block text-sm font-medium text-gray-700">SLA</label>
            <input type="text" name="sla" id="sla" value="{{ old('sla') }}" class="mt-1 block w-full border rounded p-2" maxlength="255">
            <span class="text-red-500" id="sla_error"></span>
        </div>

        <!-- SLA Category -->
        <div class="mb-4">
            <label for="sla_category" class="block text-sm font-medium text-gray-700">SLA CATEGORY</label>
            <input type="text" name="sla_category" id="sla_category" value="{{ old('sla_category') }}" class="mt-1 block w-full border rounded p-2" maxlength="255" placeholder="Enter SLA Category">
            <span class="text-red-500" id="sla_category_error"></span>
        </div>

        <!-- Remarks -->
        <div class="mb-4">
            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
            <textarea name="remarks" id="remarks" class="mt-1 block w-full border rounded p-2" maxlength="255">{{ old('remarks') }}</textarea>
            <span class="text-red-500" id="remarks_error"></span>
        </div>

        <div class="flex">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Record IN Transaction</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@php use App\Models\Product; @endphp

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    var productTemplates = @json($products);

    // Auto-fill brand, model, category on product_code change
    $("#product_code").on("change", function(){
        var code = $(this).val();
        var selectedProduct = productTemplates.find(p => p.product_code === code);
        if (selectedProduct) {
            $("#brand").val(selectedProduct.brand);
            $("#model").val(selectedProduct.model);
            $("#category").val(selectedProduct.category);
        } else {
            $("#brand").val('');
            $("#model").val('');
            $("#category").val('');
        }
    });

    @if(old('product_code'))
        $("#product_code").val("{{ old('product_code') }}").trigger("change");
    @endif

    // Clear error messages on input/change
    $('#inTransactionForm input, #inTransactionForm select, #inTransactionForm textarea').on('input change', function() {
        let fieldId = $(this).attr('id');
        $('#' + fieldId + '_error').text('');
        $(this).removeClass('border-red-500');
    });

    function clearErrors() {
        $('.text-red-500').text('');
        $('input, select, textarea').removeClass('border-red-500');
    }

    function showError(fieldId, message) {
        $('#' + fieldId + '_error').text(message);
        $('#' + fieldId).addClass('border-red-500');
    }

    $('#inTransactionForm').on('submit', function(e){
        e.preventDefault();
        clearErrors();

        let valid = true;

        let productCode = $('#product_code').val();
        if (!productCode) {
            showError('product_code', 'Please select a product code.');
            valid = false;
        }

        let brand = $('#brand').val().trim();
        if (!brand) {
            showError('brand', 'Brand is required.');
            valid = false;
        }

        let model = $('#model').val().trim();
        if (!model) {
            showError('model', 'Model is required.');
            valid = false;
        }

        let category = $('#category').val();
        if (!category) {
            showError('category', 'Please select a category.');
            valid = false;
        }

        let serial = $('#serial_number').val().trim();
        if (!serial) {
            showError('serial_number', 'Serial number is required.');
            valid = false;
        } else if (!/^[A-Za-z0-9\-]+$/.test(serial)) {
            showError('serial_number', 'Serial number can only contain letters, numbers, and hyphens.');
            valid = false;
        }

        ['requisition_no', 'erp_number', 'sla', 'sla_category', 'remarks'].forEach(function(field) {
            let val = $('#' + field).val();
            if (val && val.length > 255) {
                showError(field, 'Maximum 255 characters allowed.');
                valid = false;
            }
        });

        if (!valid) {
            return;
        }

        // Check duplicate serial number via AJAX
        if (serial) {
            $.ajax({
                url: "{{ route('stocks.checkSerial') }}",
                method: 'GET',
                data: { serial: serial, product_code: productCode },
                success: function(response) {
                    if (response.duplicate) {
                        Swal.fire({
                            title: 'Duplicate Serial Number Found',
                            text: `This serial number already exists under product code ${response.other_product}. Do you want to proceed?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, proceed',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#inTransactionForm')[0].submit();
                            }
                        });
                    } else {
                        $('#inTransactionForm')[0].submit();
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to check serial number uniqueness.', 'error');
                }
            });
            return;
        }
        this.submit();
    });
});
</script>
@endsection
