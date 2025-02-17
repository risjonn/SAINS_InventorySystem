@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php 
        // Get the selected division value from session.
        $selectedDivision = session('selectedDivision');
    @endphp

    <!-- Page Header -->
    <h2 class="text-center mb-4">
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
        <!-- "IN" Operation Button -->
        <div class="my-4">
            <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-2">IN</a>
        </div>

        <!-- Controls Row: Search on left, Sort on right -->
        <div class="row mb-4">
            <!-- Search Input (Left Column) -->
            <div class="col-md-8 mb-2 mb-md-0">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                               placeholder="Search stocks... (Serial Number, Model)" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary ml-2">Apply</button>
                            <!-- Clear Button to reset the search and page -->
                            <button type="button" class="btn btn-secondary ml-2" id="clearSearch">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Sorting Dropdown (Right Column) -->
            <div class="col-md-4 text-md-right">
                <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
                <select id="sortOption" class="p-2 border rounded" style="width: 250px;">
                    <option value="model_asc">Model (A-Z)</option>
                    <option value="model_desc">Model (Z-A)</option>
                    <option value="category_asc">Category (A-Z)</option>
                    <option value="category_desc">Category (Z-A)</option>
                    <option value="updated_at_asc">Updated At (Oldest)</option>
                    <option value="updated_at_desc" selected>Updated At (Newest)</option>
                </select>
            </div>
        </div>

        <!-- Current Stocks Table -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">Current Stocks</h3>
            @if($stocks->isNotEmpty())
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="stockTable" class="w-full border rounded-lg shadow-md bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="p-2">Serial Number</th>
                                    <th class="p-2">Model</th>
                                    <th class="p-2">Category</th>
                                    <th class="p-2">Remarks</th>
                                    <th class="p-2">Updated At</th>
                                    <th class="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                <tr class="border-b">
                                    <td class="p-2">{{ $stock->serial_number }}</td>
                                    <td class="p-2">{{ $stock->model }}</td>
                                    <td class="p-2">{{ $stock->category }}</td>
                                    <td class="p-2">{{ $stock->remarks }}</td>
                                    <td class="p-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                                    <td class="p-2">
                                        @if(empty($stock->status) || $stock->status === 'available')
                                            <button type="button" class="bg-red-600 text-white px-2 py-1 rounded btn-out" data-stock-id="{{ $stock->id }}">
                                                OUT
                                            </button>
                                        @else
                                            <span class="text-gray-500 text-sm">Removed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
                <p>No stocks available for this division.</p>
            @endif
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $stocks->appends(request()->all())->links() }}
        </div>
    @endif
</div>

@if($selectedDivision !== 'all')
<!-- Modal for OUT Operation -->
<div class="modal fade" id="outModal" tabindex="-1" aria-labelledby="outModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="outForm" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="outModalLabel">Confirm OUT Operation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="outRemark" class="form-label">Please provide a remark for this OUT operation:</label>
              <textarea name="remarks" id="outRemark" class="form-control" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Confirm OUT</button>
          </div>
        </div>
      </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Ensure Bootstrap Bundle is loaded for Modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){

    /* ---------------------------- Sorting Functionality ---------------------------- */
    function sortTableByOption() {
        var sortValue = $("#sortOption").val();
        var config = { columnIndex: 1, order: "asc", isDate: false }; // Default to Model ascending.
        switch(sortValue) {
            case "model_asc":
                config.columnIndex = 1; // Model column
                config.order = "asc";
                config.isDate = false;
                break;
            case "model_desc":
                config.columnIndex = 1;
                config.order = "desc";
                config.isDate = false;
                break;
            case "category_asc":
                config.columnIndex = 2; // Category column
                config.order = "asc";
                config.isDate = false;
                break;
            case "category_desc":
                config.columnIndex = 2;
                config.order = "desc";
                config.isDate = false;
                break;
            case "updated_at_asc":
                config.columnIndex = 4; // Updated At column
                config.order = "asc";
                config.isDate = true;
                break;
            case "updated_at_desc":
                config.columnIndex = 4;
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

    /* ---------------------------- Search Functionality ---------------------------- */
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#stockTable tbody tr").not(".no-record");

        $rows.each(function() {
            var serial = $(this).find("td:nth-child(1)").text().toLowerCase();
            var model = $(this).find("td:nth-child(2)").text().toLowerCase();
            var showRow = (serial.indexOf(value) !== -1 || model.indexOf(value) !== -1);
            $(this).toggle(showRow);
        });

        if($("#stockTable tbody tr:visible").length === 0) {
            var colspan = $("#stockTable thead tr th").length;
            $("#stockTable tbody").append('<tr class="no-record"><td colspan="'+colspan+'" class="text-center py-3">No matching records found</td></tr>');
        } else {
            $("#stockTable tbody tr.no-record").remove();
        }
    });

    // Intercept the search form submission to prevent page refresh
    $("#searchForm").on("submit", function(e) {
        e.preventDefault();
        $("#searchInput").trigger("keyup");
        return false;
    });

    // Clear button functionality: reset the search input and reload the page.
    $("#clearSearch").on("click", function() {
        // Reset the search input field and reload the page.
        $("#searchInput").val('');
        $(this).closest("form").submit(); // This will reset the form and reload the page.
    });

    /* ---------------------------- OUT Button Functionality ---------------------------- */
    $('.btn-out').on('click', function(){
        var stockId = $(this).data('stock-id');
        // Build the action URL for the out operation.
        var url = "{{ url('stocks') }}/" + stockId + "/out";
        $('#outForm').attr('action', url);

        // Show the modal using Bootstrap's modal API.
        var outModalEl = document.getElementById('outModal');
        var outModal = new bootstrap.Modal(outModalEl);
        outModal.show();
    });
});
</script>
@endsection
