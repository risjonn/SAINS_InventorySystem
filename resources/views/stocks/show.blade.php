@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php
        // Get the selected division value from the query or session.
        $selectedDivision = request()->query('division', session('selectedDivision', 'all'));
    @endphp

    <!-- Page Header -->
    <div class="mb-4 text-center">
        <h2 class="fw-bold">Stock Overview</h2>
        <p class="text-muted">
            (Division: {{ $division ? $division->name : 'All Divisions' }} | Category: {{ $category }})
        </p>
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary rounded-pill">
            Back to Overview
        </a>
    </div>

    <div class="row mb-4">
        <!-- Search Input -->
        <div class="col-md-8">
            <form id="searchForm" method="GET"
                  action="{{ route('stocks.show', ['category' => $category, 'division' => $selectedDivision]) }}">
                <input type="hidden" name="division" value="{{ $selectedDivision }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="page" id="currentPage" value="{{ request('page', 1) }}">
                <!-- Hidden sort input always set -->
                <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'updated_at_desc') }}">
                <div class="input-group">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                           placeholder="Search stocks... (Serial Number, Model, Division, Requisition No.)"
                           class="form-control form-control-sm">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <a href="{{ route('stocks.show', [
                            'category' => $category,
                            'division' => ($selectedDivision === 'all') ? 'all' : $selectedDivision
                        ]) }}" class="btn btn-secondary ml-2">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sorting Dropdown -->
        <div class="col-md-4 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
            <label for="sortOption" class="me-2 fw-bold mb-0">Sort by:</label>
            <select id="sortOption" class="form-select form-select-sm w-50"
                    onchange="document.getElementById('sortInput').value = this.value; document.getElementById('searchForm').submit();">
                <option value="model_asc"    {{ request('sort', 'updated_at_desc') == 'model_asc' ? 'selected' : '' }}>Model (A-Z)</option>
                <option value="model_desc"   {{ request('sort', 'updated_at_desc') == 'model_desc' ? 'selected' : '' }}>Model (Z-A)</option>
                <option value="updated_at_asc" {{ request('sort', 'updated_at_desc') == 'updated_at_asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                <option value="updated_at_desc"{{ request('sort', 'updated_at_desc') == 'updated_at_desc' ? 'selected' : '' }}>Updated At (Newest)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div id="tableContainer">
        <div class="card shadow rounded">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="stockTable" class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3">Category</th>
                                <th class="text-center py-3">Brand</th>
                                <th class="text-center py-3">Model</th>
                                <th class="text-center py-3">Serial Number</th>
                                @if($selectedDivision === 'all')
                                    <th class="text-center py-3">Division</th>
                                @endif
                                <th class="text-center py-3">Requisition No.</th>
                                <th class="text-center py-3">Remarks</th>
                                <th class="text-center py-3">Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                                <tr class="border-bottom">
                                    <td class="text-center py-2">{{ $stock->category }}</td>
                                    <td class="text-center py-2">{{ $stock->brand ?? 'N/A' }}</td>
                                    <td class="text-center py-2">{{ $stock->model }}</td>
                                    <td class="text-center py-2">{{ $stock->serial_number }}</td>
                                    @if($selectedDivision === 'all')
                                        <td class="text-center py-2">{{ $stock->division->name }}</td>
                                    @endif
                                    <td class="text-center py-2">
                                        @if($stock->requisition_no)
                                            <a href="#" class="req-detail text-decoration-underline"
                                               data-req="{{ $stock->requisition_no }}"
                                               data-serial="{{ $stock->serial_number }}">
                                                {{ $stock->requisition_no }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-center py-2">{{ $stock->remarks }}</td>
                                    <td class="text-center py-2">
                                        {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedDivision === 'all' ? 8 : 7 }}" class="text-center py-3 text-muted">
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
                'search'   => request('search'),
                'division' => $selectedDivision,
                'category' => $category,
                'sort'     => request('sort', 'updated_at_desc')
            ])->links() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Optional Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Bootstrap Bundle for modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    // AJAX for Requisition details
    $(".req-detail").on("click", function(e){
        e.preventDefault();
        var reqNo = $(this).data("req");
        var serial = $(this).data("serial");

        $.ajax({
            url: "{{ route('transactions.details') }}",
            type: "GET",
            data: { requisition_no: reqNo, serial: serial },
            success: function(response){
                $("#reqDetails").html(response);
                var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
                reqModal.show();
            },
            error: function(){
                $("#reqDetails").html("<p class='text-danger'>Unable to load details.</p>");
                var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
                reqModal.show();
            }
        });
    });
});
</script>
@endsection

<!-- Modal for Requisition Details -->
<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"><!-- Larger modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reqModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reqDetails">
                Loading details...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
