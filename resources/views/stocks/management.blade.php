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
        <!-- "IN" Operation Button -->
        <div class="my-4">
            <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-2">IN</a>
        </div>

        <!-- Row for Search and Sort Controls -->
        <div class="row mb-4">
            <!-- Search Input -->
            <div class="col-md-8 mb-2 mb-md-0">
                <form method="GET" action="{{ url()->current() }}">
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
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3">Category</th>
                                <th class="text-center py-3">Brand</th>
                                <th class="text-center py-3">Model</th>
                                <th class="text-center py-3">Serial Number</th>
                                <th class="text-center py-3">Requisition No.</th>
                                <th class="text-center py-3">Requisition/ERP Number</th>
                                <th class="text-center py-3">Remarks</th>
                                <th class="text-center py-3">Updated At</th>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3 text-muted">
                                        No matching records found
                                    </td>
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
                    <table id="loanedStockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead class="bg-light">
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loanedStocks as $stock)
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
                                    <td class="text-center py-2">{{ $stock->taken_by ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-3 text-muted">
                                        No matching records found
                                    </td>
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

@include('stocks.partials.operation-modal')

@endsection

@section('scripts')
<!-- Include jQuery once -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){

    /* ======================= Sorting Functionality ======================= */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false };
        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 3; // Column 1: Serial, 2: Brand, 3: Model.
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 3;
                config.order = "desc";
                break;
            case "category_asc":
                config.columnIndex = 4; // Column 4 is Category.
                config.order = "asc";
                break;
            case "category_desc":
                config.columnIndex = 4;
                config.order = "desc";
                break;
            case "updated_at_asc":
                config.columnIndex = 8; // For available table, Updated At is column 8.
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
                config.columnIndex = 8;
                config.order = "desc";
                config.isDate = true;
                break;
            default:
                config.columnIndex = 3;
                config.order = "asc";
        }
        sortTable(config.columnIndex, config.order, config.isDate);
        // For the loaned table, if sorting by updated_at, increase the column index by 1 (because of extra "Taken by" column)
        if(sortValue === "updated_at_asc" || sortValue === "updated_at_desc"){
            sortLoanedTable(config.columnIndex + 1, config.order, config.isDate);
        } else {
            sortLoanedTable(config.columnIndex, config.order, config.isDate);
        }
    }

    function sortTable(columnIndex, order, isDate) {
        let rows = $("#availableStockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().toUpperCase();
            if(isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc" ? (A < B ? -1 : A > B ? 1 : 0)
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
            if(isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc" ? (A < B ? -1 : A > B ? 1 : 0)
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
            var showRow = rowText.indexOf(value) !== -1;
            $(this).toggle(showRow);
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
            var showRow = rowText.indexOf(value) !== -1;
            $(this).toggle(showRow);
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

    /* ======================= OUT Button Functionality ======================= */
    $('.btn-out').on('click', function(){
        var stockId = $(this).data('stock-id');
        $("#stock_id").val(stockId);
        var operationModalEl = document.getElementById('operationModal');
        var operationModal = new bootstrap.Modal(operationModalEl);
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val('');
        // Reset the remarks and show the operation type radio
        $("#operationRemark").val('');
        $("input[name='operation_type'][value='OUT']").prop('checked', true);
        // Show the taken_by for BOTH OUT and LOAN
        $("#loanFields").show();
        operationModal.show();
    });

    /* ======================= Return Button Functionality ======================= */
    $(document).on('click', '.btn-return', function(){
        var stockId = $(this).data('stock-id');
        $("#stock_id").val(stockId);
        var operationModalEl = document.getElementById('operationModal');
        var operationModal = new bootstrap.Modal(operationModalEl);
        $("#operationModalLabel").text("Confirm Return");
        $("#operationTypeContainer").hide();
        $("#force_operation_type").val('RETURN');
        // Reset the remarks field
        $("#operationRemark").val('');
        // Hide the "taken_by" field and remove required attribute for return mode
        $("#loanFields").hide();
        $("#taken_by").prop('required', false);
        operationModal.show();
    });

    /* ======================= Modal Operation Type Change ======================= */
    $("input[name='operation_type']").on("change", function(){
        // For both OUT and LOAN, show the taken_by
        if ($(this).val() === 'LOAN' || $(this).val() === 'OUT') {
            $("#loanFields").show();
        } else {
            $("#loanFields").hide();
        }
    });

    /* ======================= Reset Modal on Close ======================= */
    $('#operationModal').on("hidden.bs.modal", function (e) {
        $(this).find('form')[0].reset();
        $("#loanFields").hide();
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val('');
        // Re-enable required attribute for taken_by (for future use in OUT/LOAN mode)
        $("#taken_by").prop('required', true);
    });
});
</script>
@endsection
