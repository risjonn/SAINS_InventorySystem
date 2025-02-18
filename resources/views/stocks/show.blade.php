@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division and category values from the query or session.
        $selectedDivision = request()->query('division', session('selectedDivision', 'all'));
        $selectedCategory = $category; // Use the category passed from the controller
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
        Stock Overview
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }} 
            | Category: {{ $selectedCategory }})
        </span>
    </h2>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <!-- Search Input (Left Column: 8 columns) -->
        <div class="col-md-8 mb-2 mb-md-0">
        <form id="searchForm" method="GET" action="{{ route('stocks.show', ['category' => $selectedCategory, 'division' => $selectedDivision]) }}">
    <input type="hidden" name="division" value="{{ request('division', 'all') }}">
    <input type="hidden" name="category" value="{{ $selectedCategory }}">
    <div class="input-group">
        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
               placeholder="Search stocks..." class="form-control">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary ml-2">Apply</button>
            <a href="{{ route('stocks.show', ['category' => $selectedCategory, 'division' => request('division', 'all')]) }}" 
               class="btn btn-secondary ml-2">Clear</a>
        </div>
    </div>
</form>



        </div>

        <!-- Sorting Dropdown (Right Column: 4 columns, right-aligned) -->
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
            <select id="sortOption" class="p-2 border rounded" style="width: 250px;">
                <option value="model_asc" {{ request('sort') == 'model_asc' ? 'selected' : '' }}>Model (A-Z)</option>
                <option value="model_desc" {{ request('sort') == 'model_desc' ? 'selected' : '' }}>Model (Z-A)</option>
                <option value="updated_at_asc" {{ request('sort') == 'updated_at_asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                <option value="updated_at_desc" selected {{ request('sort') == 'updated_at_desc' ? 'selected' : '' }}>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="stockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #e0e0e0; color: #333;">
                        <tr>
                            <th class="text-center py-3">Serial Number</th>
                            <th class="text-center py-3">Model</th>
                            <th class="text-center py-3">Category</th>
                            @if($selectedDivision === 'all')
                                <th class="text-center py-3">Division</th>
                            @endif
                            <th class="text-center py-3">Remarks</th>
                            <th class="text-center py-3">Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($stocks as $stock)
        <tr style="border-bottom: 1px solid #f0f0f0;">
            <td class="text-center py-2">{{ $stock->serial_number }}</td>
            <td class="text-center py-2">{{ $stock->model }}</td>
            <td class="text-center py-2">{{ $stock->category }}</td>
            @if($selectedDivision === 'all')
                <td class="text-center py-2">{{ $stock->division->name }}</td>
            @endif
            <td class="text-center py-2">{{ $stock->remarks }}</td>
            <td class="text-center py-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-3">No matching records found</td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $stocks->appends(['search' => request('search'), 'division' => request('division', 'all'), 'category' => $selectedCategory])->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    var allDivisions = @json($selectedDivision === 'all');

    // Set the placeholder dynamically based on the division selection.
    if (allDivisions) {
        $("#searchInput").attr("placeholder", "Search stocks... (Serial Number, Model, Division)");
    } else {
        $("#searchInput").attr("placeholder", "Search stocks... (Serial Number, Model)");
    }

    /* =======================
       Sorting Functionality
       ======================= */
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
            case "updated_at_asc":
                config.columnIndex = $("table thead tr th").length - 1;
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
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
        var rows = $("#stockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex).text().toUpperCase();
            if (isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc" ? (A < B ? -1 : A > B ? 1 : 0) : (A > B ? -1 : A < B ? 1 : 0);
        });
        $.each(rows, function(index, row) {
            $("#stockTable tbody").append(row);
        });
    }

    $("#sortOption").on("change", sortTableByOption).trigger("change");

    /* =======================
       Filter Table Functionality
       ======================= */
    function filterTable() {
        var value = $("#searchInput").val().toLowerCase();
        var $rows = $("#stockTable tbody tr").not(".no-record");
        $rows.each(function() {
            var serial = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model = $(this).find("td:nth-child(2)").text().toLowerCase();
            var divisionText = "";
            if (allDivisions) {
                divisionText = $(this).find("td:nth-child(4)").text().toLowerCase();
            }
            var showRow = (serial.indexOf(value) !== -1 || model.indexOf(value) !== -1 || (allDivisions && divisionText.indexOf(value) !== -1));
            $(this).toggle(showRow);
        });
        $("#stockTable tbody tr.no-record").remove();
        if ($("#stockTable tbody tr:visible").length === 0) {
            var colspan = $("#stockTable thead tr th").length;
            $("#stockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        }
    }

    $("#searchInput").on("keyup", filterTable);

    $("#clearSearch").on("click", function() {
        $("#searchInput").val('');
        $("#searchForm").submit();
    });
});
</script>
@endsection
