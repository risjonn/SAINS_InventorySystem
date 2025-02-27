@extends('layouts.layout')

@section('title', 'Stock Transaction')

@section('content')
<div class="container my-5">
    @php 
        // Assume the user has selected a specific division
        $selectedDivision = session('selectedDivision');
        // Determine current filter (default to 'IN' if none provided)
        $currentFilter = request('filter', 'IN');
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
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
                          {{ request('filter') == 'LOAN' ? 'text-white font-bold bg-purple-600' : 'text-purple-800 font-bold bg-purple-100' }}">
                    LOAN
                </a>
            </li>
        </ul>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <!-- Search Input (Left Column, 8 columns wide) -->
        <div class="col-md-8 mb-2 mb-md-0">
            <form method="GET" action="{{ route('stocks.transactions') }}">
                <div class="input-group">
                    <input type="text" id="searchInput" name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search transactions... (Serial Number, Model)"
                           class="form-control">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <button type="button" class="btn btn-secondary" id="clearSearch">Clear</button>
                    </div>
                </div>
                <!-- Keep the filter in the query string -->
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
        <!-- Updated table: Added "Remarks" column -->
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Serial Number</th>
                    <th class="p-3">Product Code</th>
                    <th class="p-3">Requisition No.</th>
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-b hover:bg-gray-50">
                        <!-- Notice the addition of 'whitespace-nowrap' to keep text in one line -->
                        <td class="p-3 whitespace-nowrap">{{ $transaction->category }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->brand ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->stock_model }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->serial_number }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->product_code ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->requisition_no ?? 'N/A' }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $transaction->remarks }}</td>
                        <td class="p-3 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <!-- Original columns for OUT / LOAN, or any other type -->
        <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Category</th> <!-- Added -->
                    <th class="p-3">Brand</th> <!-- Added -->
                    <th class="p-3">Serial Number</th>
                    <th class="p-3">Model</th>
                    <th class="p-3">Type (IN/OUT)</th>
                    <th class="p-3">
                        {{ in_array($currentFilter, ['OUT', 'LOAN']) ? 'Taken by' : 'Handled by' }}
                    </th>
                    <th class="p-3">Remarks</th>
                    <th class="p-3">Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    @php
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
                        <td class="p-3">{{ $transaction->category }}</td> <!-- Added -->
                        <td class="p-3">{{ $transaction->brand ?? 'N/A' }}</td> <!-- Added -->
                        <td class="p-3">{{ $transaction->serial_number }}</td>
                        <td class="p-3">{{ $transaction->stock_model }}</td>
                        <td class="p-3 font-bold {{ $transaction->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type }}
                        </td>
                        <td class="p-3">
                            @if(in_array($transaction->type, ['LOAN','OUT']))
                                {{ $takenByValue }}
                            @else
                                {{ $transaction->handled_by_name }}
                            @endif
                        </td>
                        <td class="p-3">
                            @if(in_array($transaction->type, ['LOAN','OUT']))
                                {{ $displayRemarks }}
                            @else
                                {{ $transaction->remarks }}
                            @endif
                        </td>
                        <td class="p-3">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-500">
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
                config.columnIndex = 1;
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 1;
                config.order = "desc";
                break;
            case "transaction_date_asc":
                config.columnIndex = $("table thead tr th").length - 1;
                config.order = "asc";
                config.isDate = true;
                break;
            case "transaction_date_desc":
                config.columnIndex = $("table thead tr th").length - 1;
                config.order = "desc";
                config.isDate = true;
                break;
            default:
                config.columnIndex = 1;
                config.order = "asc";
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

    $("#sortOption").on("change", function() {
        sortTableByOption();
    });
    $("#sortOption").trigger("change");

    /* ===================== Search Filter Functionality ===================== */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#transactionsTable tbody tr").not(".no-record");
        $rows.each(function() {
            var serial = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model  = $(this).find("td:nth-child(2)").text().toLowerCase();
            var showRow = (serial.indexOf(value) !== -1 || model.indexOf(value) !== -1);
            $(this).toggle(showRow);
        });
        if($("#transactionsTable tbody tr:visible").length === 0) {
            var colspan = $("#transactionsTable thead tr th").length;
            $("#transactionsTable tbody").append(
                '<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>'
            );
        } else {
            $("#transactionsTable tbody tr.no-record").remove();
        }
    });

    $("#clearSearch").on("click", function() {
        $("#searchInput").val('');
        $(this).closest("form").submit();
    });
});
</script>
@endsection
