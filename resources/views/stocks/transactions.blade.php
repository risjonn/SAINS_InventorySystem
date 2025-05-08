@extends('layouts.layout')

@section('title', 'Stock Transaction')

@section('styles')
<!-- DataTables CSS (if you haven’t already) -->
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
        Stock Transaction
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
                   {{ request('filter', 'IN') == 'IN' ? 'text-white font-bold bg-green-600' : 'text-green-800 font-bold bg-green-100' }}">
                    IN
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'OUT']) }}"
                   class="inline-block border-l border-t border-r rounded-t px-4 py-2 
                   {{ request('filter') == 'OUT' ? 'text-white font-bold bg-red-600' : 'text-red-800 font-bold bg-red-100' }}">
                    OUT
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'LOAN']) }}"
                   class="inline-block border-l border-t border-r rounded-t px-4 py-2 
                   {{ request('filter') == 'LOAN' ? 'text-white font-bold bg-yellow-600' : 'text-yellow-800 font-bold bg-yellow-100' }}">
                    LOAN
                </a>
            </li>
        </ul>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <!-- Search Input (Left Column) -->
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
                <!-- Preserve the current filter in the query string -->
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
            </form>
        </div>

        <!-- Sorting Dropdown (Right Column) -->
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
        <!-- For IN filter, display additional columns including Product Code, Division (all), and Remarks -->
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Serial Number</th>
                    <th class="p-3">Product Code</th>
                    <th class="p-3">Requisition No.</th>
                    @if($selectedDivision === 'all')
                        <th class="p-3">Division</th>
                    @endif
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 whitespace-nowrap">{{ $transaction->product_category ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->product_brand ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->product_model ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">
                            <a href="#" class="stock-detail" data-serial="{{ $transaction->serial_number }}"
                               style="text-decoration: underline; cursor: pointer;">
                                {{ $transaction->serial_number }}
                            </a>
                        </td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->product_code ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->requisition_no ?? 'N/A' }}</td>
                        @if($selectedDivision === 'all')
                            <td class="p-3 whitespace-nowrap">{{ $transaction->division_name ?? 'N/A' }}</td>
                        @endif
                        <td class="p-3 whitespace-nowrap">{{ $transaction->remarks }}</td>
                        <td class="p-3 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="@if($selectedDivision === 'all') 9 @else 8 @endif" class="p-4 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <!-- For OUT/LOAN filters, display Division column when all -->
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Serial Number</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Type (OUT/LOAN)</th>
                    <th class="p-3">{{ in_array($currentFilter, ['OUT','LOAN']) ? 'Taken by' : 'Handled by' }}</th>
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                    @if($selectedDivision === 'all')
                        <th class="p-3">Division</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    @php
                        $fullRemarks    = $transaction->remarks ?? '';
                        $pattern        = '/\(Taken by:\s*(.*?)\)/i';
                        $matches        = [];
                        $takenByValue   = '';
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
                            <a href="#" class="stock-detail" data-serial="{{ $transaction->serial_number }}"
                               style="text-decoration: underline; cursor: pointer;">
                                {{ $transaction->serial_number }}
                            </a>
                        </td>
                        <td class="p-3">{{ $transaction->product_model ?? 'N/A' }}</td>
                        <td class="p-3 font-bold {{ $transaction->type=='IN'?'text-green-600':'text-red-600' }}">
                            {{ $transaction->type }}
                        </td>
                        <td class="p-3">
                            @if(in_array($transaction->type, ['LOAN','OUT']))
                                {{ $takenByValue }}
                            @else
                                {{ $transaction->handled_by_name }}
                            @endif
                        </td>
                        <td class="p-3">{{ $displayRemarks }}</td>
                        <td class="p-3">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
                        </td>
                        @if($selectedDivision === 'all')
                            <td class="p-3 whitespace-nowrap">{{ $transaction->division_name ?? 'N/A' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="@if($selectedDivision === 'all') 9 @else 8 @endif" class="p-4 text-center text-gray-500">
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

{{-- Insert the Stock Details modal here so your JS can open it: --}}
<!-- ====== Stock Details Modal ====== -->
<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-xl rounded-lg overflow-hidden">
      <div class="modal-header p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <h5 class="modal-title" id="reqModalLabel">
          <i class="fas fa-info-circle me-2"></i> Stock Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="reqDetailsContent" class="modal-body">
        <!-- Stock details will be loaded here via AJAX -->
      </div>
      <div class="modal-footer bg-gray-100 p-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
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

    /* ===================== Clickable Serial Number for Stock Details ===================== */
    $(document).on('click', '.stock-detail', function(e) {
        e.preventDefault();
        var serial = $(this).data('serial');

        // Show loading spinner
        $("#reqDetailsContent").html(
            '<div class="d-flex justify-content-center align-items-center p-5">'+
                '<div class="spinner-border text-primary" role="status">'+
                    '<span class="visually-hidden">Loading...</span>'+
                '</div>'+
            '</div>'
        );

        // Open modal
        var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
        reqModal.show();

        // AJAX load details
        $.ajax({
            url: "{{ route('stocks.details') }}",
            type: "GET",
            data: { serial: serial },
            success: function(response) {
                $("#reqDetailsContent").html(response);
            },
            error: function() {
                $("#reqDetailsContent").html('<div class="alert alert-danger">Unable to load stock details.</div>');
            }
        });
    });

    /* ===================== DataTables Configuration ===================== */
    var table = $('#transactionsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        destroy: true,   // Destroy any existing instance
        retrieve: true   // Retrieve existing table rather than re-creating
    });
});
</script>
@endsection
