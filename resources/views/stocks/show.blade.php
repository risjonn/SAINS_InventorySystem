@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division value from the query or session.
        $selectedDivision = request()->query('division', session('selectedDivision', 'all'));
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
        Stock Overview
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }} 
            | Category: {{ $category }})
        </span>
    </h2>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <!-- Search Input (Left Column: 8 columns) -->
        <div class="col-md-8 mb-2 mb-md-0">
            <form id="searchForm" method="GET" action="{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision === 'all' ? 'all' : ($division->id ?? 'all')]) }}">
                <input type="hidden" name="division" value="{{ $selectedDivision }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <!-- Hidden field for page number if needed -->
                <input type="hidden" name="page" id="currentPage" value="{{ request('page', 1) }}">
                <div class="input-group">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                           placeholder="Search stocks... (Serial Number, Model, Division)" class="form-control">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <a href="{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision === 'all' ? 'all' : ($division->id ?? 'all')]) }}" class="btn btn-secondary ml-2" id="clearSearch">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sorting Dropdown (Right Column: 4 columns, right-aligned) -->
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
            <select id="sortOption" name="sort" class="p-2 border rounded" style="width: 250px;">
                <option value="model_asc" {{ request('sort', 'updated_at_desc') == 'model_asc' ? 'selected' : '' }}>Model (A-Z)</option>
                <option value="model_desc" {{ request('sort', 'updated_at_desc') == 'model_desc' ? 'selected' : '' }}>Model (Z-A)</option>
                <option value="updated_at_asc" {{ request('sort', 'updated_at_desc') == 'updated_at_asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                <option value="updated_at_desc" {{ request('sort', 'updated_at_desc') == 'updated_at_desc' ? 'selected' : '' }}>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div id="tableContainer">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="stockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead style="background-color: #e0e0e0; color: #333;">
                            <tr>
                                <th class="text-center py-3">Category</th>
                                <th class="text-center py-3">Model</th>
                                <th class="text-center py-3">Serial Number</th>
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
                                    <td class="text-center py-2">{{ $stock->category }}</td>
                                    <td class="text-center py-2">{{ $stock->model }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    @if($selectedDivision === 'all')
                                        <td class="text-center py-2">{{ $stock->division->name }}</td>
                                    @endif
                                    <td class="text-center py-2">{{ $stock->remarks }}</td>
                                    <td class="text-center py-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedDivision === 'all' ? 6 : 5 }}" class="text-center py-3">No matching records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {!! $stocks->appends(['search' => request('search'), 'division' => $selectedDivision, 'category' => $category])->links() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    var allDivisions = @json($selectedDivision === 'all');

    if (allDivisions) {
        $("#searchInput").attr("placeholder", "Search stocks... (Serial Number, Model, Division)");
    } else {
        $("#searchInput").attr("placeholder", "Search stocks... (Serial Number, Model)");
    }

    // AJAX Live Search Functionality with debounce and preserving page number.
    var debounceTimer;
    function performAjaxSearch(){
        console.log("Performing AJAX search...");
        var searchQuery = $("#searchInput").val();
        var sortOption = $("#sortOption").val();
        var currentPage = $("#currentPage").val();
        $.ajax({
            url: "{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision === 'all' ? 'all' : ($division->id ?? 'all')]) }}",
            type: "GET",
            data: {
                search: searchQuery,
                sort: sortOption,
                division: "{{ $selectedDivision }}",
                category: "{{ $category }}",
                page: currentPage
            },
            cache: false,
            success: function(response) {
                var newContent = $(response).find("#tableContainer").html();
                $("#tableContainer").html(newContent);
                console.log("AJAX search successful");
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }
    
    // Intercept form submission for the Apply button.
    $("#searchForm").on("submit", function(e){
        e.preventDefault();
        performAjaxSearch();
        return false;
    });
    
    // Bind click event explicitly to the Apply button.
    $("#searchForm button[type='submit']").on("click", function(e){
        e.preventDefault();
        performAjaxSearch();
    });
    
    // Keyup with debounce for live search.
    $("#searchInput").on("keyup", function(e){
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(performAjaxSearch, 300);
    });
    
    // Trigger AJAX search on sort option change.
    $("#sortOption").on("change", function(e){
        e.preventDefault();
        performAjaxSearch();
    });
    
    // Clear button resets the search input and triggers AJAX search.
    $("#clearSearch").on("click", function(){
        $("#searchInput").val('');
        performAjaxSearch();
    });
    
    // Intercept pagination link clicks for AJAX.
    $(document).on("click", ".pagination a", function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        // Parse the page parameter from the URL.
        var params = new URLSearchParams(url.split('?')[1]);
        var page = params.get('page') || 1;
        $("#currentPage").val(page);
        $.ajax({
            url: url,
            type: "GET",
            data: {
                search: $("#searchInput").val(),
                sort: $("#sortOption").val(),
                division: "{{ $selectedDivision }}",
                category: "{{ $category }}",
                page: page
            },
            cache: false,
            success: function(response) {
                var newContent = $(response).find("#tableContainer").html();
                $("#tableContainer").html(newContent);
            },
            error: function(xhr, status, error) {
                console.error("Pagination AJAX error:", error);
            }
        });
    });

    // --- Client-side Sorting & Filtering (if needed) ---
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 2, order: "asc", isDate: false }; // Model is now the 2nd column
        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 2;
                config.order = "asc";
                break;
            case "model_desc":
                config.columnIndex = 2;
                config.order = "desc";
                break;
            case "updated_at_asc":
                config.columnIndex = $("table thead tr th").length; // Updated At is last column
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
                config.columnIndex = $("table thead tr th").length;
                config.order = "desc";
                config.isDate = true;
                break;
            default:
                config.columnIndex = 2;
                config.order = "asc";
        }
        sortTable(config.columnIndex, config.order, config.isDate);
    }

    function sortTable(columnIndex, order, isDate) {
        var rows = $("#stockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex-1).text().toUpperCase();
            var B = $(b).children("td").eq(columnIndex-1).text().toUpperCase();
            if(isDate) {
                A = new Date(A);
                B = new Date(B);
            }
            return order === "asc" ? (A < B ? -1 : A > B ? 1 : 0)
                                   : (A > B ? -1 : A < B ? 1 : 0);
        });
        $.each(rows, function(index, row) {
            $("#stockTable tbody").append(row);
        });
    }
    
    $("#sortOption").on("change", function(){
        sortTableByOption();
    });
    // Removed the automatic triggering on page load to prevent animation:
    // $("#sortOption").trigger("change");

    function filterTable() {
        var value = $("#searchInput").val().toLowerCase();
        var $rows = $("#stockTable tbody tr").not(".no-record");
        $rows.each(function() {
            // New column order: 1 - Category, 2 - Model, 3 - Serial Number, (4 - Division if applicable)
            var category = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model = $(this).find("td:nth-child(2)").text().toLowerCase();
            var serial = $(this).find("td:nth-child(3)").text().toLowerCase();
            var divisionText = "";
            if(allDivisions) {
                divisionText = $(this).find("td:nth-child(4)").text().toLowerCase();
            }
            var showRow = (category.indexOf(value) !== -1 || model.indexOf(value) !== -1 || serial.indexOf(value) !== -1 || (allDivisions && divisionText.indexOf(value) !== -1));
            $(this).toggle(showRow);
        });
        $("#stockTable tbody tr.no-record").remove();
        if ($("#stockTable tbody tr:visible").length === 0) {
            var colspan = $("#stockTable thead tr th").length;
            $("#stockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        }
    }
    
    $("#searchInput").on("keyup", function(){
        filterTable();
    });
});
</script>
@endsection
