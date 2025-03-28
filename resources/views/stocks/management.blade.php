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
                    <option value="sla_category_asc">SLA Category (A-Z)</option>
                    <option value="sla_category_desc">SLA Category (Z-A)</option>
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
                                <th class="text-center py-3">SLA Category</th>
                                <th class="text-center py-3">Remarks</th>
                                <th class="text-center py-3">Updated At</th>
                                <th class="text-center py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($availableStocks as $stock)
                                <tr class="border-bottom">
                                    <td class="text-center py-2">{{ optional($stock->product)->category ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ optional($stock->product)->brand ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ optional($stock->product)->model ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    <td class="text-center py-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->erp_number ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->sla_category ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->remarks }}</td>
                                    <td class="text-center py-2">
                                        {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="text-center py-2">
                                        <button type="button" class="btn btn-danger btn-out" data-stock-id="{{ $stock->id }}">
                                            OUT
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-3 text-muted">No matching records found</td>
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
                                    $fullRemarks = $stock->remarks ?? '';
                                    $pattern = '/\(Taken by:\s*(.*?)\)/i';
                                    $matches = [];
                                    $takenByValue = 'N/A';
                                    $displayRemarks = $fullRemarks;
                                    if (preg_match($pattern, $fullRemarks, $matches)) {
                                        $takenByValue = $matches[1];
                                    }
                                    $displayRemarks = trim(preg_replace($pattern, '', $fullRemarks));
                                    $loanStart  = $stock->loan_start_date ?? $stock->updated_at;
                                    $loanPeriod = $stock->loan_period ?? 90;
                                    $daysElapsed = \Carbon\Carbon::parse($loanStart)->diffInDays(now());
                                    $daysLeft    = $loanPeriod - $daysElapsed;
                                @endphp

                                <tr class="border-bottom" @if($daysLeft < 0) style="background-color: #FFECEC; color: #C00000;" @endif>
                                    <td class="text-center py-2">{{ optional($stock->product)->category ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ optional($stock->product)->brand ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ optional($stock->product)->model ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    <td class="text-center py-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->erp_number ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->remarks }}</td>
                                    <td class="text-center py-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}</td>
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

@include('stocks.partials.operation-modal')
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    /* ===================== Sorting Functionality ===================== */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false };

        switch(sortValue) {
            case "model_asc":
                // Model column is 3rd in availableStockTable
                config.columnIndex = 3;
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 3;
                config.order = "desc";
                break;
            case "category_asc":
                // Category column is 1st
                config.columnIndex = 1;
                config.order = "asc";
                break;
            case "category_desc":
                config.columnIndex = 1;
                config.order = "desc";
                break;
            case "sla_category_asc":
                // SLA Category column is 7th in availableStockTable
                config.columnIndex = 7;
                config.order = "asc";
                break;
            case "sla_category_desc":
                config.columnIndex = 7;
                config.order = "desc";
                break;
            case "updated_at_asc":
                // Updated At column is 9th (as there are 10 columns)
                config.columnIndex = 9;
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
                config.columnIndex = 9;
                config.order = "desc";
                config.isDate = true;
                break;
            default:
                config.columnIndex = 1;
                config.order = "asc";
        }
        // If sorting by SLA Category, only sort the available table (since loaned table does not include it)
        if(sortValue === "sla_category_asc" || sortValue === "sla_category_desc") {
            sortTable(config.columnIndex, config.order, config.isDate);
        } else {
            sortTable(config.columnIndex, config.order, config.isDate);
            sortLoanedTable(config.columnIndex, config.order, config.isDate);
        }
    }

    function sortTable(columnIndex, order, isDate) {
        var rows = $("#availableStockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().trim().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().trim().toUpperCase();
            if (isDate) {
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
        var rows = $("#loanedStockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().toUpperCase();
            if (isDate) {
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

    /* ===================== Search Functionality ===================== */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        
        // Client-side filter for the current page (optional)
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
        $("#searchInput").trigger("keyup");
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
        
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#force_operation_type").val('');
        $("input[name='operation_type'][value='OUT']").prop('checked', true);
        $("#operationRemark").val('');
        $("#loan_period_container").hide();
        $("#loan_period").prop('required', false);
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
        $("#loanFields").show();
        $("#loanFields input[required], #loanFields textarea[required]").prop('required', false);
        $("#outExtraFields").hide();
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
            $("#outExtraFields").hide();
        } else if ($(this).val() === 'OUT') {
            $("#loanFields").show();
            $("#loan_period_container").hide();
            $("#loan_period").prop('required', false);
            $("#taken_by").prop('required', true);
            $("#outExtraFields").show();
        } else {
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
});
</script>
@endsection

@if($selectedDivision !== 'all')
    <!-- Single Unified Operation Modal for OUT/Loan/Return Operation -->
    <div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="operationForm" method="POST" action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
                @csrf
                <input type="hidden" name="stock_id" id="stock_id">
                <input type="hidden" name="force_operation_type" id="force_operation_type" value="">
                <div class="modal-content shadow-lg rounded-lg">
                    <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                        <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Operation Type Radios -->
                        <div class="mb-3" id="operationTypeContainer">
                            <label class="form-label fw-bold">Operation Type:</label>
                            <div class="btn-group" role="group" aria-label="Operation Type">
                                <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                                <label class="btn btn-out-custom" for="btn-out">OUT</label>

                                <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                                <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>
                            </div>
                        </div>

                        <!-- Loan Fields (2×2 layout) -->
                        <div class="mb-3" id="loanFields">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                                    <input type="date" name="manual_date" id="manual_date" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="delivery_note" class="form-label fw-bold">Delivery Note</label>
                                    <input type="text" name="delivery_note" id="delivery_note" class="form-control" placeholder="Enter delivery note">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="document_number" class="form-label fw-bold">Document Number</label>
                                    <input type="text" name="document_number" id="document_number" class="form-control" placeholder="Enter document number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="taken_by" class="form-label fw-bold">Taken by</label>
                                    <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                                </div>
                                <div class="col-md-6 mb-3" id="loan_period_container">
                                    <label for="loan_period" class="form-label fw-bold">Loan Period (days)</label>
                                    <input type="number" name="loan_period" id="loan_period" class="form-control" placeholder="Enter loan period (1-90 days)" min="1" max="90">
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                            <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                        </div>

                        <!-- Extra Fields for OUT Transactions Only -->
                        <div class="row" id="outExtraFields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="ticket_number" class="form-label fw-bold">Ticket Number</label>
                                <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="manual_requisition" class="form-label fw-bold"> Requisition Number</label>
                                <input type="text" name="manual_requisition" id="manual_requisition" class="form-control" placeholder="Enter requisition number">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- Cancel and Submit -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
