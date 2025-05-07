@extends('layouts.layout')

@section('title','Stock Report')

@section('styles')
  <!-- DataTables Bootstrap5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
  <!-- DataTables Buttons Bootstrap5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <style>
    /* DataTables Buttons: simple raised style */
    .dt-buttons .btn {
      background-color: #f7f7f7;
      border: 1px solid #ccc;
      color: #333;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 4px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
      margin-right: .5rem;
      transition: background-color .2s;
    }
    .dt-buttons .btn:hover {
      background-color: #e6e6e6;
      color: #000;
    }
    .dt-buttons .btn:active {
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
      background-color: #ddd;
    }

    /* Custom back button style */
    .btn-back {
      display: inline-flex;
      align-items: center;
      background-color: #f0f0f0;
      color: #007bff;
      padding: 8px 16px;
      border-radius: 5px;
      font-size: 14px;
      text-decoration: none;
      border: 1px solid #ccc;
      margin-bottom: 20px;
    }

    .btn-back:hover {
      background-color: #e6e6e6;
      text-decoration: none;
      color: #0056b3;
    }

    .btn-back i {
      margin-right: 5px; /* Space between arrow and text */
    }

    /* Custom dropdown style */
    #divisionSelect {
      width: 100%;
      max-width: 300px;
      margin-left: 10px;
      border-radius: 5px;
      padding: 8px;
      border: 1px solid #ccc;
      background-color: #fff;
      color: #333;
    }
    #divisionSelect:focus {
      border-color: #0056b3;
    }

    /* Flexbox for label and dropdown side by side */
    .select-division-container {
      display: flex;
      align-items: center;
    }

    .select-division-container .form-label {
      margin-right: 10px;
      font-weight: 500;
    }
  </style>
@endsection

@section('content')
<div class="container my-5">

  {{-- Back to management --}}
  <div class="mb-4">
    <a href="{{ route('stocks.management') }}" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back to Management
    </a>
  </div>

  {{-- Division Selection --}}
  <div class="mb-4 select-division-container">
    <label for="divisionSelect" class="form-label">Select Division:</label>
    <select id="divisionSelect" class="form-select" aria-label="Select Division" onchange="window.location.href=this.value">
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'all_divisions']) }}" 
              {{ $selectedDivision == 'all_divisions' ? 'selected' : '' }}>
        All Divisions
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'Kuching']) }}" 
              {{ $selectedDivision == 'Kuching' ? 'selected' : '' }}>
        Kuching
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'Miri']) }}" 
              {{ $selectedDivision == 'Miri' ? 'selected' : '' }}>
        Miri
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'Samarahan']) }}" 
              {{ $selectedDivision == 'Samarahan' ? 'selected' : '' }}>
        Samarahan
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'SriAman']) }}" 
              {{ $selectedDivision == 'SriAman' ? 'selected' : '' }}>
        Sri Aman
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'Bintulu']) }}" 
              {{ $selectedDivision == 'Bintulu' ? 'selected' : '' }}>
        Bintulu
      </option>
      <option value="{{ route('reports.stock', ['report_type' => $reportType, 'division' => 'Kapit']) }}" 
              {{ $selectedDivision == 'Kapit' ? 'selected' : '' }}>
        Kapit
      </option>
    </select>
  </div>

  {{-- For debugging (can be removed in production) --}}
  <div class="small text-muted mb-3">
    Selected Division: {{ $selectedDivision ?? 'all_divisions' }}
  </div>

  {{-- Report Toggle --}}
  <div class="btn-group mb-4">
    @php
      // Ensure $reportType is defined with a default value
      $reportType = $reportType ?? 'available';
    @endphp
    <a href="{{ route('reports.stock', ['report_type' => 'available', 'division' => $selectedDivision]) }}"
       class="btn btn-outline-primary {{ $reportType === 'available' ? 'active' : '' }}">
      Available
    </a>
    <a href="{{ route('reports.stock', ['report_type' => 'loaned', 'division' => $selectedDivision]) }}"
       class="btn btn-outline-warning {{ $reportType === 'loaned' ? 'active' : '' }}">
      Loaned
    </a>
  </div>

  {{-- AVAILABLE STOCKS --}}
  @if($reportType === 'available')
    <div class="table-responsive mb-5">
      <table id="availableTable" class="table table-striped table-hover table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Category</th><th>Brand</th><th>Model</th>
            <th>Serial Number</th><th>Requisition No.</th>
            <th>Requisition/ERP Number</th><th>SLA</th>
            <th>SLA Category</th><th>Remarks</th><th>Updated At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($availableStocks ?? [] as $s)
            <tr>
              <td>{{ optional($s->product)->category  ?? 'N/A' }}</td>
              <td>{{ optional($s->product)->brand     ?? 'N/A' }}</td>
              <td>{{ optional($s->product)->model     ?? 'N/A' }}</td>
              <td>{{ $s->serial_number               ?? 'N/A' }}</td>
              <td>{{ $s->requisition_no              ?? 'N/A' }}</td>
              <td>{{ $s->erp_number                  ?? 'N/A' }}</td>
              <td>{{ $s->sla                         ?? 'N/A' }}</td>
              <td>{{ $s->sla_category                ?? 'N/A' }}</td>
              <td>{{ $s->remarks                     ?? 'N/A' }}</td>
              <td>{{ $s->updated_at ? $s->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  {{-- LOANED STOCKS --}}
  @if($reportType === 'loaned')
    <div class="table-responsive mb-5">
      <table id="loanedTable" class="table table-striped table-hover table-bordered" style="width:100%">
        <thead>
          <tr>
            <th>Category</th><th>Brand</th><th>Model</th>
            <th>Serial Number</th><th>Requisition No.</th>
            <th>Requisition/ERP Number</th><th>SLA</th>
            <th>SLA Category</th>
            <th>Transaction Date</th><th>Delivery Note</th>
            <th>Document Number</th><th>Name</th>
            <th>Taken By</th><th>Loan Period</th>
            <th>Remarks</th><th>Updated At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($loanedStocks ?? [] as $stock)
            @php $t = $stock->latestTransaction; @endphp
            <tr>
              <td>{{ optional($stock->product)->category ?? 'N/A' }}</td>
              <td>{{ optional($stock->product)->brand    ?? 'N/A' }}</td>
              <td>{{ optional($stock->product)->model    ?? 'N/A' }}</td>
              <td>{{ $stock->serial_number               ?? 'N/A' }}</td>
              <td>{{ $stock->requisition_no              ?? 'N/A' }}</td>
              <td>{{ $stock->erp_number                  ?? 'N/A' }}</td>
              <td>{{ $stock->sla                         ?? 'N/A' }}</td>
              <td>{{ $stock->sla_category                ?? 'N/A' }}</td>
              <td>
                {{ $t && $t->transaction_date
                    ? \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d')
                    : 'N/A' }}
              </td>
              <td>{{ $t->delivery_note ?? 'N/A' }}</td>
              <td>{{ $t->document_number ?? 'N/A' }}</td>
              <td>{{ $t->name ?? 'N/A' }}</td>
              <td>{{ $t->taken_by ?? 'N/A' }}</td>
              <td>{{ $t->loan_period ?? 'N/A' }}</td>
              <td>
                @php
                  $remarks = $stock->remarks ?? '';
                  preg_match('/Taken by: (.*?)(\)|$)/', $remarks, $matches);
                  echo isset($matches[1]) ? trim($matches[1]) : 'N/A';
                @endphp
              </td>
              <td>{{ $stock->updated_at ? $stock->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>
@endsection

@section('scripts')
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables core + Bootstrap5 integration -->
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
  <!-- DataTables Buttons & dependencies -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/js/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

  <script>
    // buttons on first row (left), "Show entries" top-right, then search below buttons (left)
    const domWithButtons = `
      <'d-flex justify-content-between align-items-center mb-3'
        <'d-flex'B>
        <'d-flex align-items-center'l>
      >
      <'d-flex mb-3'
        <'d-flex'f>
      >
      t
      <'d-flex justify-content-between align-items-center mt-3'
        <'d-flex'i>
        <'d-flex'p>
      >
    `;

    $(function(){
      // Available
      $('#availableTable').DataTable({
        dom: domWithButtons,
        ordering: false, // disable sorting
        buttons: [
          { extend: 'copy',  exportOptions: { modifier: { page: 'all' } } },
          { extend: 'csv',   exportOptions: { modifier: { page: 'all' } } },
          { extend: 'excel', exportOptions: { modifier: { page: 'all' } } },
          {
            extend: 'pdf',
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: { modifier: { page: 'all' } },
            customize: function(doc) {
              doc.defaultStyle.fontSize = 7;
              doc.styles.tableHeader.fontSize = 8;
              var colCount = doc.content[1].table.body[0].length;
              doc.content[1].table.widths = Array(colCount).fill('*');
            }
          },
          {
            extend: 'print',
            exportOptions: { modifier: { page: 'all' } },
            customize: function(win) {
              $(win.document.body).find('table')
                 .addClass('table-sm')
                 .css('font-size','0.8em');
              $(win.document.body).find('h1')
                 .css({ 'text-align': 'center', 'margin-bottom': '20px' });
            }
          }
        ],
        pageLength: 25,
        lengthMenu: [10,25,50,100],
        language: {
          lengthMenu: "Show _MENU_ entries",
          search: "",
          searchPlaceholder: "Search…"
        }
      });

      // Loaned
      $('#loanedTable').DataTable({
        dom: domWithButtons,
        ordering: false, // disable sorting
        buttons: [
          { extend: 'copy',  exportOptions: { modifier: { page: 'all' } } },
          { extend: 'csv',   exportOptions: { modifier: { page: 'all' } } },
          { extend: 'excel', exportOptions: { modifier: { page: 'all' } } },
          {
            extend: 'pdf',
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: { modifier: { page: 'all' } },
            customize: function(doc) {
              doc.defaultStyle.fontSize = 7;
              doc.styles.tableHeader.fontSize = 8;
              var colCount = doc.content[1].table.body[0].length;
              doc.content[1].table.widths = Array(colCount).fill('*');
            }
          },
          {
            extend: 'print',
            exportOptions: { modifier: { page: 'all' } },
            customize: function(win) {
              $(win.document.body).find('table')
                 .addClass('table-sm')
                 .css('font-size','0.8em');
              $(win.document.body).find('h1')
                 .css({ 'text-align': 'center', 'margin-bottom': '20px' });
            }
          }
        ],
        pageLength: 25,
        lengthMenu: [10,25,50,100],
        language: {
          lengthMenu: "Show _MENU_ entries",
          search: "",
          searchPlaceholder: "Search…"
        }
      });
    });
  </script>
@endsection
