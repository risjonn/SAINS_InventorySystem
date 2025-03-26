@extends('layouts.layout')

@section('title', 'Stock Management')

@section('content')
<div class="container my-5">
    @php 
        $selectedDivision = session('selectedDivision');
    @endphp

    <!-- Page Header -->
    <h2 class="text-xl font-semibold mt-4">
        Stock Management
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }})
        </span>
    </h2>

    @if($selectedDivision === 'all')
        <div class="alert alert-warning mt-4">
            Please select a specific division for stock management operations.
        </div>
    @else
        {{-- Overdue Loans Alert --}}
        @if(isset($overdueLoans) && count($overdueLoans) > 0)
            <div class="alert alert-danger mt-3">
                <strong>Attention!</strong> There are {{ count($overdueLoans) }} overdue loan(s). Please review them immediately.
            </div>
        @endif

        <!-- "IN" Operation Button -->
        <div class="my-4">
            <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-2">IN</a>
        </div>

        <!-- Row for Search and Sort Controls -->
        <div class="row mb-4">
            <!-- Search Input -->
            <div class="col-md-8 mb-2 mb-md-0">
                <form id="searchForm" method="GET" action="{{ url()->current() }}">
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                               placeholder="Search stocks... (Serial Number, Model)" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary ml-2">Apply</button>
                            <button type="button" class="btn btn-secondary ml-2" id="clearSearch">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Sorting Dropdown -->
            <div class="col-md-4 text-md-right">
                <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
                <select id="sortOption" class="p-2 border rounded" style="width: 250px;">
                    <option value="model_asc">Model (A-Z)</option>
                    <option value="model_desc">Model (Z-A)</option>
                    <option value="category_asc">Category (A-Z)</option>
                    <option value="category_desc">Category (Z-A)</option>
                    <option value="updated_at_asc">Updated At (Oldest)</option>
                    <option value="updated_at_desc" selected>Updated At (Newest)</option>
                </select>
            </div>
        </div>

        <!-- Bootstrap Tabs for Available & Loaned -->
        <ul class="nav nav-tabs" id="stockTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#availableTab" type="button" role="tab" aria-controls="availableTab" aria-selected="true">
                    Available Stocks
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="loaned-tab" data-bs-toggle="tab" data-bs-target="#loanedTab" type="button" role="tab" aria-controls="loanedTab" aria-selected="false">
                    Loaned Stocks
                </button>
            </li>
        </ul>

        <!-- Tab Panes -->
        <div class="tab-content" id="stockTabsContent">
            <!-- Available Stocks Pane -->
            <div class="tab-pane fade show active" id="availableTab" role="tabpanel" aria-labelledby="available-tab">
                <div class="mt-4">
                    <table id="availableStockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                                <th class="text-center py-3">Category</th>
                                <th class="text-center py-3">Brand</th>
                                <th class="text-center py-3">Model</th>
                                <th class="text-center py-3">Serial Number</th>
                                <th class="text-center py-3">Requisition No.</th>
                                <th class="text-center py-3">Requisition/ERP Number</th>
                                <th class="text-center py-3">Remarks</th>
                                <th class="text-center py-3">Updated At</th>
                                <th class="text-center py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($availableStocks as $stock)
                                <tr class="border-bottom">
                                    <td class="text-center py-2">{{ $stock->category }}</td>
                                    <td class="text-center py-2">{{ $stock->brand ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->model }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    <td class="text-center py-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->erp_number ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->remarks }}</td>
                                    <td class="text-center py-2">
                                        {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="text-center py-2">
                                        <!-- NEW: Details Button -->
                                        <button type="button"
                                                class="btn btn-info btn-sm btn-details"
                                                data-requisition_no="{{ $stock->requisition_no ?? 'Not Available' }}"
                                                data-serial_number="{{ $stock->serial_number ?? 'N/A' }}"
                                                data-brand="{{ $stock->brand ?? 'N/A' }}"
                                                data-product_code="{{ $stock->product_code ?? 'N/A' }}"
                                                data-model="{{ $stock->model }}"
                                                data-category="{{ $stock->category }}"
                                                data-remarks="{{ $stock->remarks ?? '' }}"
                                                data-updated_at="{{ $stock->updated_at }}"
                                        >
                                            Details
                                        </button>

                                        <!-- OUT Button -->
                                        <button type="button" class="btn btn-danger btn-out" data-stock-id="{{ $stock->id }}">
                                            OUT
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-3 text-muted">No matching records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {!! $availableStocks->appends(request()->all())->links() !!}
                </div>
            </div>

            <!-- Loaned Stocks Pane -->
            <div class="tab-pane fade" id="loanedTab" role="tabpanel" aria-labelledby="loaned-tab">
                <div class="mt-4">
                    <table id="loanedStockTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                                <th class="text-center py-3">Category</th>
                                <th class="text-center py-3">Brand</th>
                                <th class="text-center py-3">Model</th>
                                <th class="text-center py-3">Serial Number</th>
                                <th class="text-center py-3">Requisition No.</th>
                                <th class="text-center py-3">Requisition/ERP Number</th>
                                <th class="text-center py-3">Remarks</th>
                                <th class="text-center py-3">Updated At</th>
                                <th class="text-center py-3">Taken By</th>
                                <th class="text-center py-3">Days Left</th>
                                <th class="text-center py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loanedStocks as $stock)
                                @php
                                    // Parse out "(Taken by: X)" from remarks
                                    $fullRemarks = $stock->remarks ?? '';
                                    $pattern = '/\(Taken by:\s*(.*?)\)/i';
                                    $matches = [];
                                    $takenByValue = 'N/A';
                                    if (preg_match($pattern, $fullRemarks, $matches)) {
                                        $takenByValue = $matches[1];
                                    }
                                    // Remove the "Taken by" portion from remarks
                                    $displayRemarks = trim(preg_replace($pattern, '', $fullRemarks));

                                    // Calculate days left using loan_start_date & loan_period if available;
                                    // fallback to 90 days and updated_at if not set.
                                    $loanStart  = $stock->loan_start_date ?? $stock->updated_at;
                                    $loanPeriod = $stock->loan_period ?? 90;
                                    $daysElapsed = \Carbon\Carbon::parse($loanStart)->diffInDays(now());
                                    $daysLeft    = $loanPeriod - $daysElapsed;
                                @endphp

                                <tr class="border-bottom"
                                    @if($daysLeft < 0)
                                        style="background-color: #FFECEC; color: #C00000;"
                                    @endif
                                >
                                    <td class="text-center py-2">{{ $stock->category }}</td>
                                    <td class="text-center py-2">{{ $stock->brand ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->model }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    <td class="text-center py-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->erp_number ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $displayRemarks }}</td>
                                    <td class="text-center py-2">
                                        {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="text-center py-2">{{ $takenByValue }}</td>
                                    <td class="text-center py-2">
                                        @if($stock->status === 'LOAN')
                                            @if($daysLeft > 0)
                                                {{ $daysLeft }} days left
                                            @elseif($daysLeft == 0)
                                                Expires today
                                            @else
                                                {{ abs($daysLeft) }} days overdue
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-center py-2">
                                        <!-- NEW: Details Button -->
                                        <button type="button"
                                                class="btn btn-info btn-sm btn-details"
                                                data-requisition_no="{{ $stock->requisition_no ?? 'Not Available' }}"
                                                data-serial_number="{{ $stock->serial_number ?? 'N/A' }}"
                                                data-brand="{{ $stock->brand ?? 'N/A' }}"
                                                data-product_code="{{ $stock->product_code ?? 'N/A' }}"
                                                data-model="{{ $stock->model }}"
                                                data-category="{{ $stock->category }}"
                                                data-remarks="{{ $fullRemarks }}"
                                                data-updated_at="{{ $stock->updated_at }}"
                                        >
                                            Details
                                        </button>

                                        <!-- Return Button -->
                                        <button type="button" class="btn btn-primary btn-return" data-stock-id="{{ $stock->id }}">
                                            Return
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-3 text-muted">No matching records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {!! $loanedStocks->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>
    @endif
</div>

@include('stocks.partials.operation-modal') <!-- Keep your existing modal for OUT/LOAN/RETURN -->

<!-- NEW: Stock Details Modal (for "Details" button) -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg rounded-lg">
      <div class="modal-header text-white" style="background: linear-gradient(45deg, #3b82f6, #1e40af);">
        <h5 class="modal-title" id="detailsModalLabel">Stock Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Show stock info in key-value style -->
        <div class="mb-2">
          <strong>Requisition No:</strong> <span id="detailRequisitionNo">-</span>
        </div>
        <div class="mb-2">
          <strong>Serial Number:</strong> <span id="detailSerialNumber">-</span>
        </div>
        <div class="mb-2">
          <strong>Brand:</strong> <span id="detailBrand">-</span>
        </div>
        <div class="mb-2">
          <strong>Product Code:</strong> <span id="detailProductCode">-</span>
        </div>
        <div class="mb-2">
          <strong>Model:</strong> <span id="detailModel">-</span>
        </div>
        <div class="mb-2">
          <strong>Category:</strong> <span id="detailCategory">-</span>
        </div>
        <div class="mb-2">
          <strong>Remarks:</strong> <span id="detailRemarks">-</span>
        </div>
        <div class="mb-2">
          <strong>Last Updated:</strong> <span id="detailUpdatedAt">-</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (optional, but needed for your sorting/search code) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){

    /* ======================= Sorting Functionality ======================= */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false };
        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 3; // Model is in col 3
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 3;
                config.order = "desc";
                break;
            case "category_asc":
                config.columnIndex = 1; // Category is in col 1
                config.order = "asc";
                break;
            case "category_desc":
                config.columnIndex = 1;
                config.order = "desc";
                break;
            case "updated_at_asc":
                config.columnIndex = 8; // Updated At is in col 8
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
                config.columnIndex = 8;
                config.order = "desc";
                config.isDate = true;
                break;
            default:
                config.columnIndex = 1;
                config.order = "asc";
        }
        sortTable(config.columnIndex, config.order, config.isDate);
        sortLoanedTable(config.columnIndex, config.order, config.isDate);
    }

    function sortTable(columnIndex, order, isDate) {
        let rows = $("#availableStockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().toUpperCase();
            if (isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc"
                ? (A < B ? -1 : A > B ? 1 : 0)
                : (A > B ? -1 : A < B ? 1 : 0);
        });
        $.each(rows, function(index, row) {
            $("#availableStockTable tbody").append(row);
        });
    }

    function sortLoanedTable(columnIndex, order, isDate) {
        let rows = $("#loanedStockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().toUpperCase();
            if (isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc"
                ? (A < B ? -1 : A > B ? 1 : 0)
                : (A > B ? -1 : A < B ? 1 : 0);
        });
        $.each(rows, function(index, row) {
            $("#loanedStockTable tbody").append(row);
        });
    }

    $("#sortOption").on("change", function() {
        sortTableByOption();
    });
    $("#sortOption").trigger("change");

    /* ======================= Search Functionality ======================= */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        
        // Filter availableStockTable
        var $rowsAvail = $("#availableStockTable tbody tr").not(".no-record");
        $rowsAvail.each(function() {
            var rowText = $(this).text().toLowerCase(); 
            $(this).toggle(rowText.indexOf(value) !== -1);
        });
        if($("#availableStockTable tbody tr:visible").length === 0) {
            var colspan = $("#availableStockTable thead tr th").length;
            $("#availableStockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        } else {
            $("#availableStockTable tbody tr.no-record").remove();
        }
        
        // Filter loanedStockTable
        var $rowsLoan = $("#loanedStockTable tbody tr").not(".no-record");
        $rowsLoan.each(function() {
            var rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(value) !== -1);
        });
        if($("#loanedStockTable tbody tr:visible").length === 0) {
            var colspan = $("#loanedStockTable thead tr th").length;
            $("#loanedStockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        } else {
            $("#loanedStockTable tbody tr.no-record").remove();
        }
    });

    $("#searchForm").on("submit", function(e) {
        e.preventDefault();
        $("#searchInput").trigger("keyup");
        return false;
    });

    $("#clearSearch").on("click", function() {
        $("#searchInput").val('');
        $(this).closest("form").submit();
    });

    /* ======================= OUT Button ======================= */
    $('.btn-out').on('click', function(){
        var stockId = $(this).data('stock-id');
        $("#stock_id").val(stockId);

        var operationModalEl = document.getElementById('operationModal');
        var operationModal = new bootstrap.Modal(operationModalEl);
        
        // Show the modal with default = OUT
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#force_operation_type").val('');
        $("input[name='operation_type'][value='OUT']").prop('checked', true);

        // Clear remarks
        $("#operationRemark").val('');
        
        // Hide the loan_period container for OUT
        $("#loan_period_container").hide();
        $("#loan_period").prop('required', false);

        // Show extra out fields for Ticket and Manual Requisition
        $("#outExtraFields").show();

        operationModal.show();
    });

    /* ======================= Return Button ======================= */
    $(document).on('click', '.btn-return', function(){
        var stockId = $(this).data('stock-id');
        $("#stock_id").val(stockId);

        var operationModalEl = document.getElementById('operationModal');
        var operationModal = new bootstrap.Modal(operationModalEl);
        
        $("#operationModalLabel").text("Confirm Return");
        $("#operationTypeContainer").hide();
        $("#force_operation_type").val('RETURN');

        // For Return, show the same 2×2 layout but remove "required" from its inputs
        $("#loanFields").show();
        $("#loanFields input[required], #loanFields textarea[required]").prop('required', false);

        // Hide extra out fields on return
        $("#outExtraFields").hide();

        // Clear remarks
        $("#operationRemark").val('');

        operationModal.show();
    });

    /* ======================= Operation Type Radio Change ======================= */
    $("input[name='operation_type']").on("change", function(){
        if ($(this).val() === 'LOAN') {
            $("#loanFields").show();
            $("#loan_period_container").show();
            $("#loan_period").prop('required', true);
            $("#taken_by").prop('required', true);
            // Hide extra out fields when LOAN is selected
            $("#outExtraFields").hide();
        } else if ($(this).val() === 'OUT') {
            $("#loanFields").show();
            $("#loan_period_container").hide();
            $("#loan_period").prop('required', false);
            $("#taken_by").prop('required', true);
            // Show extra out fields when OUT is selected
            $("#outExtraFields").show();
        } else {
            // For any other type, hide everything
            $("#loanFields").hide();
            $("#loan_period_container").hide();
            $("#loan_period").prop('required', false);
            $("#taken_by").prop('required', false);
            $("#outExtraFields").hide();
        }
    });

    /* ======================= Reset Modal on Close ======================= */
    $('#operationModal').on("hidden.bs.modal", function (e) {
        $(this).find('form')[0].reset();
        $("#loanFields").show();
        $("#loan_period_container").show();
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val('');
        $("#taken_by").prop('required', true);
        $("#loan_period").prop('required', false);
        $("#outExtraFields").hide();
    });

    /* ======================= NEW: Details Button ======================= */
    $(document).on('click', '.btn-details', function(){
        // Grab data from data-* attributes
        var reqNo    = $(this).data('requisition_no');
        var serial   = $(this).data('serial_number');
        var brand    = $(this).data('brand');
        var prodCode = $(this).data('product_code');
        var model    = $(this).data('model');
        var category = $(this).data('category');
        var remarks  = $(this).data('remarks');
        var updated  = $(this).data('updated_at');

        // Populate the #detailsModal placeholders
        $("#detailRequisitionNo").text(reqNo);
        $("#detailSerialNumber").text(serial);
        $("#detailBrand").text(brand);
        $("#detailProductCode").text(prodCode);
        $("#detailModel").text(model);
        $("#detailCategory").text(category);
        $("#detailRemarks").text(remarks);
        $("#detailUpdatedAt").text(updated);

        // Show the details modal
        var detailsModalEl = document.getElementById('detailsModal');
        var detailsModal = new bootstrap.Modal(detailsModalEl);
        detailsModal.show();
    });
});
</script>
@endsection
