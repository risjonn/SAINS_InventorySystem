@extends('layouts.layout')
@section('title', 'Stock Management')
@section('content')
@php
    // 1) Try the URL
    $selectedDivision = request()->query('division');
    // 2) If no ?division=… use what we last stored (or 'all' as a last fallback)
    if (! $selectedDivision) {
        $selectedDivision = session('selectedDivision', 'all');
    }
    // 3) Persist back into the session
    session(['selectedDivision' => $selectedDivision]);
@endphp
    <!-- Page Header -->
    <h2 class="text-xl font-semibold mt-4">
        Stock Management
        @if($selectedDivision !== 'all')
            <span class="text-sm ml-2 text-gray-600">
                (Division: {{ optional($division)->name ?? 'Unknown' }})
            </span>
        @endif
    </h2>

    @if($selectedDivision === 'all')
        {{-- 3a) Yellow prompt + Division cards --}}
        <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 rounded">
            <i class="fas fa-info-circle mr-2"></i>
            Please select a specific division below (or click one of these cards) to manage its stock.
        </div>

        @php
            // Build overdue counts for each division
            $divisionOverdues = \App\Models\Division::orderBy('name')->get()->map(function ($div) {
                $overdue = \App\Models\Stock::where('division_id', $div->id)
                    ->where('status', 'LOAN')
                    ->get()
                    ->filter(fn ($s) =>
                        \Carbon\Carbon::parse($s->loan_start_date ?? $s->updated_at)
                            ->diffInDays(now()) > ($s->loan_period ?? 90))
                    ->count();

                return (object)[
                    'id'    => $div->id,
                    'name'  => $div->name,
                    'count' => $overdue,
                ];
            });
        @endphp

        <div class="mt-6">
            <h4 class="text-lg font-semibold mb-4">Overdue Loans by Division</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($divisionOverdues as $d)
                    <a href="{{ route('stocks.management',['division'=>$d->id]) }}"
                       class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700">{{ $d->name }}</span>
                            <span class="text-2xl font-bold {{ $d->count>0?'text-red-600':'text-gray-400' }}">
                                {{ $d->count }}
                            </span>
                        </div>
                        @if($d->count>0)
                            <div class="mt-2 text-xs font-semibold text-red-500 uppercase">overdue</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

    @else
        {{-- 3b) Single-division UI --}}
        @if(isset($overdueLoans) && $overdueLoans->count())
            <div class="alert alert-danger mt-3">
                <strong>Attention!</strong> There are {{ $overdueLoans->count() }} overdue loan(s). Please review them immediately.
            </div>
        @endif

        <div class="d-flex align-items-center my-4">
            <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-3">IN</a>
            <a href="{{ route('reports.stock') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Generate Report</a>
        </div>

        <!-- ░░ Search + Sort ░░ -->
        <div class="row mb-4">
            <!-- Search -->
            <div class="col-md-8 mb-2 mb-md-0">
                <form id="searchForm" method="GET" action="{{ url()->current() }}">
                    <div class="input-group">
                        <input class="form-control"
                               id="searchInput"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search stocks… (Serial Number, Model)">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary ml-2">Apply</button>
                            <button type="button" class="btn btn-secondary ml-2" id="clearSearch">Clear</button>
                        </div>
                    </div>
                    <!-- Preserve other parameters -->
                    @if(request()->has('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request()->has('direction'))
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @endif
                </form>
            </div>
            <!-- Sort -->
            <div class="col-md-4 text-md-right">
                <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
                <select id="sortOption" class="p-2 border rounded" style="width:250px">
                    <option value="model_asc" {{ request('sort') == 'model' && request('direction') == 'asc' ? 'selected' : '' }}>Model (A-Z)</option>
                    <option value="model_desc" {{ request('sort') == 'model' && request('direction') == 'desc' ? 'selected' : '' }}>Model (Z-A)</option>
                    <option value="category_asc" {{ request('sort') == 'category' && request('direction') == 'asc' ? 'selected' : '' }}>Category (A-Z)</option>
                    <option value="category_desc" {{ request('sort') == 'category' && request('direction') == 'desc' ? 'selected' : '' }}>Category (Z-A)</option>
                    <option value="sla_category_asc" {{ request('sort') == 'sla_category' && request('direction') == 'asc' ? 'selected' : '' }}>SLA Category (A-Z)</option>
                    <option value="sla_category_desc" {{ request('sort') == 'sla_category' && request('direction') == 'desc' ? 'selected' : '' }}>SLA Category (Z-A)</option>
                    <option value="updated_at_asc" {{ request('sort') == 'updated_at' && request('direction') == 'asc' ? 'selected' : '' }}>Updated At (Oldest)</option>
                    <option value="updated_at_desc" {{ (request('sort') == 'updated_at' && request('direction') == 'desc') || (!request('sort') && !request('direction')) ? 'selected' : '' }}>Updated At (Newest)</option>
                </select>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="stockTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#availableTab">Available Stocks</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="loaned-tab" data-bs-toggle="tab" data-bs-target="#loanedTab">Loaned Stocks</button>
            </li>
        </ul>

        <div class="tab-content mt-4">

    {{-- AVAILABLE Stocks --}}
<div class="tab-pane fade show active" id="availableTab">

@php
    // Get current page number
    $currentPage = request()->get('page', 1);
    
    // Only show bulk items on page 1 and when sorted by updated_at desc (newest first)
    $sortField = request('sort', 'updated_at');
    $sortDirection = request('direction', 'desc');
    $showBulkItems = ($currentPage == 1) && ($sortField == 'updated_at') && ($sortDirection == 'desc');
    
    // Combine bulk and individual items for consistent sorting
    $allAvailableItems = collect();
    
    // Add bulk items to collection ONLY on page 1 with default sorting
    if ($showBulkItems) {
        $groupedBatches = $availableBatches
            ->groupBy('product_id')
            ->map(function($batches) {
                $first = $batches->first();
                $total = $batches->sum('remaining_quantity');
                
                // Make sure we're always getting the product properties
                $product = $first->product;
                $category = $product ? $product->category : ($first->category ?? 'N/A');
                $brand = $product ? $product->brand : ($first->brand ?? 'N/A');
                $model = $product ? $product->model : ($first->model ?? 'N/A');
                
                return (object)[
                    'id'              => $first->id,
                    'product_id'      => $first->product_id,
                    'category'        => $category,
                    'brand'           => $brand,
                    'model'           => $model,
                    'quantity'        => $total,
                    'requisition_no'  => $first->manual_requisition,
                    'erp_number'      => $first->collection_note,
                    'sla_category'    => $first->sla_category,
                    'sla'             => $first->sla,
                    'remarks'         => $first->remarks,
                    'updated_at'      => $first->received_at,
                    'is_bulk'         => true
                ];
            });
    } else {
        $groupedBatches = collect(); // Empty collection if not on page 1
    }
        
    // Prepare for processing items together
    $bulkList = $bulkList ?? [];
@endphp

@if($showBulkItems && $groupedBatches->isNotEmpty())
    <div class="mt-4">
        <strong>Total Available in Bulk:</strong>
        <span class="text-xl">{{ $groupedBatches->sum('quantity') }}</span>
    </div>
@endif

<table id="availableStockTable" class="table table-hover table-bordered mb-0">
    <thead class="bg-gray-200">
        <tr>
            <th>Category</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Serial/Quantity</th>
            <th>Requisition No.</th>
            <th>Requisition/ERP Number</th>
            <th>SLA Category</th>
            <th>Remarks</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {{-- Combined display of all stock items --}}
        @php
            // Create combined collection of bulk and non-bulk items
            $combinedAvailableItems = collect();
            
            // Add bulk items ONLY if we're showing them (page 1 + default sort)
            if ($showBulkItems) {
                foreach ($groupedBatches as $batch) {
                    $combinedAvailableItems->push((object)[
                        'id' => $batch->id,
                        'product_id' => $batch->product_id,
                        'category' => $batch->category,
                        'brand' => $batch->brand,
                        'model' => $batch->model,
                        'serial_number' => null,
                        'quantity' => $batch->quantity,
                        'requisition_no' => $batch->requisition_no,
                        'erp_number' => $batch->erp_number,
                        'sla_category' => $batch->sla_category,
                        'remarks' => $batch->remarks,
                        'updated_at' => $batch->updated_at,
                        'is_bulk' => true
                    ]);
                }
            }
            
            // Add individual items from current page
            foreach ($availableStocks as $s) {
                $cat = strtolower($s->category ?? '');
                $isBulk = in_array($cat, $bulkList);
                
                if (!$isBulk) {
                    $combinedAvailableItems->push((object)[
                        'id' => $s->id,
                        'product_id' => $s->product_id ?? null,
                        'category' => $s->category,
                        'brand' => $s->brand,
                        'model' => $s->model,
                        'serial_number' => $s->serial_number,
                        'quantity' => 1,
                        'requisition_no' => $s->requisition_no,
                        'erp_number' => $s->erp_number,
                        'sla_category' => $s->sla_category,
                        'remarks' => $s->remarks,
                        'updated_at' => $s->updated_at,
                        'is_bulk' => false
                    ]);
                }
            }
            
            // Sort the combined collection
            // Note: If bulk items are shown, they should appear first (newest)
            if ($showBulkItems) {
                // When showing bulk items, sort with bulk items first, then by updated_at desc
                $sortedItems = $combinedAvailableItems->sortBy(function ($item) {
                    // Bulk items first (by making is_bulk higher priority)
                    // Then sort by updated_at descending
                    return [
                        $item->is_bulk ? 0 : 1, // Bulk items get 0 (first), non-bulk get 1
                        -strtotime($item->updated_at) // Negative timestamp for desc order
                    ];
                });
            } else {
                // Normal sorting for other pages/sorts
                if ($sortDirection === 'desc') {
                    $sortedItems = $combinedAvailableItems->sortByDesc($sortField);
                } else {
                    $sortedItems = $combinedAvailableItems->sortBy($sortField);
                }
            }
        @endphp
        
        @forelse($sortedItems as $item)
            <tr>
                <td>{{ $item->category ?? 'N/A' }}</td>
                <td>{{ $item->brand ?? 'N/A' }}</td>
                <td>{{ $item->model ?? 'N/A' }}</td>
                <td>
                    @if($item->is_bulk)
                        <a href="#"
                           class="batch-detail"
                           data-batch-id="{{ $item->product_id }}"
                           style="text-decoration:underline;">
                          {{ $item->quantity }}
                        </a>
                    @else
                        <a href="#"
                           class="stock-detail"
                           data-serial="{{ $item->serial_number }}"
                           data-id="{{ $item->id }}"
                           style="text-decoration:underline;">
                          {{ $item->serial_number }}
                        </a>
                    @endif
                </td>
                <td>{{ $item->requisition_no ?? 'N/A' }}</td>
                <td>{{ $item->erp_number ?? 'N/A' }}</td>
                <td>{{ $item->sla_category ?? 'N/A' }}</td>
                <td>{{ $item->remarks ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d H:i:s') }}</td>
                <td>
                   <button
                        type="button"
                        class="btn btn-danger btn-out"
                        data-bs-toggle="modal"
                        data-bs-target="#operationModal"
                        data-is-bulk="{{ $item->is_bulk ? '1' : '0' }}"
                        data-max-qty="{{ $item->quantity }}"
                        data-batch-id="{{ $item->is_bulk ? $item->id : '' }}"
                        data-stock-id="{{ !$item->is_bulk ? $item->id : '' }}"
                        data-sla-category="{{ $item->sla_category }}"
                    >OUT</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center">No matching records found</td></tr>
        @endforelse
    </tbody>
</table>

{!! $availableStocks->appends(request()->except('page'))->links() !!}
</div>
{{-- LOANED Stocks Tab --}}
<div class="tab-pane fade" id="loanedTab">
 

    <table id="loanedStockTable" class="table table-bordered mt-3">
        <thead class="bg-gray-200">
            <tr>
                <th>Category</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Serial/Quantity</th>
                <th>Requisition No.</th>
                <th>ERP Number</th>
                <th>Remarks</th>
                <th>Loan Date</th>
                <th>Taken By</th>
                <th>Return By</th>
                <th>Working Days Left</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($combinedLoanedStocks as $item)
                @php
                    // Ensure we're using Carbon objects for date calculations
                    $start = $item->loan_start_date ? 
                        \Carbon\Carbon::parse($item->loan_start_date) : 
                        \Carbon\Carbon::parse($item->updated_at);
                    
                    $period = $item->loan_period ?? 90;
                    $dueDate = $start->copy()->addWeekdays($period - 1);
                    $daysLeft = \Carbon\Carbon::now()->diffInWeekdays($dueDate, false);
                    
                    // Clean up remarks (remove return by info if embedded)
                    $pureRemarks = $item->remarks;
                    if (!$item->is_bulk && $item->remarks && preg_match('/\s*\(Taken by:.*\)$/', $item->remarks)) {
                        $pureRemarks = preg_replace('/\s*\(Taken by:.*\)$/', '', $item->remarks) ?: 'N/A';
                    }
                @endphp
                <tr @if($daysLeft < 0) class="bg-red-100 text-red-700" @endif>
                    <td>{{ $item->category ?? 'N/A' }}</td>
                    <td>{{ $item->brand ?? 'N/A' }}</td>
                    <td>{{ $item->model ?? 'N/A' }}</td>
                    <td>
                        @if($item->is_bulk)
                            <span class="badge badge-info">BULK</span> {{ $item->quantity }}
                        @else
                            <a href="#"
                               class="stock-detail"
                               data-serial="{{ $item->serial_number }}"
                               data-id="{{ $item->id }}"
                               style="text-decoration:underline;">
                              {{ $item->serial_number }}
                            </a>
                        @endif
                    </td>
                    <td>{{ $item->requisition_no ?? 'N/A' }}</td>
                    <td>{{ $item->erp_number ?? 'N/A' }}</td>
                    <td>{{ $pureRemarks ?? 'N/A' }}</td>
                    <td>{{ $start->format('Y-m-d') }}</td>
                    <td>{{ $item->taken_by ?? 'N/A' }}</td>
                    <td>{{ $dueDate->format('Y-m-d') }}</td>
                    <td>
                        @if($daysLeft > 0)
                            {{ $daysLeft }} days left
                        @elseif($daysLeft === 0)
                            Expires today
                        @else
                            <span class="text-danger font-weight-bold">{{ abs($daysLeft) }} days overdue</span>
                        @endif
                    </td>
   {{-- Fixed Bulk Return Form in the Loaned Stocks Tab --}}
<td>
    @if($item->is_bulk)
        {{-- Use button with modal instead of direct form submission --}}
       <button 
    class="btn btn-primary btn-sm btn-return" 
    data-stock-id="{{ $item->id }}"
    data-is-bulk="1"
    data-bulk-batch-id="{{ $item->bulk_batch_id }}"
    data-max-qty="{{ $item->quantity }}"
>
            Return
        </button>
        <button class="btn btn-warning btn-sm btn-extend" 
                data-stock-id="{{ $item->id }}"
                data-is-bulk="{{ $item->is_bulk ? '1' : '0' }}"
                data-bulk-batch-id="{{ $item->bulk_batch_id ?? '' }}"
                data-available-quantity="{{ $item->quantity }}"
                data-transaction-date="{{ \Carbon\Carbon::parse($item->loan_start_date ?? $item->updated_at)->format('Y-m-d') }}"
                data-collection-note="{{ $item->collection_note ?? 'N/A' }}"
                data-extend-by="{{ $item->taken_by ?? 'N/A' }}"
                data-transaction-id="{{ $item->transaction_id ?? '' }}"
                data-ticket-number="{{ $item->ticket_number ?? '' }}"
                data-requisition-no="{{ $item->requisition_no ?? 'N/A' }}">
            Extend
        </button>
    @else
        <button 
    class="btn btn-primary btn-sm btn-return"
    data-stock-id="{{ $item->id }}"
    data-is-bulk="0"
>
    Return
        </button>
        <button class="btn btn-warning btn-sm btn-extend"
                data-stock-id="{{ $item->id }}"
                data-is-bulk="{{ $item->is_bulk ? '1' : '0' }}"
                data-bulk-batch-id="{{ $item->bulk_batch_id ?? '' }}"
                data-available-quantity="1"
                data-collection-note="{{ $item->collection_note ?? 'N/A' }}"
                data-requisition-no="{{ $item->requisition_no ?? 'N/A' }}"
                data-transaction-id="{{ $item->transaction_id ?? '' }}"
                data-taken-by="{{ $item->taken_by ?? 'N/A' }}"
                data-ticket-number="{{ $item->ticket_number ?? '' }}">
            Extend
        </button>
    @endif
                </tr>
            @empty
                <tr><td colspan="12" class="text-center">No matching records found</td></tr>
            @endforelse
        </tbody>
    </table>

    {!! $combinedLoanedStocks->appends(request()->except('loan_page'))->links() !!}
</div>
</div>
    @endif
                </div> 
{{-- Operation Modal (OUT / LOAN / RETURN) --}}
<div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="operationForm" method="POST" action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
            @csrf
            <input type="hidden" name="stock_id" id="operation_stock_id">
            <input type="hidden" name="bulk_batch_id" id="operation_batch_id">
            <input type="hidden" name="is_bulk" id="operation_is_bulk" value="0">
            <input type="hidden" name="force_operation_type" id="force_operation_type" value="">

            <div class="modal-content shadow-lg rounded-lg">
                <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                    <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- any validation errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Operation Type Radios -->
                    <div class="mb-3" id="operationTypeContainer">
                        <label class="form-label fw-bold">Operation Type:</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                            <label class="btn btn-out-custom" for="btn-out">OUT</label>

                            <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                            <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>
                            
                            <input type="radio" class="btn-check" name="operation_type" id="btn-return" autocomplete="off" value="RETURN">
                            <label class="btn btn-return-custom" for="btn-return">RETURN</label>
                        </div>
                    </div>

                    <!-- Transaction Date & Delivery -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                            <input type="date" name="manual_date" id="manual_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="collection_note" class="form-label fw-bold">Collection Note / Service Voucher</label>
                            <input type="text" name="collection_note" id="collection_note" class="form-control" placeholder="Enter collection note / service voucher">
                        </div>
                    </div>

                    <!-- Taken By & Ticket Number -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label id="takenByLabel" for="taken_by" class="form-label fw-bold">Taken by</label>
                            <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ticket_number" class="form-label fw-bold">Ticket Number</label>
                            <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number">
                        </div>
                    </div>

                    <!-- Requisition Number & Quantity -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manual_requisition" class="form-label fw-bold">Requisition Number</label>
                            <input type="text" name="manual_requisition" id="manual_requisition" class="form-control" placeholder="Enter requisition number">
                        </div>
                   <!-- In the operationModal's quantity input section -->
<div class="col-md-6" id="quantity_container" style="display:none;">
    <label for="operation_quantity" class="form-label fw-bold">
        Quantity <span id="quantity_limit_text" class="text-muted small"></span>
    </label>
    <input
        type="number"
        name="quantity"
        id="operation_quantity"
        class="form-control"
        min="1"
        placeholder="Enter quantity"
        data-max-quantity=""
    >
    <div id="quantity_error" class="text-danger small mt-1" style="display: none;"></div>
</div>

                    <!-- Loan Period -->
                    <div class="row mb-3" id="loan_period_container">
                        <div class="col-md-6">
                            <label for="loan_period" class="form-label fw-bold">Loan Period (days)</label>
                            <input type="number" name="loan_period" id="loan_period" class="form-control" min="1" max="90" placeholder="Loan period">
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-3">
                        <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Extension Modal (Extend Loan Period) --}}
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="extendForm" method="POST" action="{{ route('stocks.extendLoan') }}">
    @csrf
    <input type="hidden" name="stock_id" id="extend_stock_id">
    <input type="hidden" name="bulk_batch_id" id="extend_bulk_batch_id">


            <div class="modal-content shadow-lg rounded-lg">
                <div class="modal-header bg-yellow-600 text-white">
                    <h5 class="modal-title" id="extendModalLabel">
                        <i class="fas fa-clock me-2"></i> Extend Loan Period
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Transaction Date --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="extend_manual_date" class="form-label fw-bold">Transaction Date</label>
                           <input type="date" name="manual_date" id="extend_manual_date" class="form-control">

                        </div>
                        <div class="col-md-6">
                            <label for="extend_collection_note" class="form-label fw-bold">Collection Note / Service Voucher</label>
                            <input type="text" name="collection_note" id="extend_collection_note" class="form-control">

                        </div>
                    </div>

                    {{-- Return by & Ticket Number --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
<label for="extend_return_by" class="form-label fw-bold">Extend by</label>
<input type="text" name="taken_by" id="extend_return_by" class="form-control" placeholder="Enter who extends">

                        </div>
                        <div class="col-md-6">
                            <label for="extend_ticket_number" class="form-label fw-bold">Ticket Number</label>
                            <input type="text" name="ticket_number" id="extend_ticket_number" class="form-control">

                        </div>
                    </div>

                    {{-- Requisition No. & Extension Days --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="extend_requisition" class="form-label fw-bold">Requisition No.</label>
                           <input type="text" id="extend_requisition" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="extension_days" class="form-label fw-bold">Extension Days</label>
                           <input type="number" name="extension_days" id="extension_days" class="form-control" min="1" max="90" placeholder="Days to add" required>

                        </div>
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label for="extend_remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="extend_remarks" class="form-control" rows="3" placeholder="Enter remarks…"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Extension</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Stock Details Modal --}}
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

{{-- Available Stock Report Modal --}}
<div class="modal fade" id="availableStockReportModal" tabindex="-1" aria-labelledby="availableStockReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-xl rounded-lg overflow-hidden">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="availableStockReportModalLabel">
                    <i class="fas fa-chart-bar me-2"></i> Available Stock Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($availableStocks->isEmpty())
                    <p class="text-center text-gray-500 mt-2">No available stocks found.</p>
                @else
                    <table class="table table-bordered mt-2">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Serial</th>
                                <th>Product Code</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableStocks as $stock)
                                <tr>
                                    <td>{{ $stock->serial_number }}</td>
                                    <td>{{ optional($stock->product)->product_code ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->model ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->brand ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->category ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="modal-footer bg-gray-100 p-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Loaned Stock Report Modal --}}
<div class="modal fade" id="loanedStockReportModal" tabindex="-1" aria-labelledby="loanedStockReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-xl rounded-lg overflow-hidden">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="loanedStockReportModalLabel">
                    <i class="fas fa-chart-bar me-2"></i> Loaned Stock Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($loanedStocks->isEmpty())
                    <p class="text-center text-gray-500 mt-2">No loaned stocks found.</p>
                @else
                    <table class="table table-bordered mt-2">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Serial</th>
                                <th>Product Code</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Loan Start Date</th>
                                <th>Loan Period</th>
                                <th>Remarks</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loanedStocks as $stock)
                                <tr>
                                    <td>{{ $stock->serial_number }}</td>
                                    <td>{{ optional($stock->product)->product_code ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->model ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->brand ?? 'N/A' }}</td>
                                    <td>{{ optional($stock->product)->category ?? 'N/A' }}</td>
                                    <td>
                                        @if($stock->loan_start_date)
                                            {{ \Carbon\Carbon::parse($stock->loan_start_date)->format('Y-m-d') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $stock->loan_period ?? 'N/A' }}</td>
                                    <td>{{ $stock->remarks }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="modal-footer bg-gray-100 p-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Include jQuery and Bootstrap Bundle (for modals) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize tabs and sorting
        initializeTabs();
        
        // Event listeners for sorting and searching
        setupSortingAndSearching();
        
        // Button handlers for OUT, RETURN, and EXTEND operations
        setupOperationHandlers();
        
        // Modal handlers
        setupModalHandlers();
        
        // Initialize bulk quantity toggle
        setupBulkQuantityToggle();
        
        // FUNCTION DEFINITIONS
        
        function initializeTabs() {
            // When Loaned Stocks tab is active on page load, force sort by Due Date
            var activeTab = $("#stockTabs button.nav-link.active").attr("id");
            if (activeTab === "loaned-tab") {
                $("#availableReportBtn").addClass("d-none");
                $("#loanedReportBtn").removeClass("d-none");
                // Sort loaned table by Due Date descending (newest due date first)
                sortLoanedTable("due_date", "desc", true);
            } else {
                $("#availableReportBtn").removeClass("d-none");
                $("#loanedReportBtn").addClass("d-none");
            }
        }
        
        function setupSortingAndSearching() {
            // Sorting handler
            $("#sortOption").on("change", function() {
                sortTableByOption();
            });
            
            // Search functionality
            $("#searchInput").on("keyup", function() {
                performSearch();
            });
            
            $("#searchForm").on("submit", function(e) {
                e.preventDefault();
                performSearch();
            });
            
            $("#clearSearch").on("click", function() {
                $("#searchInput").val('');
                performSearch();
            });
        }
        
        function setupOperationHandlers() {
            // OUT Button Handler is now handled in setupBulkQuantityToggle()
            
            // RETURN Button Handler
    $(document).on("click", ".btn-return", function() {
  const isBulk      = $(this).data("is-bulk") === 1;
  const stockId     = $(this).data("stock-id");
  const bulkBatchId = $(this).data("bulk-batch-id");
  const maxQty      = isBulk
    ? parseInt($(this).data("max-qty"), 10)
    : undefined;

  if (isBulk) {
    $("#operation_batch_id").val(bulkBatchId);
    setupOperationModal(stockId, "RETURN", true, maxQty);
  } else {
    setupOperationModal(stockId, "RETURN", false);
  }

  $("#operation_is_bulk").val(isBulk ? "1" : "0");
  $("#takenByLabel").text("Return by");
});
    // EXTEND Button Handler
    $(document).on("click", ".btn-extend", function() {
        setupExtendModal($(this));
    });
    
    // Operation Type Change Handler
    $("input[name='operation_type']").on("change", function() {
        updateOperationTypeFields($(this).val());
    });
}
        
        function setupModalHandlers() {
            // Serial-detail click
            $(document).on("click", ".stock-detail", function(e) {
                e.preventDefault();
                loadStockDetails($(this));
            });

            // Batch-detail click
            $(document).on("click", ".batch-detail", function(e) {
                e.preventDefault();
                var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
                reqModal.show();
                $("#reqDetailsContent").html(`
                    <div class="d-flex justify-content-center align-items-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                `);
                $.ajax({
                    url: "{{ route('stocks.details') }}",
                    type: "GET",
                    data: { batch_id: $(this).data("batch-id") },
                    success(html) {
                        $("#reqDetailsContent").html(html);
                    },
                    error() {
                        $("#reqDetailsContent").html(
                            '<div class="alert alert-danger">Unable to load batch details.</div>'
                        );
                    }
                });
            });
            
            // Reset modal when hidden
            $('#operationModal').on('hidden.bs.modal', function() {
                $(this).removeData('bs.modal');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                resetOperationModal();
            });
            
            // Setup fields when modal is shown
            $('#operationModal').on('show.bs.modal', function(e) {
                const isReturn = $(e.relatedTarget).hasClass('btn-return');
                if (isReturn) {
                    $('#loan_period_container').hide();
                } else {
                    const operationType = $("input[name='operation_type']:checked").val();
                    $('#loan_period_container').toggle(operationType === "LOAN");
                }
            });
        }
        
        // Fixed version of setupBulkQuantityToggle function
        function setupBulkQuantityToggle() {
            // 1) Unified OUT click: stash bulk, max-qty, and call setupOperationModal()
            $(document).on("click", ".btn-out", function() {
                const isBulk = $(this).data("is-bulk") === 1;
                const maxQty = $(this).data("max-qty");
                const batchId = isBulk ? $(this).data("batch-id") : null;
                const stockId = isBulk ? null : $(this).data("stock-id");
                
                const idToUse = isBulk ? batchId : stockId;
                setupOperationModal(idToUse, "OUT", isBulk, maxQty);
            });

            // 2) Hook up quantity validation on input
          $(document).on("input", "#operation_quantity", function() {
   const max = parseInt($(this).attr("max")) || 1;
    const val = $(this).val().trim();
    const $error = $("#quantity_error");

    // Allow empty input (user deleting to type new number)
    
    if (val === "") {
        $error.hide();
        return;
    }

    const numVal = parseInt(val);
    if (isNaN(numVal)) {
        $error.text("Please enter a valid number.").show();
        $(this).val('');
        return;
    }

    if (numVal > max) {
        $(this).val(max);
        $error.text("Maximum quantity is " + max + ".").show();
    } else if (numVal < 1) {
        $(this).val('');
        $error.text("Quantity must be at least 1.").show();
    } else {
        $error.hide();
    }
});


            // 3) On hide, clear data and reset Quantity entirely
            $("#operationModal").on("hidden.bs.modal", function() {
                $(this).removeData("is-bulk").removeData("max-qty");
                const qtyRow = document.getElementById("quantity_container");
                const qty    = document.getElementById("operation_quantity");
                if (qtyRow) {
                    qtyRow.style.display = "none";
                }
                if (qty) {
                    qty.required = false;
                    qty.value    = "";
                    qty.removeAttribute("max");
                }
            });
        }

        // Updated to handle isBulk and maxQty parameters
function setupOperationModal(stockId, operationType, isBulk = false, maxQty = 1) {
    // preserve your ID logic
    if (isBulk) {
        $("#operation_batch_id").val(stockId);
        $("#operation_stock_id").val("");
        $("#operation_is_bulk").val("1");
    } else {
        $("#operation_stock_id").val(stockId);
        $("#operation_batch_id").val("");
        $("#operation_is_bulk").val("0");
    }
    
    var operationModal = new bootstrap.Modal(document.getElementById("operationModal"));

    // ─── RESET quantity field ────────────────────────────────────────────────────
    $("#quantity_container").hide();
    $("#operation_quantity")
      .prop("required", false)
      .val("")
      .removeAttr("max");
    $("#quantity_limit_text").text("");

    // ─── BULK: OUT or LOAN (allow custom qty) ─────────────────────────────────
    if (isBulk && operationType !== "RETURN") {
        $("#quantity_container").show();
        $("#operation_quantity")
          .prop("required", true)
          .attr("max", maxQty)
          .val("");
        $("#quantity_limit_text").text("(max: " + maxQty + ")");
    }

    // ─── BULK: RETURN (default full qty) ──────────────────────────────────────
    if (isBulk && operationType === "RETURN") {
        $("#quantity_container").show();
        $("#operation_quantity")
          .prop("required", true)
          .attr("max", maxQty)
          .val(maxQty);
        $("#quantity_limit_text").text("(max: " + maxQty + ")");
    }

    // ─── NON-BULK: RETURN ───────────────────────────────────────────────────────
    // quantity stays hidden & not required

    // ─── COMMON RETURN SETUP ────────────────────────────────────────────────────
    if (operationType === "RETURN") {
        $("#operationModalLabel").text("Confirm Return");
        $("#operationTypeContainer").hide();
        $("#force_operation_type").val("RETURN");
        $("#loan_period_container").hide();
        $("#takenByLabel").text("Return by");
    }

    // preserve your remark + ticket logic
    $("#operationRemark").val("");
    $("#ticket_number").closest(".row").show();

    operationModal.show();
}


        
    function setupExtendModal(button) {
    const isBulk = button.data("is-bulk") === 1 || button.data("is-bulk") === true;
    const id = isBulk ? button.data("bulk-batch-id") : button.data("stock-id");

    if (isBulk) {
        $("#extend_bulk_batch_id").val(id);
        $("#extend_stock_id").val("");
        
    } else {
        $("#extend_stock_id").val(id);
        $("#extend_bulk_batch_id").val("");
    }

    let row;
    if (isBulk) {
        row = $(`button.btn-extend[data-bulk-batch-id='${id}']`).closest('tr');
    } else {
        row = $(`button.btn-extend[data-stock-id='${id}']`).closest('tr');
    }

    if (row.length) {
        const transactionDate = row.find('td').eq(7).text().trim();
        const collectionNote = row.find('td').eq(5).text().trim();
        const extendBy       = row.find('td').eq(8).text().trim();
        const requisitionNo  = row.find('td').eq(4).text().trim();

        // Get ticket number from button data attribute, not from table cell
        const ticketNumber = button.data("ticket-number") || '';

        $("#extend_manual_date").val(transactionDate || '');
        $("#extend_collection_note").val(collectionNote || '');
        $("#extend_return_by").val(extendBy || '');
        $("#extend_ticket_number").val(ticketNumber);
        $("#extend_requisition").val(requisitionNo || '');

        // Make fields readonly
        $("#extend_manual_date").prop('readonly', true);
        $("#extend_collection_note").prop('readonly', true);
        $("#extend_return_by").prop('readonly', true);
        $("#extend_ticket_number").prop('readonly', true);
        $("#extend_requisition").prop('readonly', true);
    } else {
        $("#extend_manual_date").val('');
        $("#extend_collection_note").val('');
        $("#extend_return_by").val('');
        $("#extend_ticket_number").val('');
        $("#extend_requisition").val('');
    }

    $("#extension_days").val('');
    $("#extend_remarks").val('');

    new bootstrap.Modal(document.getElementById("extendModal")).show();
}




        function updateOperationTypeFields(operationType) {
            if (operationType === "LOAN") {
                $("#loanFields").show();
                $("#loan_period_container").show();
                $("#loan_period").prop("required", true);
                $("#taken_by").prop("required", true);
            } else if (operationType === "OUT") {
                $("#loanFields").show();
                $("#loan_period_container").hide();
                $("#loan_period").prop("required", false);
                $("#taken_by").prop("required", true);
            } else {
                $("#loanFields").hide();
                $("#loan_period_container").hide();
                $("#loan_period").prop("required", false);
                $("#taken_by").prop("required", false);
            }
            $("#ticket_number").closest(".row").show();
        }
        
        function resetOperationModal() {
            $("#operationModal").find("form")[0].reset();
            $("#loanFields").show();
            $("#loan_period_container").show();
            $("#operationModalLabel").text("Confirm Transaction Details");
            $("#operationTypeContainer").show();
            $("#force_operation_type").val("");
            $("#taken_by").prop("required", true);
            $("#loan_period").prop("required", false);
            $("#ticket_number").closest(".row").show();
            $("#takenByLabel").text("Taken by");
            $("#operation_quantity").val('');
        }
        
        function loadStockDetails(element) {
            var id = element.data("id");
            var serial = element.data("serial");
            var payload = id ? { id: id } : { serial: serial };
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
                data: payload,
                success: function(html) {
                    $("#reqDetailsContent").html(html);
                },
                error: function() {
                    $("#reqDetailsContent").html('<div class="alert alert-danger">Unable to load stock details.</div>');
                }
            });
        }
        
        // HELPER FUNCTIONS FOR SORTING AND FILTERING
        
        function sortTableByOption() {
            var sortValue = $("#sortOption").val();
            var config = { columnIndex: 1, order: "asc", isDate: false };
            switch (sortValue) {
                case "model_asc":       config.columnIndex = 3; config.order = "asc";  break;
                case "model_desc":      config.columnIndex = 3; config.order = "desc"; break;
                case "category_asc":    config.columnIndex = 1; config.order = "asc";  break;
                case "category_desc":   config.columnIndex = 1; config.order = "desc"; break;
                case "sla_category_asc":config.columnIndex = 7; config.order = "asc";  break;
                case "sla_category_desc":config.columnIndex = 7; config.order = "desc";break;
                case "updated_at_asc":
                case "updated_at_desc":
                    if ($("#loanedTab").hasClass("active")) {
                        config.columnIndex = "due_date";
                        config.order = (sortValue==="updated_at_desc")?"desc":"asc";
                        config.isDate = true;
                    } else {
                        config.columnIndex = 9;
                        config.order = (sortValue==="updated_at_desc")?"desc":"asc";
                        config.isDate = true;
                    }
                    break;
                default:
                    config.columnIndex = 1; config.order="asc";
            }
            sortAvailableTable(config.columnIndex, config.order, config.isDate);
            sortLoanedTable(config.columnIndex, config.order, config.isDate);
        }
        
        function sortAvailableTable(columnIndex, order, isDate) {
            var rows = $("#availableStockTable tbody tr").get();
            rows.sort(function(a, b) {
                var A = $(a).children("td").eq(columnIndex-1).text().trim();
                var B = $(b).children("td").eq(columnIndex-1).text().trim();
                if (isDate) { A = parseDateTime(A); B = parseDateTime(B); }
                else { A = A.toUpperCase(); B = B.toUpperCase(); }
                return (A < B ? (order==="asc"? -1:1) : (A > B? (order==="asc"?1:-1):0));
            });
            $.each(rows, function(i,row){ $("#availableStockTable tbody").append(row) });
        }
        
        function sortLoanedTable(columnIdentifier, order, isDate) {
            var rows = $("#loanedStockTable tbody tr").get();
            rows.sort(function(a, b) {
                if (columnIdentifier==="due_date") {
                    var A=parseDateTime($(a).attr("data-duedate")), B=parseDateTime($(b).attr("data-duedate"));
                    return (order==="asc"? A-B: B-A);
                } else if (columnIdentifier===10) {
                    var A=parseFloat($(a).children("td").eq(9).attr("data-daysleft"))||0,
                        B=parseFloat($(b).children("td").eq(9).attr("data-daysleft"))||0;
                    return (order==="asc"? A-B: B-A);
                } else {
                    var A=$(a).children("td").eq(columnIdentifier-1).text().trim().toUpperCase(),
                        B=$(b).children("td").eq(columnIdentifier-1).text().trim().toUpperCase();
                    return (A < B? (order==="asc"? -1:1) : (A > B? (order==="asc"?1:-1):0));
                }
            });
            $.each(rows, function(i,row){ $("#loanedStockTable tbody").append(row) });
        }
        
        function parseDateTime(dateStr) {
            if (!dateStr) return 0;
            var parts=dateStr.split(" "), d=parts[0].split("-"), t=(parts[1]||"").split(":");
            return new Date(+d[0], d[1]-1, +d[2], +t[0]||0, +t[1]||0, +t[2]||0).getTime();
        }
        
        function performSearch() {
            var v=$("#searchInput").val().toLowerCase();
            filterTable("#availableStockTable",v);
            filterTable("#loanedStockTable",v);
        }
        
        function filterTable(sel,sv) {
            var $rows=$(sel+" tbody tr").not(".no-record");
            $rows.each(function(){
                $(this).toggle($(this).text().toLowerCase().indexOf(sv)!==-1);
            });
            if ($(sel+" tbody tr:visible").length===0) {
                var c=$(sel+" thead tr th").length;
                $(sel+" tbody tr.no-record").remove();
                $(sel+" tbody").append('<tr class="no-record"><td colspan="'+c+'" class="text-center py-3">No matching records found</td></tr>');
            } else {
                $(sel+" tbody tr.no-record").remove();
            }
        }

        // ←─── **HERE** ───←
        // Force pagination links to actually reload the page
        $(document).on('click', '.pagination a', function(e){
            e.preventDefault();
            window.location.href = this.href;
        });
    });
    </script>

    <script>
    // Your added vanilla JS code here
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sort dropdown changes
        const sortDropdown = document.getElementById('sortOption');
        
        if (sortDropdown) {
            sortDropdown.addEventListener('change', function() {
                const selectedOption = this.value;
                const [field, direction] = selectedOption.split('_');
                
                // Build the URL with current parameters but update sort parameters
                const currentUrl = new URL(window.location.href);
                const searchParams = currentUrl.searchParams;
                
                // Update or add sort parameters
                searchParams.set('sort', field);
                searchParams.set('direction', direction);
                
                // Reset page to 1 when sorting changes
                searchParams.set('page', '1');
                
                // Navigate to the new URL
                window.location.href = currentUrl.toString();
            });
        }
        
        // Handle clear search button
        const clearButton = document.getElementById('clearSearch');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                }
                
                // Submit the form to clear the search
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.submit();
                }
            });
        }
        
        // Fix tab switching to maintain query parameters
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(event) {
                // When switching tabs, preserve current URL parameters
                const activeTab = event.target.getAttribute('id');
                
                // You might want to store the active tab in localStorage or session
                // so it can be restored when the page is reloaded
                sessionStorage.setItem('activeStockTab', activeTab);
            });
        });
        
        // Restore active tab from session storage if available
        const activeTab = sessionStorage.getItem('activeStockTab');
        if (activeTab) {
            const tabEl = document.getElementById(activeTab);
            if (tabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
        
        // Handle return and extend buttons
        const returnButtons = document.querySelectorAll('.btn-return');
        returnButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const stockId = this.getAttribute('data-stock-id');
                
                // Here you'd normally do an AJAX call or form submission
                // For demonstration, we'll just console log
                console.log('Return stock ID:', stockId);
            });
        });
        
        // ←─── **AND ALSO HERE** ───←
        // In vanilla JS, force pagination links to navigate
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
        });
    });
    </script>
@endsection


<!-- Operation Modal (OUT / LOAN / RETURN) -->
@if($selectedDivision !== 'all')
<div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
<form method="POST"
      action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
  @csrf
  <input type="hidden" name="stock_id"   id="operation_stock_id">
<input type="hidden" name="batch_id" id="operation_batch_id">  
  <input type="hidden" name="force_operation_type" id="force_operation_type">
            <div class="modal-content shadow-lg rounded-lg">
                <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                    <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- any validation errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Operation Type Radios -->
                    <div class="mb-3" id="operationTypeContainer">
                        <label class="form-label fw-bold">Operation Type:</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                            <label class="btn btn-out-custom" for="btn-out">OUT</label>
                            <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                            <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>
                        </div>
                    </div>

                    <!-- Transaction Date & Delivery -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                            <input type="date" name="manual_date" id="manual_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="collection_note" class="form-label fw-bold">Collection Note / Service Voucher</label>
                            <input type="text" name="collection_note" id="collection_note" class="form-control" placeholder="Enter collection note / service voucher">
                        </div>
                    </div>

                    <!-- Taken By & Ticket Number -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label id="takenByLabel" for="taken_by" class="form-label fw-bold">Taken by</label>
                            <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ticket_number" class="form-label fw-bold">Ticket Number</label>
                            <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number">
                        </div>
                    </div>

                    <!-- Requisition Number & Loan Period -->
                   <div class="row mb-3">
    <div class="col-md-6">
        <label for="manual_requisition" class="form-label fw-bold">Requisition Number</label>
        <input type="text" name="manual_requisition" id="manual_requisition" class="form-control" placeholder="Enter requisition number">
    </div>

    <div class="col-md-6" id="quantity_container" style="display:none;">
        <label for="operation_quantity" class="form-label fw-bold">Quantity</label>
        <input
            type="number"
            name="quantity"
            id="operation_quantity"
            class="form-control"
            placeholder="Enter quantity"
            max=""
        >
    </div>

    <div class="col-md-6" id="loan_period_container">
        <label for="loan_period" class="form-label fw-bold">Loan Period (days)</label>
        <input type="number" name="loan_period" id="loan_period" class="form-control" min="1" max="90" placeholder="Loan period">
    </div>
</div>


                    <!-- Remarks -->
                    <div class="mb-3">
                        <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

