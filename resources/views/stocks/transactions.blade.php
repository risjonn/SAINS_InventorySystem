@extends('layouts.layout')

@section('title', 'Stock Summary')

@section('styles')
<!-- DataTables CSS (if you haven't already) -->
<link 
    rel="stylesheet" 
    href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css"
/>
<!-- DataTables Buttons CSS -->
<link 
    rel="stylesheet" 
    href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"
/>
@endsection

@section('content')
<div class="container my-5">
    @php 
        // Assume the user has selected a specific division
        $selectedDivision = session('selectedDivision');
        // Determine current filter (default to 'IN' if none provided)
        $currentFilter = request('filter', 'IN');
    @endphp

    <!-- Page Header -->
    <h2 class="text-center font-semibold mb-4">
        Stock Summary
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }})
        </span>
    </h2>

    <!-- Tabbed Filter Interface (Default filter: IN) -->
    <div class="my-4">
        <ul class="flex border-b">
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'IN']) }}"
                   class="inline-block border-l border-t border-r rounded-t px-4 py-2 
                   {{ $currentFilter=='IN' ? 'text-white font-bold bg-green-600' : 'text-green-800 font-bold bg-green-100' }}">
                    IN
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'OUT']) }}"
                   class="inline-block border-l border-t border-r rounded-t px-4 py-2 
                   {{ $currentFilter=='OUT' ? 'text-white font-bold bg-red-600' : 'text-red-800 font-bold bg-red-100' }}">
                    OUT
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'LOAN']) }}"
                   class="inline-block border-l border-t border-r rounded-t px-4 py-2 
                   {{ $currentFilter=='LOAN' ? 'text-white font-bold bg-yellow-600' : 'text-yellow-800 font-bold bg-yellow-100' }}">
                    LOAN
                </a>
            </li>
        </ul>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <div class="col-md-8 mb-2 mb-md-0">
            <form method="GET" action="{{ route('stocks.transactions') }}">
                <div class="input-group">
                    <input type="text" id="searchInput" name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search transactions... (Serial Number, Model, Requisition No.)"
                           class="form-control">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <button type="button" class="btn btn-secondary" id="clearSearch">Clear</button>
                    </div>
                </div>
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
            </form>
        </div>
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
            <select id="sortOption" class="p-2 border rounded" style="width: 250px;">
                <option value="model_asc">Model (A-Z)</option>
                <option value="model_desc">Model (Z-A)</option>
                <option value="transaction_date_asc">Transaction Date (Oldest)</option>
                <option value="transaction_date_desc" selected>Transaction Date (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table for Stock Transactions -->
    @if($currentFilter === 'IN')
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Serial / Quantity</th>
                    <th class="p-3">Product Code</th>
                    <th class="p-3">Requisition No.</th>
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    @php 
                     $cat    = strtolower($transaction->product_category);
                     $isBulk = in_array($cat, $bulkList);
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $transaction->product_category }}</td>
                        <td class="p-3">{{ $transaction->product_brand }}</td>
                        <td class="p-3">{{ $transaction->product_model }}</td>
                        <td class="p-3">
                            <a href="#"
                               class="stock-detail"
                               data-id="{{ $transaction->stock_id }}"
                               data-stock-id="{{ $transaction->stock_id }}"
                               data-serial="{{ $transaction->serial_number }}"
                               style="text-decoration:underline;">
                                {{ $isBulk ? $transaction->trans_qty : $transaction->serial_number }}
                            </a>
                        </td>
                        <td class="p-3">{{ $transaction->product_code }}</td>
                        <td class="p-3">{{ $transaction->requisition_no }}</td>
                        <td class="p-3">{{ $transaction->remarks }}</td>
                        <td class="p-3">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)
                                             ->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Serial / Quantity</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Type (OUT/LOAN)</th>
                    <th class="p-3">{{ in_array($currentFilter, ['OUT','LOAN']) ? 'Taken by' : 'Handled by' }}</th>
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                    @if($selectedDivision === 'all')
                        <th class="p-3">Division</th>
                    @endif
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    @php
                        $cat = strtolower($transaction->product_category ?? '');
                        $isBulk = in_array($cat, $bulkList);
                        $fullRemarks = $transaction->remarks ?? '';
                        $pattern = '/\(Taken by:\s*(.*?)\)/i';
                        $matches = [];
                        $takenByValue = '';
                        $displayRemarks = $fullRemarks;
                        if (in_array($transaction->type, ['LOAN','OUT'])) {
                            if (preg_match($pattern, $fullRemarks, $matches)) {
                                $takenByValue = $matches[1];
                            } else {
                                $takenByValue = 'N/A';
                            }
                            $displayRemarks = trim(preg_replace($pattern, '', $fullRemarks));
                        }
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $transaction->product_category ?? 'N/A' }}</td>
                        <td class="p-3">{{ $transaction->product_brand ?? 'N/A' }}</td>
                        <td class="p-3">
                            <a href="#"
                               class="stock-detail"
                               data-id="{{ $transaction->stock_id }}"
                               data-stock-id="{{ $transaction->stock_id }}"
                               data-serial="{{ $transaction->serial_number }}"
                               style="text-decoration:underline;">
                                {{ $isBulk ? $transaction->trans_qty : $transaction->serial_number }}
                            </a>
                        </td>
                        <td class="p-3">{{ $transaction->product_model ?? 'N/A' }}</td>
                        <td class="p-3 font-bold {{ $transaction->type=='IN'?'text-green-600':'text-red-600' }}">
                            {{ $transaction->type }}
                        </td>
                        <td class="p-3">{{ in_array($transaction->type, ['LOAN','OUT']) ? $takenByValue : $transaction->handled_by_name }}</td>
                        <td class="p-3">{{ $displayRemarks }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}</td>
                        @if($selectedDivision === 'all')
                            <td class="p-3">{{ $transaction->division_name ?? 'N/A' }}</td>
                        @endif
                        <td class="p-3">
                            @if($transaction->type === 'OUT')
                                <button 
                                    class="btn btn-sm btn-secondary btn-revert-out" 
                                    data-id="{{ $transaction->id }}"
                                >
                                    Revert OUT
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $selectedDivision==='all' ? 10 : 9 }}" class="p-4 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $transactions->appends(request()->all())->links() }}
    </div>
</div>
@endsection

{{-- Stock Details Modal --}}
<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-xl rounded-lg overflow-hidden">
            <div class="modal-header p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <h5 class="modal-title" id="reqModalLabel">
                    <i class="fas fa-info-circle me-2"></i> Stock Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div id="reqDetailsContent" class="modal-body">
                <!-- AJAX content -->
            </div>
            <div class="modal-footer bg-gray-100 p-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Revert OUT Modal --}}
<div class="modal fade" id="revertOutModal" tabindex="-1" aria-labelledby="revertOutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="revertOutForm" method="POST" action="{{ route('stocks.revertOut') }}">
            @csrf
            <input type="hidden" name="transaction_id" id="revert_transaction_id">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="revertOutModalLabel">Revert OUT Operation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="revert_remarks" class="form-label fw-bold">Revert Remark</label>
                        <textarea 
                            name="remarks" 
                            id="revert_remarks" 
                            class="form-control" 
                            rows="3" 
                            placeholder="Enter 5–10 words explaining why you're reverting this OUT..."
                            required
                        ></textarea>
                        <div class="invalid-feedback">
                            Please enter between 5 and 10 words.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary">Submit Revert</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<!-- 1) jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- 2) DataTables core + Buttons -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function(){
    /* ===================== Sorting Functionality ===================== */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false };

        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 1;
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 1;
                config.order = "desc";
                break;
            case "transaction_date_asc":
                config.columnIndex = $("#transactionsTable thead th").length - 1;
                config.order = "asc";
                config.isDate = true;
                break;
            case "transaction_date_desc":
                config.columnIndex = $("#transactionsTable thead th").length - 1;
                config.order = "desc";
                config.isDate = true;
                break;
        }
        sortTable(config.columnIndex, config.order, config.isDate);
    }

    function sortTable(columnIndex, order, isDate) {
        var rows = $("#transactionsTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex).text().trim().toUpperCase();
            var B = $(b).children("td").eq(columnIndex).text().trim().toUpperCase();
            if (isDate) {
                A = Date.parse(A) || 0;
                B = Date.parse(B) || 0;
            }
            return order === "asc"
                ? (A < B ? -1 : (A > B ? 1 : 0))
                : (A > B ? -1 : (A < B ? 1 : 0));
        });
        $.each(rows, function(index, row) {
            $("#transactionsTable tbody").append(row);
        });
    }

    $("#sortOption").on("change", sortTableByOption).trigger("change");

    /* ===================== Clear Search ===================== */
    $("#clearSearch").on("click", function() {
        $("#searchInput").val('');
        $(this).closest("form").submit();
    });

    /* ===================== Stock Details Modal Handler ===================== */
    $(document).on('click', '.stock-detail', function(e) {
        e.preventDefault();

        const stockId = $(this).data('stock-id') || $(this).data('id');
        const serial  = $(this).data('serial');

        $('#reqDetailsContent').html(`
            <div class="d-flex justify-content-center align-items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        new bootstrap.Modal($('#reqModal')).show();

        const payload = {};
        if (serial)  payload.serial = serial;
        if (stockId) payload.id     = stockId;

        $.ajax({
            url: "{{ route('stocks.details') }}",
            type: "GET",
            data: payload,
            success(response) {
                $('#reqDetailsContent').html(response);
            },
            error(xhr) {
                const msg = xhr.responseJSON?.message || 'Unknown error';
                $('#reqDetailsContent').html(`
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Unable to load stock details</h5>
                        <p>${msg}</p>
                    </div>
                `);
            }
        });
    });

    /* ===================== DataTables Configuration ===================== */
    $('#transactionsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        destroy: true,
        retrieve: true
    });

    /* ===================== Revert OUT Button & Modal ===================== */
    $(document).on('click', '.btn-revert-out', function(){
    // populate the hidden field
    $('#revert_transaction_id').val($(this).data('id'));
    // clear & reset validation classes
    $('#revert_remarks').removeClass('is-invalid').val('');

    // correctly grab the DOM element, not the jQuery wrapper
    const modalEl = document.getElementById('revertOutModal');
    const revertModal = new bootstrap.Modal(modalEl);
    revertModal.show();
});


    $('#revertOutForm').on('submit', function(e){
        let words = $('#revert_remarks').val()
                       .trim()
                       .split(/\s+/)
                       .filter(w => w.length);
        if (words.length < 5 || words.length > 10) {
            e.preventDefault();
            $('#revert_remarks').addClass('is-invalid');
        }
    });

    $('#revert_remarks').on('input', function(){
        let c = $(this).val().trim().split(/\s+/).filter(w => w).length;
        $(this).toggleClass('is-invalid', c < 5 || c > 10);
    });
});
</script>
@endsection
