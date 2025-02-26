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
                    @if($availableStocks->isNotEmpty())
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="availableStockTable" class="w-full border rounded-lg shadow-md bg-white">
                                    <thead>
                                        <tr class="bg-gray-200 text-left">
                                            <th class="p-2">Serial Number</th>
                                            <th class="p-2">Brand</th>
                                            <th class="p-2">Model</th>
                                            <th class="p-2">Category</th>
                                            <th class="p-2">Product Code</th>
                                            <th class="p-2">Requisition No.</th>
                                            <th class="p-2">Remarks</th>
                                            <th class="p-2">Updated At</th>
                                            <th class="p-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableStocks as $stock)
                                        <tr class="border-b">
                                            <td class="p-2">{{ $stock->serial_number }}</td>
                                            <td class="p-2">{{ $stock->brand ?? 'N/A' }}</td>
                                            <td class="p-2">{{ $stock->model }}</td>
                                            <td class="p-2">{{ $stock->category }}</td>
                                            <td class="p-2">{{ $stock->product_code ?? 'N/A' }}</td>
                                            <td class="p-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                            <td class="p-2">{{ $stock->remarks }}</td>
                                            <td class="p-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                                            <td class="p-2">
                                                <button type="button" class="bg-red-600 text-white px-2 py-1 rounded btn-out" data-stock-id="{{ $stock->id }}">
                                                    OUT
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        {!! $availableStocks->appends(request()->all())->links('pagination::bootstrap-4') !!}
                    </div>
                    @else
                        <p>No available stocks for this division.</p>
                    @endif
                </div>
            </div>

            <!-- Loaned Stocks Pane -->
            <div class="tab-pane fade" id="loanedTab" role="tabpanel" aria-labelledby="loaned-tab">
                <div class="mt-4">
                    @if($loanedStocks->isNotEmpty())
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="loanedStockTable" class="w-full border rounded-lg shadow-md bg-white">
                                    <thead>
                                        <tr class="bg-gray-200 text-left">
                                            <th class="p-2">Serial Number</th>
                                            <th class="p-2">Brand</th>
                                            <th class="p-2">Model</th>
                                            <th class="p-2">Category</th>
                                            <th class="p-2">Product Code</th>
                                            <th class="p-2">Requisition No.</th>
                                            <th class="p-2">Taken by</th>
                                            <th class="p-2">Remarks</th>
                                            <th class="p-2">Updated At</th>
                                            <th class="p-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($loanedStocks as $stock)
                                            @php
                                                // Grab the most recent LOAN transaction for this stock
                                                $loanTx = $stock->transactions()
                                                               ->where('type', 'LOAN')
                                                               ->latest('transaction_date')
                                                               ->first();
                                                $takenByValue = 'N/A';
                                                $displayRemarks = $stock->remarks;
                                                if ($loanTx) {
                                                    $pattern = '/\(Taken by:\s*(.*?)\)/i';
                                                    $fullRemarks = $loanTx->remarks ?? '';
                                                    if (preg_match($pattern, $fullRemarks, $matches)) {
                                                        $takenByValue = $matches[1];
                                                        $displayRemarks = trim(preg_replace($pattern, '', $fullRemarks));
                                                    } else {
                                                        $displayRemarks = $fullRemarks;
                                                    }
                                                }
                                            @endphp
                                            <tr class="border-b">
                                                <td class="p-2">{{ $stock->serial_number }}</td>
                                                <td class="p-2">{{ $stock->brand ?? 'N/A' }}</td>
                                                <td class="p-2">{{ $stock->model }}</td>
                                                <td class="p-2">{{ $stock->category }}</td>
                                                <td class="p-2">{{ $stock->product_code ?? 'N/A' }}</td>
                                                <td class="p-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                                <td class="p-2">{{ $takenByValue }}</td>
                                                <td class="p-2">{{ $displayRemarks }}</td>
                                                <td class="p-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                                                <td class="p-2">
                                                    <button type="button" class="bg-blue-600 text-white px-2 py-1 rounded btn-return" data-stock-id="{{ $stock->id }}" title="Mark as Returned">
                                                        Return
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        {!! $loanedStocks->appends(request()->all())->links('pagination::bootstrap-4') !!}
                    </div>
                    @else
                        <p>No loaned stocks for this division.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@if($selectedDivision !== 'all')
<!-- Modal for OUT/Loan/Return Operation -->
<div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="operationForm" method="POST" action="{{ route('stocks.transactions.storeOutOrLoan') }}">
        @csrf
        <!-- Hidden field for Stock ID -->
        <input type="hidden" name="stock_id" id="stock_id">
        <!-- Hidden field to force operation type for returns -->
        <input type="hidden" name="force_operation_type" id="force_operation_type" value="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="operationModalLabel">Confirm Operation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Operation Type (displayed only for OUT/Loan; hidden when processing return) -->
            <div class="mb-3" id="operationTypeContainer">
                <label class="form-label">Operation Type</label>
                <div>
                    <label class="form-check-label me-2">
                        <input type="radio" name="operation_type" value="OUT" class="form-check-input" checked>
                        OUT
                    </label>
                    <label class="form-check-label">
                        <input type="radio" name="operation_type" value="LOAN" class="form-check-input">
                        Loan
                    </label>
                </div>
            </div>
            <!-- Remarks Field -->
            <div class="mb-3">
                <label for="operationRemark" class="form-label">Remarks</label>
                <textarea name="remarks" id="operationRemark" class="form-control" required></textarea>
            </div>
            <!-- Taken by Field (for BOTH OUT and LOAN) -->
            <div class="mb-3" id="loanFields" style="display: none;">
                <label for="taken_by" class="form-label">Taken by</label>
                <input type="text" name="taken_by" id="taken_by" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Confirm Operation</button>
          </div>
        </div>
      </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<!-- Include jQuery once -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Include Bootstrap Bundle for modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
        $("#operationModalLabel").text("Confirm Operation");
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
    $('.btn-return').on('click', function(){
        var stockId = $(this).data('stock-id');
        $("#stock_id").val(stockId);
        var operationModalEl = document.getElementById('operationModal');
        var operationModal = new bootstrap.Modal(operationModalEl);
        $("#operationModalLabel").text("Confirm Return");
        $("#operationTypeContainer").hide();
        $("#force_operation_type").val('RETURN');
        // Reset the remarks
        $("#operationRemark").val('');
        $("#loanFields").hide();
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
        $("#operationModalLabel").text("Confirm Operation");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val('');
    });
});
</script>
@endsection
