@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division value from the query or session.
        $selectedDivision = request()->query('division', session('selectedDivision', 'all'));
    @endphp

    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="text-center">Stock Overview</h2>
        <p class="text-center text-muted">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }} 
            | Category: {{ $category }})
        </p>
    </div>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <!-- Search and Sorting -->
    <div class="row mb-4">
        <!-- Search Input -->
        <div class="col-md-8 mb-3 mb-md-0">
            <form id="searchForm" method="GET" action="{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision]) }}">
                <input type="hidden" name="division" value="{{ $selectedDivision }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="page" id="currentPage" value="{{ request('page', 1) }}">
                <div class="input-group">
                    <input 
                        type="text" 
                        id="searchInput" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search stocks... (Serial Number, Model, Division)" 
                        class="form-control form-control-lg"
                    >
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2" id="applyButton">
                            <i class="fas fa-search"></i> Apply
                        </button>
                        <a href="{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision === 'all' ? 'all' : $division->id]) }}" 
                           class="btn btn-secondary ml-1" id="clearSearch">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <!-- Sorting Dropdown -->
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-4 font-weight-bold">Sort by:</label>
            <select 
                id="sortOption" 
                name="sort" 
                class="custom-select custom-select-lg w-50"
            >
                <option value="model_asc"    {{ request('sort', 'updated_at_desc') == 'model_asc' ? 'selected' : '' }}>Model (A-Z)</option>
                <option value="model_desc"   {{ request('sort', 'updated_at_desc') == 'model_desc' ? 'selected' : '' }}>Model (Z-A)</option>
                <option value="updated_at_asc"  {{ request('sort', 'updated_at_desc') == 'updated_at_asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                <option value="updated_at_desc" {{ request('sort', 'updated_at_desc') == 'updated_at_desc' ? 'selected' : '' }}>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div id="tableContainer">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table id="stockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
    <thead style="background-color:rgb(223, 223, 223);">
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
                <td class="text-center py-2">
                    {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $selectedDivision === 'all' ? 6 : 5 }}" class="text-center py-3 text-muted">
                    No matching records found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {!! $stocks->appends([
                'search' => request('search'),
                'division' => $selectedDivision,
                'category' => $category,
                'sort' => request('sort', 'updated_at_desc')
            ])->links() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include jQuery (ensure it loads before your script) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Optional: Include Font Awesome for the search icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<script>
$(document).ready(function(){
    console.log("Custom script is loaded and jQuery is ready.");

    // Since we're now using server-side filtering,
    // the form submission will trigger a full page reload,
    // so we do not prevent the default submit event.
    // However, we still keep client-side sorting.
    
    function sortTableByOption() {
        console.log("sortTableByOption() called");
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 2, order: "asc", isDate: false };

        if (sortValue === "updated_at_asc") {
            // Assume last column is Updated At.
            config.columnIndex = $("table thead tr th").length;
            config.order = "asc";
            config.isDate = true;
        } else if (sortValue === "updated_at_desc") {
            config.columnIndex = $("table thead tr th").length;
            config.order = "desc";
            config.isDate = true;
        } else if (sortValue === "model_asc") {
            config.columnIndex = 2;
            config.order = "asc";
            config.isDate = false;
        } else if (sortValue === "model_desc") {
            config.columnIndex = 2;
            config.order = "desc";
            config.isDate = false;
        }
        sortTable(config.columnIndex, config.order, config.isDate);
    }

    function sortTable(columnIndex, order, isDate) {
        console.log("sortTable() called. columnIndex=" + columnIndex + ", order=" + order + ", isDate=" + isDate);
        var rows = $("#stockTable tbody tr").get();
        rows.sort(function(a, b) {
            var A = $(a).children("td").eq(columnIndex - 1).text().trim().toUpperCase();
            var B = $(b).children("td").eq(columnIndex - 1).text().trim().toUpperCase();
            if (isDate) {
                A = Date.parse(A) || 0;
                B = Date.parse(B) || 0;
            }
            if (A < B) return order === "asc" ? -1 : 1;
            if (A > B) return order === "asc" ? 1 : -1;
            return 0;
        });
        $.each(rows, function(index, row) {
            $("#stockTable tbody").append(row);
        });
    }

    $("#sortOption").on("change", function(){
        sortTableByOption();
    });

    // Initial sort on page load.
    sortTableByOption();
});
</script>
@endsection
