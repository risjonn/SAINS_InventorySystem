@extends('layouts.layout')

@section('title', 'Stock Overview')

@section('content')
<div class="container my-3">
    @php
        // Get the selected division value from the query or session.
        $selectedDivision = request()->query('division', session('selectedDivision', 'all'));

        // Decide on the placeholder text dynamically
        $searchPlaceholder = ($selectedDivision === 'all')
            ? "Search stocks... (Serial Number, Model, Division, Requisition No.)"
            : "Search stocks... (Serial Number, Model, Requisition No.)";

        // Sorting logic for Brand and Model columns
        $currentSort = request('sort', 'updated_at_desc');

        // Brand sort toggles
        $brandAsc  = $currentSort === 'brand_asc';
        $nextBrand = $brandAsc ? 'brand_desc' : 'brand_asc';
        $brandIcon = $brandAsc
                        ? 'fa-sort-up'
                        : ($currentSort === 'brand_desc' ? 'fa-sort-down' : 'fa-sort');

        // Model sort toggles
        $modelAsc  = $currentSort === 'model_asc';
        $nextModel = $modelAsc ? 'model_desc' : 'model_asc';
        $modelIcon = $modelAsc
                        ? 'fa-sort-up'
                        : ($currentSort === 'model_desc' ? 'fa-sort-down' : 'fa-sort');

        // 1) Load your bulk‐category names (lowercased)
        $bulkList = \App\Models\Category::where('is_bulk', true)
                        ->pluck('name')
                        ->map(fn($n) => strtolower($n))
                        ->toArray();

        // 2) Is the current category one of those bulk categories?
        $isBulkCategory = in_array(strtolower($category), $bulkList);
    @endphp

    <!-- Page Header -->
    <div class="mb-2 text-center">
        <h2 class="fw-bold">Stock Overview</h2>
        <p class="text-muted">
            (Division: {{ $division ? $division->name : 'All Divisions' }} | Category: {{ $category }})
        </p>
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary rounded-pill">
            Back to Overview
        </a>
    </div>

    <div class="row mb-2">
        <!-- Search Input (full width) -->
        <div class="col-md-12">
            <form id="searchForm" method="GET"
                  action="{{ route('stocks.show', ['category' => $category, 'division' => $selectedDivision]) }}">
                <input type="hidden" name="division" value="{{ $selectedDivision }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="page" id="currentPage" value="{{ request('page', 1) }}">
                <!-- Preserve sort param -->
                <input type="hidden" name="sort" id="sortInput" value="{{ $currentSort }}">

                <div class="input-group">
                    <input type="text"
                           id="searchInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="{{ $searchPlaceholder }}"
                           class="form-control form-control-sm">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                        <a href="{{ route('stocks.show', [
                            'category' => $category,
                            'division' => $selectedDivision
                        ]) }}" class="btn btn-secondary ml-2">Clear</a>
                    </div>
                </div>
            </form>
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
                                {{-- Brand with sorting --}}
                                <th class="text-center py-3">
                                    <a href="#"
                                       class="text-dark text-decoration-none"
                                       onclick="event.preventDefault();
                                                document.getElementById('sortInput').value='{{ $nextBrand }}';
                                                document.getElementById('searchForm').submit();">
                                        Brand <i class="fas {{ $brandIcon }}"></i>
                                    </a>
                                </th>
                                {{-- Model with sorting --}}
                                <th class="text-center py-3">
                                    <a href="#"
                                       class="text-dark text-decoration-none"
                                       onclick="event.preventDefault();
                                                document.getElementById('sortInput').value='{{ $nextModel }}';
                                                document.getElementById('searchForm').submit();">
                                        Model <i class="fas {{ $modelIcon }}"></i>
                                    </a>
                                </th>

                                {{-- Serial vs Quantity header --}}
                                <th class="text-center py-3">
                                    {{ $isBulkCategory ? 'Quantity' : 'Serial Number' }}
                                </th>

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
            <td class="text-center py-2">{{ optional($stock->product)->category ?? 'N/A' }}</td>
            <td class="text-center py-2">{{ optional($stock->product)->brand    ?? 'N/A' }}</td>
            <td class="text-center py-2">{{ optional($stock->product)->model    ?? 'N/A' }}</td>

            {{-- Serial or Quantity --}}
            <td class="text-center py-2">
                @if($isBulkCategory)
                    {{ $stock->quantity ?? '–' }}
                @else
                    <a href="#" class="req-detail text-decoration-underline"
                       data-serial="{{ $stock->serial_number }}">
                        {{ $stock->serial_number }}
                    </a>
                @endif
            </td>

            @if($selectedDivision === 'all')
                <td class="text-center py-2">{{ $stock->division->name }}</td>
            @endif

            <td class="text-center py-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
            <td class="text-center py-2">{{ $stock->remarks            }}</td>
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
                'sort'     => $currentSort,
            ])->links() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Bootstrap Bundle for modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    $(".req-detail").on("click", function(e){
        e.preventDefault();
        var serial = $(this).data("serial");

        $("#reqDetailsContent").html(`
          <div class="d-flex justify-content-center align-items-center p-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `);

        var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
        reqModal.show();

        $.ajax({
            url: "{{ route('stocks.details') }}",
            type: "GET",
            data: { serial: serial },
            success: function(response){
                $("#reqDetailsContent").html(response);
            },
            error: function(){
                $("#reqDetailsContent").html(
                  '<div class="alert alert-danger">Unable to load transaction details.</div>'
                );
            }
        });
    });
});
</script>
@endsection

<!-- Modal for Transaction Details -->
<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-xl rounded-lg overflow-hidden">
      <div class="modal-header p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <h5 class="modal-title text-lg font-bold" id="reqModalLabel">
          <i class="fas fa-info-circle mr-2"></i> Transaction Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card border-0">
          <div class="card-body p-4" id="reqDetailsContent">
            <!-- loading spinner or AJAX response will appear here -->
          </div>
        </div>
      </div>
      <div class="modal-footer bg-gray-100 p-4">
        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
