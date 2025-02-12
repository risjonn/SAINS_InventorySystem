@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division value (default to 'all')
        $selectedDivision = request()->query('division', 'all');
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
        Stock Overview
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }})
        </span>
    </h2>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <!-- Row for Search and Sorting Options -->
    <div class="row mb-4">
        <!-- Search Input (Left Column: 8 columns) -->
        <div class="col-md-8 mb-2 mb-md-0">
            <input type="text" id="searchInput" placeholder="Search stocks... (Serial Number, Model, Division)" class="form-control">
        </div>
        <!-- Sorting Dropdown (Right Column: 4 columns, right-aligned) -->
        <div class="col-md-4 text-md-right">
            <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
            <select id="sortOption" class="p-2 border rounded" style="width: 250px;">
                <option value="model_asc">Model (A-Z)</option>
                <option value="model_desc">Model (Z-A)</option>
                <option value="updated_at_asc">Updated At (Oldest)</option>
                <option value="updated_at_desc" selected>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table Container (no division header above the table) -->
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
                        @foreach($stocks as $stock)
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    var allDivisions = @json($selectedDivision === 'all'); // Check if "All Divisions" is selected

    // Set the placeholder dynamically based on the division selection
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
        var config = { columnIndex: 1, order: "asc", isDate: false }; // Default to Model ascending.

        // Sort based on the dropdown value
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
            $("#stockTable tbody").append(row);
        });
    }

    // Trigger sorting when the dropdown selection changes.
    $("#sortOption").on("change", function() {
        sortTableByOption();
    });

    // Trigger default sorting on page load.
    $("#sortOption").trigger("change");

    /* =======================
       Search Filter Functionality
       ======================= */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#stockTable tbody tr").not(".no-record");

        $rows.each(function() {
            var serial = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model = $(this).find("td:nth-child(2)").text().toLowerCase();
            var divisionText = "";
            if(allDivisions) {
                // If all divisions are selected, Division column is the 4th column.
                divisionText = $(this).find("td:nth-child(4)").text().toLowerCase();
            }
            var showRow = (serial.indexOf(value) === 0 || model.indexOf(value) === 0 || (allDivisions && divisionText.indexOf(value) === 0));
            $(this).toggle(showRow);
        });

        if($("#stockTable tbody tr:visible").length === 0) {
            var colspan = $("#stockTable thead tr th").length;
            $("#stockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        } else {
            $("#stockTable tbody tr.no-record").remove();
        }
    });
});

</script>
@endsection
 