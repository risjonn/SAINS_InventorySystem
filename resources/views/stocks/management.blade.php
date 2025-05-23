@extends('layouts.layout')
@section('title', 'Stock Management')
@section('content')
@php
    // 1) Try the URL
    $selectedDivision = request()->query('division');
    // 2) If no ?division=… use what we last stored (or 'all' as a last fallback)
    if (! $selectedDivision) {
        $selectedDivision = session('selectedDivision', 'all');
    }
    // 3) Persist back into the session
    session(['selectedDivision' => $selectedDivision]);
@endphp
<!-- Page Header -->
<h2 class="text-xl font-semibold mt-4">
    Stock Management
    @if($selectedDivision !== 'all')
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ optional($division)->name ?? 'Unknown' }})
        </span>
    @endif
</h2>

@if($selectedDivision === 'all')
    {{-- Yellow prompt + Division cards --}}
    <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 rounded">
        <i class="fas fa-info-circle mr-2"></i>
        Please select a specific division below (or click one of these cards) to manage its stock.
    </div>

    @php
        // Build overdue counts for each division
        $divisionOverdues = \App\Models\Division::orderBy('name')->get()->map(function ($div) {
            $overdue = \App\Models\Stock::where('division_id', $div->id)
                ->where('status', 'LOAN')
                ->get()
                ->filter(fn ($s) =>
                    \Carbon\Carbon::parse($s->loan_start_date ?? $s->updated_at)
                        ->diffInDays(now()) > ($s->loan_period ?? 90))
                ->count();

            return (object)[
                'id'    => $div->id,
                'name'  => $div->name,
                'count' => $overdue,
            ];
        });
    @endphp

    <div class="mt-6">
        <h4 class="text-lg font-semibold mb-4">Overdue Loans by Division</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($divisionOverdues as $d)
                <a href="{{ route('stocks.management',['division'=>$d->id]) }}"
                   class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">{{ $d->name }}</span>
                        <span class="text-2xl font-bold {{ $d->count>0?'text-red-600':'text-gray-400' }}">
                            {{ $d->count }}
                        </span>
                    </div>
                    @if($d->count>0)
                        <div class="mt-2 text-xs font-semibold text-red-500 uppercase">overdue</div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

@else
    {{-- Single-division UI --}}
    @if(isset($overdueLoans) && $overdueLoans->count())
        <div class="alert alert-danger mt-3">
            <strong>Attention!</strong> There are {{ $overdueLoans->count() }} overdue loan(s). Please review them immediately.
        </div>
    @endif

    <div class="d-flex align-items-center my-4">
        <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-3">IN</a>
        <a href="{{ route('reports.stock') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Generate Report</a>
    </div>

    <!-- Search + Sort -->
    <div class="row mb-4">
        <!-- Search -->
        <div class="col-md-8 mb-2 mb-md-0">
            <form id="searchForm" method="GET" action="{{ url()->current() }}">
                <div class="input-group">
                    <input class="form-control"
                           id="searchInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search stocks… (Serial Number, Model)">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <button type="button" class="btn btn-secondary ml-2" id="clearSearch">Clear</button>
                    </div>
                </div>
                <!-- Preserve other parameters -->
                @if(request()->has('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request()->has('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>
        </div>
        <!-- Sort -->
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
            <select id="sortOption" class="p-2 border rounded" style="width:250px">
                <option value="model_asc" {{ request('sort') == 'model' && request('direction') == 'asc' ? 'selected' : '' }}>Model (A-Z)</option>
                <option value="model_desc" {{ request('sort') == 'model' && request('direction') == 'desc' ? 'selected' : '' }}>Model (Z-A)</option>
                <option value="category_asc" {{ request('sort') == 'category' && request('direction') == 'asc' ? 'selected' : '' }}>Category (A-Z)</option>
                <option value="category_desc" {{ request('sort') == 'category' && request('direction') == 'desc' ? 'selected' : '' }}>Category (Z-A)</option>
                <option value="sla_category_asc" {{ request('sort') == 'sla_category' && request('direction') == 'asc' ? 'selected' : '' }}>SLA Category (A-Z)</option>
                <option value="sla_category_desc" {{ request('sort') == 'sla_category' && request('direction') == 'desc' ? 'selected' : '' }}>SLA Category (Z-A)</option>
                <option value="updated_at_asc" {{ request('sort') == 'updated_at' && request('direction') == 'asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                <option value="updated_at_desc" {{ (request('sort') == 'updated_at' && request('direction') == 'desc') || (!request('sort') && !request('direction')) ? 'selected' : '' }}>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="stockTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#availableTab">Available Stocks</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="loaned-tab" data-bs-toggle="tab" data-bs-target="#loanedTab">Loaned Stocks</button>
        </li>
    </ul>

    <div class="tab-content mt-4">
        {{-- AVAILABLE Stocks --}}
        <div class="tab-pane fade show active" id="availableTab">
            <table id="availableStockTable" class="table table-hover table-bordered mb-0">
                <thead class="bg-gray-200">
                    <tr>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Requisition No.</th>
                        <th>Requisition/ERP Number</th>
                        <th>SLA Category</th>
                        <th>Remarks</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($availableStocks as $stock)
                    <tr>
                        <td>{{ $stock->category ?? 'N/A' }}</td>
                        <td>{{ $stock->brand ?? 'N/A' }}</td>
                        <td>{{ $stock->model ?? 'N/A' }}</td>
                        <td>
                            <a href="#"
                               class="stock-detail"
                               data-serial="{{ $stock->serial_number }}"
                               data-id="{{ $stock->id }}"
                               style="text-decoration:underline;">
                              {{ $stock->serial_number }}
                            </a>
                        </td>
                        <td>{{ $stock->requisition_no ?? 'N/A' }}</td>
                        <td>{{ $stock->erp_number ?? 'N/A' }}</td>
                        <td>{{ $stock->sla_category ?? 'N/A' }}</td>
                        <td>{{ $stock->remarks ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-danger btn-out"
                                data-bs-toggle="modal"
                                data-bs-target="#operationModal"
                                data-is-bulk="0"
                                data-max-qty="1"
                                data-batch-id=""
                                data-stock-id="{{ $stock->id }}"
                                data-sla-category="{{ $stock->sla_category }}"
                            >OUT</button>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">No matching records found</td></tr>
                    @endforelse
                </tbody>
            </table>

            {!! $availableStocks->appends(request()->except('page'))->links() !!}
        </div>

        {{-- LOANED Stocks Tab --}}
        <div class="tab-pane fade" id="loanedTab">
            <table id="loanedStockTable" class="table table-bordered mt-3">
                <thead class="bg-gray-200">
                    <tr>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Requisition No.</th>
                        <th>ERP Number</th>
                        <th>Remarks</th>
                        <th>Loan Date</th>
                        <th>Taken By</th>
                        <th>Return By</th>
                        <th>Working Days Left</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($combinedLoanedStocks as $item)
                        @php
                            $start = $item->loan_start_date ? 
                                \Carbon\Carbon::parse($item->loan_start_date) : 
                                \Carbon\Carbon::parse($item->updated_at);

                            $period = $item->loan_period ?? 90;
                            $dueDate = $start->copy()->addWeekdays($period - 1);
                            $daysLeft = \Carbon\Carbon::now()->diffInWeekdays($dueDate, false);

                            // Clean up remarks (remove return by info if embedded)
                            $pureRemarks = $item->remarks;
                            if ($item->remarks && preg_match('/\s*\(Taken by:.*\)$/', $item->remarks)) {
                                $pureRemarks = preg_replace('/\s*\(Taken by:.*\)$/', '', $item->remarks) ?: 'N/A';
                            }
                        @endphp
                        <tr @if($daysLeft < 0) class="bg-red-100 text-red-700" @endif>
                            <td>{{ $item->category ?? 'N/A' }}</td>
                            <td>{{ $item->brand ?? 'N/A' }}</td>
                            <td>{{ $item->model ?? 'N/A' }}</td>
                            <td>
                                <a href="#"
                                   class="stock-detail"
                                   data-serial="{{ $item->serial_number }}"
                                   data-id="{{ $item->id }}"
                                   style="text-decoration:underline;">
                                  {{ $item->serial_number }}
                                </a>
                            </td>
                            <td>{{ $item->requisition_no ?? 'N/A' }}</td>
                            <td>{{ $item->erp_number ?? 'N/A' }}</td>
                            <td>{{ $pureRemarks ?? 'N/A' }}</td>
                            <td>{{ $start->format('Y-m-d') }}</td>
                            <td>{{ $item->taken_by ?? 'N/A' }}</td>
                            <td>{{ $dueDate->format('Y-m-d') }}</td>
                            <td>
                                @if($daysLeft > 0)
                                    {{ $daysLeft }} days left
                                @elseif($daysLeft === 0)
                                    Expires today
                                @else
                                    <span class="text-danger font-weight-bold">{{ abs($daysLeft) }} days overdue</span>
                                @endif
                            </td>
                            <td>
                                <button 
                                    class="btn btn-primary btn-sm btn-return" 
                                    data-stock-id="{{ $item->id }}"
                                    data-is-bulk="0"
                                >Return</button>
                                <button class="btn btn-warning btn-sm btn-extend"
                                        data-stock-id="{{ $item->id }}"
                                        data-is-bulk="0"
                                        data-collection-note="{{ $item->collection_note ?? 'N/A' }}"
                                        data-requisition-no="{{ $item->requisition_no ?? 'N/A' }}"
                                        data-transaction-id="{{ $item->transaction_id ?? '' }}"
                                        data-taken-by="{{ $item->taken_by ?? 'N/A' }}"
                                        data-ticket-number="{{ $item->ticket_number ?? '' }}">
                                    Extend
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center">No matching records found</td></tr>
                    @endforelse
                </tbody>
            </table>

            {!! $combinedLoanedStocks->appends(request()->except('loan_page'))->links() !!}
        </div>
    </div>
@endif
@endsection

@section('scripts')
<!-- Include jQuery and Bootstrap Bundle (for modals) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Tab and sort initialization
    var activeTab = $("#stockTabs button.nav-link.active").attr("id");
    if (activeTab === "loaned-tab") {
        $("#availableReportBtn").addClass("d-none");
        $("#loanedReportBtn").removeClass("d-none");
    } else {
        $("#availableReportBtn").removeClass("d-none");
        $("#loanedReportBtn").addClass("d-none");
    }

    // Sorting handler
    $("#sortOption").on("change", function() {
        var selected = $(this).val();
        var [field, direction] = selected.split('_');
        var url = new URL(window.location.href);
        url.searchParams.set('sort', field);
        url.searchParams.set('direction', direction);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    });

    // Search submit handler
    $("#searchForm").on("submit", function(e) {
        e.preventDefault();
        this.submit();
    });

    // Clear search button
    $("#clearSearch").on("click", function() {
        $("#searchInput").val('');
        $("#searchForm").submit();
    });

    // Pagination links - force reload
    $(document).on('click', '.pagination a', function(e){
        e.preventDefault();
        window.location.href = this.href;
    });

    // Stock detail modal load
    $(document).on("click", ".stock-detail", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var serial = $(this).data("serial");
        var payload = id ? { id: id } : { serial: serial };
        $("#reqDetailsContent").html(`
            <div class="d-flex justify-content-center align-items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
        reqModal.show();
        $.ajax({
            url: "{{ route('stocks.details') }}",
            type: "GET",
            data: payload,
            success: function(html) {
                $("#reqDetailsContent").html(html);
            },
            error: function() {
                $("#reqDetailsContent").html('<div class="alert alert-danger">Unable to load stock details.</div>');
            }
        });
    });

    // OUT button handler
    $(document).on("click", ".btn-out", function() {
        var stockId = $(this).data("stock-id");
        $("#operation_stock_id").val(stockId);
        $("#operation_batch_id").val('');
        $("#operation_is_bulk").val('0');
        $("#quantity_container").hide();
        $("#operation_quantity").prop("required", false).val('').removeAttr("max");
        $("#quantity_limit_text").text('');
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val('');
        $("#loan_period_container").show();
        $("#takenByLabel").text("Taken by");
        var modal = new bootstrap.Modal(document.getElementById("operationModal"));
        modal.show();
    });

    // RETURN button handler
    $(document).on("click", ".btn-return", function() {
        var stockId = $(this).data("stock-id");
        $("#operation_stock_id").val(stockId);
        $("#operation_batch_id").val('');
        $("#operation_is_bulk").val('0');
        $("#quantity_container").hide();
        $("#operation_quantity").prop("required", false).val('').removeAttr("max");
        $("#quantity_limit_text").text('');
        $("#operationModalLabel").text("Confirm Return");
        $("#operationTypeContainer").hide();
        $("#force_operation_type").val("RETURN");
        $("#loan_period_container").hide();
        $("#takenByLabel").text("Return by");
        var modal = new bootstrap.Modal(document.getElementById("operationModal"));
        modal.show();
    });

    // EXTEND button handler
    $(document).on("click", ".btn-extend", function() {
        var stockId = $(this).data("stock-id");
        $("#extend_stock_id").val(stockId);
        $("#extend_bulk_batch_id").val('');

        var row = $(this).closest('tr');
        $("#extend_manual_date").val(row.find('td').eq(7).text().trim());
        $("#extend_collection_note").val($(this).data('collection-note') || '');
        $("#extend_return_by").val(row.find('td').eq(8).text().trim());
        $("#extend_ticket_number").val($(this).data('ticket-number') || '');
        $("#extend_requisition").val($(this).data('requisition-no') || '');

        $("#extend_manual_date").prop('readonly', true);
        $("#extend_collection_note").prop('readonly', true);
        $("#extend_return_by").prop('readonly', true);
        $("#extend_ticket_number").prop('readonly', true);
        $("#extend_requisition").prop('readonly', true);

        $("#extension_days").val('');
        $("#extend_remarks").val('');

        var modal = new bootstrap.Modal(document.getElementById("extendModal"));
        modal.show();
    });

    // Operation type change
    $("input[name='operation_type']").on("change", function() {
        var val = $(this).val();
        if (val === "LOAN") {
            $("#loan_period_container").show();
            $("#loan_period").prop("required", true);
            $("#taken_by").prop("required", true);
        } else if (val === "OUT") {
            $("#loan_period_container").hide();
            $("#loan_period").prop("required", false);
            $("#taken_by").prop("required", true);
        } else {
            $("#loan_period_container").hide();
            $("#loan_period").prop("required", false);
            $("#taken_by").prop("required", false);
        }
        $("#ticket_number").closest(".row").show();
    });

    // Reset operation modal on hide
    $("#operationModal").on("hidden.bs.modal", function() {
        $(this).find("form")[0].reset();
        $("#loan_period_container").show();
        $("#operationModalLabel").text("Confirm Transaction Details");
        $("#operationTypeContainer").show();
        $("#force_operation_type").val("");
        $("#taken_by").prop("required", true);
        $("#loan_period").prop("required", false);
        $("#ticket_number").closest(".row").show();
        $("#takenByLabel").text("Taken by");
        $("#operation_quantity").val('');
    });
});
</script>
@endsection

{{-- Operation Modal (OUT / LOAN / RETURN) --}}
@if($selectedDivision !== 'all')
<div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
            @csrf
            <input type="hidden" name="stock_id" id="operation_stock_id">
            <input type="hidden" name="batch_id" id="operation_batch_id">
            <input type="hidden" name="force_operation_type" id="force_operation_type">

            <div class="modal-content shadow-lg rounded-lg">
                <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                    <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- any validation errors -->
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
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                            <label class="btn btn-out-custom" for="btn-out">OUT</label>

                            <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                            <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>

                            <input type="radio" class="btn-check" name="operation_type" id="btn-return" autocomplete="off" value="RETURN">
                            <label class="btn btn-return-custom" for="btn-return">RETURN</label>
                        </div>
                    </div>

                    <!-- Transaction Date & Delivery -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                            <input type="date" name="manual_date" id="manual_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="collection_note" class="form-label fw-bold">Collection Note / Service Voucher</label>
                            <input type="text" name="collection_note" id="collection_note" class="form-control" placeholder="Enter collection note / service voucher">
                        </div>
                    </div>

                    <!-- Taken By & Ticket Number -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label id="takenByLabel" for="taken_by" class="form-label fw-bold">Taken by</label>
                            <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ticket_number" class="form-label fw-bold">Ticket Number</label>
                            <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number">
                        </div>
                    </div>

                    <!-- Requisition Number & Loan Period -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manual_requisition" class="form-label fw-bold">Requisition Number</label>
                            <input type="text" name="manual_requisition" id="manual_requisition" class="form-control" placeholder="Enter requisition number">
                        </div>

                        <div class="col-md-6" id="loan_period_container">
                            <label for="loan_period" class="form-label fw-bold">Loan Period (days)</label>
                            <input type="number" name="loan_period" id="loan_period" class="form-control" min="1" max="90" placeholder="Loan period">
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-3">
                        <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
