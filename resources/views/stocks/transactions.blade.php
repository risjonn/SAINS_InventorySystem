@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division value (default to 'all')
        $selectedDivision = session('selectedDivision');
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
        Stock Transaction
        <span class="text-sm ml-2 text-gray-600">
        (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }})
        </span>
    </h2>

    <!-- Tabbed Filter Interface -->
    <div class="my-4">
        <ul class="flex border-b">
            <li class="-mb-px mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'all']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter', 'all') == 'all' ? 'text-blue-800 font-bold bg-blue-200' : 'text-gray-500 font-bold' }}">
                    All
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'IN']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter') == 'IN' ? 'text-green-800 font-bold bg-green-200' : 'text-gray-500 font-bold' }}">
                    IN
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'OUT']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter') == 'OUT' ? 'text-red-800 font-bold bg-red-200' : 'text-gray-500 font-bold' }}">
                    OUT
                </a>
            </li>
        </ul>
    </div>

    <!-- Table for Stock Transactions -->
    <table id="transactionsTable" class="w-full border rounded-lg shadow-md bg-white mt-4">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-3">Serial Number</th>
                <th class="p-3">Model</th>
                <th class="p-3">Type (IN/OUT)</th>
                <th class="p-3">Handled by</th>
                <th class="p-3">Remarks</th>
                <th class="p-3">Transaction Date</th>
                @if($selectedDivision === 'all')
                    <th class="p-3">Division</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $transaction->serial_number }}</td>
                <td class="p-3">{{ $transaction->stock_model }}</td>
                <td class="p-3 font-bold {{ $transaction->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type }}
                </td>
                <td class="p-3">{{ $transaction->handled_by_name }}</td>
                <td class="p-3">{{ $transaction->remarks }}</td>
                <td class="p-3">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}</td>
                @if($selectedDivision === 'all')
                    <td class="p-3">
                        {{ \App\Models\Division::find($transaction->division_id)->name ?? '' }}
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $selectedDivision === 'all' ? 7 : 6 }}" class="p-4 text-center text-gray-500">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

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
    // Set a flag for "All Divisions"
    var allDivisions = @json($selectedDivision === 'all');

    /* =======================================
       Sorting Functionality via Dropdown
       ======================================= */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false }; // Default to Model ascending.

        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 1;
                config.order = "asc";
                config.isDate = false;
                break;
            case "model_desc":
                config.columnIndex = 1;
                config.order = "desc";
                config.isDate = false;
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
            var A = $(a).children("td").eq(columnIndex).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex).text().toUpperCase();

            if(isDate) {
                A = new Date(A);
                B = new Date(B);
            }

            if(order === "asc") {
                return (A < B) ? -1 : (A > B) ? 1 : 0;
            } else {
                return (A > B) ? -1 : (A < B) ? 1 : 0;
            }
        });

        $.each(rows, function(index, row) {
            $("#transactionsTable tbody").append(row);
        });
    }

    $("#sortOption").on("change", function() {
        sortTableByOption();
    });
    $("#sortOption").trigger("change");

    /* =======================================
       Search Filter Functionality
       ======================================= */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#transactionsTable tbody tr").not(".no-record");

        $rows.each(function() {
            var serial = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model = $(this).find("td:nth-child(2)").text().toLowerCase();
            var divisionText = "";
            if(allDivisions) {
                // Division column is the last column.
                divisionText = $(this).find("td:last-child").text().toLowerCase();
            }
            var showRow = (serial.indexOf(value) === 0 || model.indexOf(value) === 0 || (allDivisions && divisionText.indexOf(value) === 0));
            $(this).toggle(showRow);
        });

        if($("#transactionsTable tbody tr:visible").length === 0) {
            var colspan = $("#transactionsTable thead tr th").length;
            $("#transactionsTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        } else {
            $("#transactionsTable tbody tr.no-record").remove();
        }
    });

    // Dynamically set placeholder based on the selected division
    if (allDivisions) {
        $("#searchInput").attr("placeholder", "Search transactions... (Serial Number, Model, Division)");
    } else {
        $("#searchInput").attr("placeholder", "Search transactions... (Serial Number, Model)");
    }
});
</script>
@endsection
