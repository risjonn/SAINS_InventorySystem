    @extends('layouts.layout')

    @section('title', 'Stock Management')

    @section('content')
    <div class="container my-5">
    @php
        // 1) Prefer URL ?division=… over anything in session (default “all”)
        $selectedDivision = request('division', session('selectedDivision', 'all'));
        // 2) Persist back into session
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
                Please select a specific division below (or click one of these cards) to manage its stock.
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
                        <a  href="{{ route('stocks.management',['division'=>$d->id]) }}"
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
            {{-- 3b) Single‑division UI ------------------------------------------------ --}}
            @if(isset($overdueLoans) && $overdueLoans->count())
                <div class="alert alert-danger mt-3">
                    <strong>Attention!</strong> There are {{ $overdueLoans->count() }} overdue loan(s). Please review them immediately.
                </div>
            @endif

            <div class="d-flex align-items-center my-4">
                <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-3">IN</a>
                <a href="{{ route('reports.stock') }}"           class="bg-blue-600  text-white px-4 py-2 rounded">Generate Report</a>
            </div>

            <!-- ░░  Search  +  Sort  ░░ -->
            <div class="row mb-4">
                <!-- Search -->
                <div class="col-md-8 mb-2 mb-md-0">
                    <form id="searchForm" method="GET" action="{{ url()->current() }}">
                        <div class="input-group">
                            <input  class="form-control"
                                    id="searchInput"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search stocks… (Serial Number, Model)">
                            <div class="input-group-append">
                                <button type="submit"  class="btn btn-primary  ml-2">Apply</button>
                                <button type="button" class="btn btn-secondary ml-2" id="clearSearch">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Sort -->
                <div class="col-md-4 text-md-right">
                    <label for="sortOption" class="mr-2 font-semibold">Sort by:</label>
                    <select id="sortOption" class="p-2 border rounded" style="width:250px">
                        <option value="model_asc"          >Model (A‑Z)</option>
                        <option value="model_desc"         >Model (Z‑A)</option>
                        <option value="category_asc"       >Category (A‑Z)</option>
                        <option value="category_desc"      >Category (Z‑A)</option>
                        <option value="sla_category_asc"   >SLA Category (A‑Z)</option>
                        <option value="sla_category_desc"  >SLA Category (Z‑A)</option>
                        <option value="updated_at_asc"     >Updated At (Oldest)</option>
                        <option value="updated_at_desc" selected>Updated At (Newest)</option>
                    </select>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="stockTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#availableTab">Available Stocks</button></li>
                <li class="nav-item"><button class="nav-link"        id="loaned-tab"    data-bs-toggle="tab" data-bs-target="#loanedTab"   >Loaned Stocks  </button></li>
            </ul>

            <div class="tab-content mt-4">
                {{-- AVAILABLE ------------------------------------------------------- --}}
                <div class="tab-pane fade show active" id="availableTab">
                    <table id="availableStockTable" class="table table-hover table-bordered mb-0">
                        <thead class="bg-gray-200">
                            <tr>
                                <th>Category</th><th>Brand</th><th>Model</th><th>Serial Number</th>
                                <th>Requisition No.</th><th>Requisition/ERP Number</th><th>SLA Category</th>
                                <th>Remarks</th><th>Updated At</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($availableStocks as $s)
                            <tr>
                                <td>{{ optional($s->product)->category   ?? 'N/A' }}</td>
                                <td>{{ optional($s->product)->brand      ?? 'N/A' }}</td>
                                <td>{{ optional($s->product)->model      ?? 'N/A' }}</td>
                                <td>
                                    <a href="#" class="stock-detail" data-serial="{{ $s->serial_number }}" style="text-decoration:underline;">
                                        {{ $s->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $s->requisition_no ?? 'N/A' }}</td>
                                <td>{{ $s->erp_number     ?? 'N/A' }}</td>
                                <td>{{ $s->sla_category   ?? 'N/A' }}</td>
                                <td>{{ $s->remarks        ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->updated_at)->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <button class="btn btn-danger btn-out" data-stock-id="{{ $s->id }}">OUT</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">No matching records found</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    {!! $availableStocks->appends(request()->all())->links() !!}
                </div>

                {{-- LOANED ---------------------------------------------------------- --}}
                <div class="tab-pane fade" id="loanedTab">
                    <table id="loanedStockTable" class="table table-bordered mt-3">
                        <thead class="bg-gray-200">
                            <tr>
                                <th>Category</th><th>Brand</th><th>Model</th><th>Serial Number</th>
                                <th>Requisition No.</th><th>Requisition/ERP Number</th><th>Remarks</th>
                                <th>Updated At</th><th>Taken By</th><th>Days Left</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($loanedStocks as $s)
                            @php
                                $full     = $s->remarks ?? '';
                                preg_match('/\(Taken by:\s*(.*?)\)/',$full,$m);
                                $takenBy  = $m[1] ?? 'N/A';
                                $dispRem  = trim(preg_replace('/\(Taken by:.*?\)/','',$full)) ?: 'N/A';
                                $start    = $s->loan_start_date ?? $s->updated_at;
                                $period   = $s->loan_period     ?? 90;
                                $daysLeft = $period - \Carbon\Carbon::parse($start)->diffInDays(now());
                                $dueDate  = \Carbon\Carbon::parse($start)->addDays($period)->format('Y-m-d H:i:s');
                            @endphp
                            <tr data-duedate="{{ $dueDate }}" @if($daysLeft<0) style="background:#FFECEC;color:#C00000" @endif>
                                <td>{{ optional($s->product)->category ?? 'N/A' }}</td>
                                <td>{{ optional($s->product)->brand    ?? 'N/A' }}</td>
                                <td>{{ optional($s->product)->model    ?? 'N/A' }}</td>
                                <td>
                                    <a href="#" class="stock-detail" data-serial="{{ $s->serial_number }}" style="text-decoration:underline;">
                                        {{ $s->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $s->requisition_no ?? 'N/A' }}</td>
                                <td>{{ $s->erp_number     ?? 'N/A' }}</td>
                                <td>{{ $dispRem }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->updated_at)->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $takenBy }}</td>
                                <td data-daysleft="{{ $daysLeft }}">
                                    @if($daysLeft>0)          {{ $daysLeft }} days left
                                    @elseif($daysLeft==0)     Expires today
                                    @else                    {{ abs($daysLeft) }} days overdue
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-return" data-stock-id="{{ $s->id }}">Return</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center">No matching records found</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    {!! $loanedStocks->appends(request()->all())->links() !!}
                </div>
            </div>
        @endif
    </div>
    @endsection
    <!-- …the rest of your modals and scripts remain exactly as they were… -->
    <!-- Loaned stock details modal (reqModal) for stock details -->
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

    <!-- Modal for "Available Stock Report" -->
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

    <!-- Modal for "Loaned Stock Report" -->
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

    @section('scripts')
    <!-- Include jQuery and Bootstrap Bundle (for modals) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {

        // When Loaned Stocks tab is active on page load, force sort by Due Date.
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

        $("#sortOption").on("change", function() {
            sortTableByOption();
        });

        function sortTableByOption() {
            var sortValue = $("#sortOption").val();
            var config = { columnIndex: 1, order: "asc", isDate: false };

            switch (sortValue) {
                case "model_asc":
                    config.columnIndex = 3;
                    config.order = "asc";
                    break;
                case "model_desc":
                    config.columnIndex = 3;
                    config.order = "desc";
                    break;
                case "category_asc":
                    config.columnIndex = 1;
                    config.order = "asc";
                    break;
                case "category_desc":
                    config.columnIndex = 1;
                    config.order = "desc";
                    break;
                case "sla_category_asc":
                    config.columnIndex = 7;
                    config.order = "asc";
                    break;
                case "sla_category_desc":
                    config.columnIndex = 7;
                    config.order = "desc";
                    break;
                case "updated_at_asc":
                case "updated_at_desc":
                    // For Available tab: use Updated At (column 9)
                    // For Loaned tab, use Due Date for sorting.
                    if ($("#loanedTab").hasClass("active")) {
                        config.columnIndex = "due_date";
                        // For our interpretation:
                        //   "updated_at_desc" (default) means newest due date first (desc),
                        //   "updated_at_asc" means oldest due date first (asc).
                        config.order = (sortValue === "updated_at_desc") ? "desc" : "asc";
                        config.isDate = true;
                    } else {
                        config.columnIndex = 9;
                        config.order = (sortValue === "updated_at_desc") ? "desc" : "asc";
                        config.isDate = true;
                    }
                    break;
                default:
                    config.columnIndex = 1;
                    config.order = "asc";
            }
            
            // Apply sorting on available and loaned tables
            sortAvailableTable(config.columnIndex, config.order, config.isDate);
            sortLoanedTable(config.columnIndex, config.order, config.isDate);
        }

        function sortAvailableTable(columnIndex, order, isDate) {
            var rows = $("#availableStockTable tbody tr").get();
            rows.sort(function(a, b) {
                var A = $(a).children("td").eq(columnIndex - 1).text().trim();
                var B = $(b).children("td").eq(columnIndex - 1).text().trim();
                if (isDate) {
                    A = parseDateTime(A);
                    B = parseDateTime(B);
                } else {
                    A = A.toUpperCase();
                    B = B.toUpperCase();
                }
                if (A < B) return (order === "asc" ? -1 : 1);
                if (A > B) return (order === "asc" ? 1 : -1);
                return 0;
            });
            $.each(rows, function(index, row) {
                $("#availableStockTable tbody").append(row);
            });
        }

        function sortLoanedTable(columnIdentifier, order, isDate) {
            var rows = $("#loanedStockTable tbody tr").get();
            rows.sort(function(a, b) {
                // If sorting by Due Date (we set columnIdentifier to the string "due_date")
                if (columnIdentifier === "due_date") {
                    var A = parseDateTime($(a).attr("data-duedate"));
                    var B = parseDateTime($(b).attr("data-duedate"));
                    return (order === "asc") ? (A - B) : (B - A);
                } 
                // If sorting by Days Left (column 10)
                else if (columnIdentifier === 10) {
                    var A = parseFloat($(a).children("td").eq(9).attr("data-daysleft")) || 0;
                    var B = parseFloat($(b).children("td").eq(9).attr("data-daysleft")) || 0;
                    return (order === "asc") ? (A - B) : (B - A);
                } else {
                    var A = $(a).children("td").eq(columnIdentifier - 1).text().trim();
                    var B = $(b).children("td").eq(columnIdentifier - 1).text().trim();
                    if (isDate) {
                        A = parseDateTime(A);
                        B = parseDateTime(B);
                    } else {
                        A = A.toUpperCase();
                        B = B.toUpperCase();
                    }
                    if (A < B) return (order === "asc" ? -1 : 1);
                    if (A > B) return (order === "asc" ? 1 : -1);
                    return 0;
                }
            });
            $.each(rows, function(index, row) {
                $("#loanedStockTable tbody").append(row);
            });
        }

        // Simple parser for "YYYY-MM-DD HH:mm:ss" into a numeric timestamp
        function parseDateTime(dateStr) {
            if (!dateStr) return 0;
            var parts = dateStr.split(" ");
            var datePart = parts[0].split("-");
            var timePart = parts[1] ? parts[1].split(":") : [0, 0, 0];
            var year  = parseInt(datePart[0]) || 0;
            var month = parseInt(datePart[1]) - 1 || 0;
            var day   = parseInt(datePart[2]) || 0;
            var hour  = parseInt(timePart[0]) || 0;
            var min   = parseInt(timePart[1]) || 0;
            var sec   = parseInt(timePart[2]) || 0;
            return new Date(year, month, day, hour, min, sec).getTime();
        }

        // Searching
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            var $rowsAvail = $("#availableStockTable tbody tr").not(".no-record");
            $rowsAvail.each(function() {
                var rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.indexOf(value) !== -1);
            });
            if ($("#availableStockTable tbody tr:visible").length === 0) {
                var colspanA = $("#availableStockTable thead tr th").length;
                $("#availableStockTable tbody").append('<tr class="no-record"><td colspan="' + colspanA + '" class="text-center py-3">No matching records found</td></tr>');
            } else {
                $("#availableStockTable tbody tr.no-record").remove();
            }

            var $rowsLoan = $("#loanedStockTable tbody tr").not(".no-record");
            $rowsLoan.each(function() {
                var rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.indexOf(value) !== -1);
            });
            if ($("#loanedStockTable tbody tr:visible").length === 0) {
                var colspanL = $("#loanedStockTable thead tr th").length;
                $("#loanedStockTable tbody").append('<tr class="no-record"><td colspan="' + colspanL + '" class="text-center py-3">No matching records found</td></tr>');
            } else {
                $("#loanedStockTable tbody tr.no-record").remove();
            }
        });

        $("#searchForm").on("submit", function(e) {
            $("#searchInput").trigger("keyup");
        });

        $("#clearSearch").on("click", function() {
            $("#searchInput").val('');
            $(this).closest("form").submit();
        });

        // OUT and RETURN Button Handlers
        $(".btn-out").on("click", function() {
            var stockId = $(this).data("stock-id");
            $("#stock_id").val(stockId);
            var operationModalEl = document.getElementById("operationModal");
            var operationModal = new bootstrap.Modal(operationModalEl);
            $("#operationModalLabel").text("Confirm Transaction Details");
            $("#force_operation_type").val("");
            $("input[name='operation_type'][value='OUT']").prop("checked", true);
            $("#operationRemark").val("");
            $("#loan_period_container").hide();
            $("#loan_period_container").show();
            $("#loan_period").prop("required", false);
            $("#outExtraFields").show();      
            $('#loan_period_container').show();      
            operationModal.show();
        });


        $(document).on("click", ".btn-return", function() {
            var stockId = $(this).data("stock-id");
            $("#stock_id").val(stockId);
            var operationModalEl = document.getElementById("operationModal");
            var operationModal = new bootstrap.Modal(operationModalEl);
            $("#operationModalLabel").text("Confirm Return");
            $("#operationTypeContainer").hide();
            $("#force_operation_type").val("RETURN");
            $("#loanFields").show();
            $("#loanFields input[required], #loanFields textarea[required]").prop("required", false);
            $("#outExtraFields").hide();
            $("#operationRemark").val("");
            $("#loan_period_container").hide();     
            operationModal.show();
        });

        $("input[name='operation_type']").on("change", function() {
            if ($(this).val() === "LOAN") {
                $("#loanFields").show();
                $("#loan_period_container").show();
                $("#loan_period").prop("required", true);
                $("#taken_by").prop("required", true);
                $("#outExtraFields").hide();
            } else if ($(this).val() === "OUT") {
                $("#loanFields").show();
                $("#loan_period_container").hide();
                $("#loan_period").prop("required", false);
                $("#taken_by").prop("required", true);
                $("#outExtraFields").show();
            } else {
                $("#loanFields").hide();
                $("#loan_period_container").hide();
                $("#loan_period").prop("required", false);
                $("#taken_by").prop("required", false);
                $("#outExtraFields").hide();
            }
        });

        $('#operationModal').on("hidden.bs.modal", function(e) {
            $(this).find("form")[0].reset();
            $("#loanFields").show();
            $("#loan_period_container").show();
            $("#operationModalLabel").text("Confirm Transaction Details");
            $("#operationTypeContainer").show();
            $("#force_operation_type").val("");
            $("#taken_by").prop("required", true);
            $("#loan_period").prop("required", false);
            $("#outExtraFields").hide();
            $('#loan_period_container').show();
        });

        // Stock Detail Modal Handler
        $(document).on("click", ".stock-detail", function(e) {
            e.preventDefault();
            var serial = $(this).data("serial");
            $("#reqDetailsContent").html(
                '<div class="d-flex justify-content-center align-items-center p-5">' +
                '    <div class="spinner-border text-primary" role="status">' +
                '        <span class="visually-hidden">Loading...</span>' +
                '    </div>' +
                '</div>'
            );
            var reqModal = new bootstrap.Modal(document.getElementById("reqModal"));
            reqModal.show();
            $.ajax({
                url: "{{ route('stocks.details') }}",
                type: "GET",
                data: { serial: serial },
                success: function(response) {
                    $("#reqDetailsContent").html(response);
                },
                error: function() {
                    $("#reqDetailsContent").html('<div class="alert alert-danger">Unable to load stock details.</div>');
                }
            });
        });
        // NEW: hide loan period _only_ when opening via the Return button
        // ------------------------------------------------------------------------
        $('#operationModal').on('show.bs.modal', function(e){
        // e.relatedTarget is the button that triggered the modal
        const isReturn = $(e.relatedTarget).hasClass('btn-return');
        // show loan-period if NOT return; hide if return
        $('#loan_period_container').toggle(! isReturn);
        });

        // Toggle Report Buttons on Tab Change
        $('#stockTabs button[data-bs-toggle="tab"]').on("shown.bs.tab", function(e) {
            var targetId = $(e.target).attr("id");
            if (targetId === "available-tab") {
                $("#availableReportBtn").removeClass("d-none");
                $("#loanedReportBtn").addClass("d-none");
            } else if (targetId === "loaned-tab") {
                $("#availableReportBtn").addClass("d-none");
                $("#loanedReportBtn").removeClass("d-none");
                // Each time the loaned tab is shown, force sort by Due Date descending.
                sortLoanedTable("due_date", "desc", true);
            }
        });
    });
    </script>
    @endsection

    @if($selectedDivision !== 'all')
        <!-- Operation Modal for OUT/LOAN/RETURN -->
        <div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="operationForm" method="POST" action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
                    @csrf
                    <input type="hidden" name="stock_id" id="stock_id">
                    <input type="hidden" name="force_operation_type" id="force_operation_type" value="">
                    <div class="modal-content shadow-lg rounded-lg">
                        <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                            <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
                                <div class="btn-group" role="group" aria-label="Operation Type">
                                    <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                                    <label class="btn btn-out-custom" for="btn-out">OUT</label>
                                    <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                                    <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>
                                </div>
                            </div>
                            <!-- Loan Fields -->
                            <div class="mb-3" id="loanFields">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                                        <input type="date" name="manual_date" id="manual_date" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="delivery_note" class="form-label fw-bold">Delivery Note</label>
                                        <input type="text" name="delivery_note" id="delivery_note" class="form-control" placeholder="Enter delivery note">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="document_number" class="form-label fw-bold">Document Number</label>
                                        <input type="text" name="document_number" id="document_number" class="form-control" placeholder="Enter document number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-bold">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="taken_by" class="form-label fw-bold">Taken by</label>
                                        <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                                    </div>
                                    <div class="col-md-6 mb-3" id="loan_period_container">
                                        <label for="loan_period" class="form-label fw-bold">Loan Period (days)</label>
                                        <input type="number" name="loan_period" id="loan_period" class="form-control" placeholder="Enter loan period (1-90 days)" min="1" max="90">
                                    </div>
                                </div>
                            </div>
                            <!-- Remarks -->
                            <div class="mb-3">
                                <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                                <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                            </div>
                            <!-- Extra Fields for OUT Transactions -->
                            <div class="row" id="outExtraFields" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label for="ticket_number" class="form-label fw-bold">Ticket Number</label>
                                    <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="manual_requisition" class="form-label fw-bold">Requisition Number</label>
                                    <input type="text" name="manual_requisition" id="manual_requisition" class="form-control" placeholder="Enter requisition number">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!-- Cancel and Submit -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif