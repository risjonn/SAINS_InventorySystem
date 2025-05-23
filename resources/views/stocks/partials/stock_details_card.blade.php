<div class="card p-3 shadow-sm">
  <div class="card-body">
    <h5 class="card-title fw-bold mb-3">Stock Details</h5>

    {{-- 1) BASIC STOCK INFO --}}
  <div class="row mb-4">
  {{-- LEFT COLUMN (6 cols) --}}
  <div class="col-md-6 mb-2">
    @php $isBulk = empty($stock->serial_number); @endphp

    {{-- 1️⃣ Serial or Quantity goes here --}}
    @if($isBulk)
      <p class="mb-1"><strong>Quantity:</strong> {{ $stock->quantity }}</p>
    @else
      <p class="mb-1"><strong>Serial Number:</strong> {{ $stock->serial_number }}</p>
    @endif

    {{-- 2️⃣–5️⃣ stay exactly where they were --}}
    <p class="mb-1"><strong>Brand:</strong>         {{ $brand }}</p>
    <p class="mb-1"><strong>Model:</strong>         {{ $model }}</p>
    <p class="mb-1"><strong>Product Code:</strong> {{ $productCode }}</p>
    <p class="mb-1"><strong>SLA Category:</strong> {{ $stock->sla_category ?? 'N/A' }}</p>
  </div>

  {{-- RIGHT COLUMN (6 cols) --}}
  <div class="col-md-6 mb-2">
    <p class="mb-1"><strong>Category:</strong>                {{ $category }}</p>
    <p class="mb-1"><strong>Requisition No.:</strong>        {{ $stock->requisition_no ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Requisition/ERP Number:</strong> {{ $stock->erp_number    ?? 'N/A' }}</p>
    <p class="mb-1"><strong>SLA:</strong>                     {{ $stock->sla           ?? 'N/A' }}</p>
    <p class="mb-1">
      <strong>Last Update:</strong>
      {{ optional($stock->updated_at)
         ? \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s')
         : 'N/A' }}
    </p>
  </div>
</div>


    {{-- 2) TRANSACTION INFO (OUT or LOAN) --}}
    @if(in_array(strtolower($stock->status), ['out','loan']))
      <hr>
      <h6 class="fw-bold mb-3 text-capitalize">{{ $stock->status }} Info</h6>

      @php
        // compute return / due date and working‐days left for loans
        $period   = $stock->loan_period ?? 90;
        $start    = \Carbon\Carbon::parse($stock->loan_start_date ?? $stock->updated_at);
        // **subtract 1** so a 5-day loan from 13 May → due 19 May
        $dueDate  = $start->copy()->addWeekdays($period - 1);
        $daysLeft = \Carbon\Carbon::now()->diffInWeekdays($dueDate, false);
      @endphp

      <div class="row gx-3 gy-2">
        {{-- Transaction Date --}}
        <div class="col-md-6">
          <p class="mb-1">
            <strong>Transaction Date:</strong>
            {{ $transaction && $transaction->manual_date
               ? \Carbon\Carbon::parse($transaction->manual_date)->format('Y-m-d')
               : 'N/A' }}
          </p>
        </div>

        {{-- Collection Note / Service Voucher --}}
        <div class="col-md-6">
          <p class="mb-1">
            <strong>Collection Note / Service Voucher:</strong>
            {{ $transaction->collection_note ?? 'N/A' }}
          </p>
        </div>

        {{-- Taken by --}}
        <div class="col-md-6">
          <p class="mb-1">
            <strong>Taken by:</strong>
            {{ $transaction->taken_by ?? 'N/A' }}
          </p>
        </div>

        {{-- Ticket Number --}}
        <div class="col-md-6">
          <p class="mb-1">
            <strong>Ticket Number:</strong>
            {{ $transaction->ticket_number ?? 'N/A' }}
          </p>
        </div>

        {{-- Requisition Number --}}
        <div class="col-md-6">
          <p class="mb-1">
            <strong>Requisition Number:</strong>
            {{ $transaction->manual_requisition ?? 'N/A' }}
          </p>
        </div>

        {{-- LOAN‐ONLY FIELDS --}}
        @if(strtolower($stock->status) === 'loan')
          {{-- Loan Period --}}
          <div class="col-md-6">
            <p class="mb-1">
              <strong>Loan Period (days):</strong>
              {{ $stock->loan_period ?? 'N/A' }}
            </p>
          </div>

          {{-- Return By --}}
          <div class="col-md-6">
            <p class="mb-1">
              <strong>Return By:</strong>
              {{ $dueDate->format('Y-m-d') }}
            </p>
          </div>

          {{-- Working Days Left --}}
          <div class="col-md-6">
            <p class="mb-1">
              <strong>Working Days Left:</strong>
              @if($daysLeft > 0)
                {{ $daysLeft }} days left
              @elseif($daysLeft === 0)
                Expires today
              @else
                {{ abs($daysLeft) }} days overdue
              @endif
            </p>
          </div>
        @endif
      </div>
    @endif

    {{-- 3) REMARKS (always last) --}}
    <hr>
    @php
      $displayRemarks = $transaction->remarks ?? $stock->remarks ?? 'N/A';
      // strip any “(Taken by:…)” from the end
      $displayRemarks = preg_replace('/\(Taken by:\s*.*?\)/i', '', $displayRemarks);
    @endphp
    <p class="mb-1"><strong>Remarks:</strong></p>
    <p>{{ $displayRemarks }}</p>
  </div>
</div>
