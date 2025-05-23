{{-- resources/views/stocks/partials/stock_details_card.blade.php --}}

{{-- 1) BASIC STOCK INFO --}}
<div class="row mb-4">
  <div class="col-md-6 mb-2">
    <p class="mb-1"><strong>Serial Number:</strong> {{ $stock->serial_number ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Brand:</strong> {{ $brand ?? $stock->brand ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Model:</strong> {{ $model ?? $stock->model ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Product Code:</strong> {{ $productCode ?? $stock->product_code ?? 'N/A' }}</p>
    <p class="mb-1"><strong>SLA Category:</strong> {{ $stock->sla_category ?? 'N/A' }}</p>
  </div>
  <div class="col-md-6 mb-2">
    <p class="mb-1"><strong>Category:</strong> {{ $category ?? $stock->category ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Requisition No.:</strong> {{ $stock->requisition_no ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Requisition/ERP Number:</strong> {{ $stock->erp_number ?? 'N/A' }}</p>
    <p class="mb-1"><strong>SLA:</strong> {{ $stock->sla ?? 'N/A' }}</p>
    <p class="mb-1">
      <strong>Last Update:</strong>
      {{ $stock->updated_at
         ? \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s')
         : 'N/A' }}
    </p>
  </div>
</div>

{{-- 2) TRANSACTION SUMMARY --}}
<div class="p-4 bg-white rounded shadow">
  <div class="grid grid-cols-2 gap-y-3 gap-x-4">
    <div class="flex justify-between">
      <span class="text-sm font-semibold text-gray-700">Requisition No:</span>
      <span class="text-sm text-gray-900">{{ $transaction->requisition_no ?? 'Not Available' }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-sm font-semibold text-gray-700">Serial Number:</span>
      <span class="text-sm text-gray-900">{{ $transaction->serial_number ?? 'Not Available' }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-sm font-semibold text-gray-700">Brand:</span>
      <span class="text-sm text-gray-900">{{ $transaction->brand ?? 'N/A' }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-sm font-semibold text-gray-700">Product Code:</span>
      <span class="text-sm text-gray-900">{{ $transaction->product_code ?? 'N/A' }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-sm font-semibold text-gray-700">Model:</span>
      <span class="text-sm text-gray-900">{{ $transaction->stock_model ?? 'Not Available' }}</span>
    </div>
    @if(!empty($transaction->category))
      <div class="flex justify-between">
        <span class="text-sm font-semibold text-gray-700">Category:</span>
        <span class="text-sm text-gray-900">{{ $transaction->category }}</span>
      </div>
    @endif
    <div class="col-span-2">
      <span class="block text-sm font-semibold text-gray-700">Remarks:</span>
      <p class="text-sm text-gray-900">{{ $transaction->remarks ?? 'N/A' }}</p>
    </div>
    <div class="col-span-2 border-t border-gray-200 pt-2">
      <span class="block text-xs text-gray-500">
        Transaction Date:
        {{ isset($transaction->transaction_date)
            ? \Carbon\Carbon::parse($transaction->transaction_date)
                ->format('M d, Y h:i A')
            : 'Not Available' }}
      </span>
    </div>
  </div>
</div>

{{-- 3) TRANSACTION HISTORY --}}
<div class="p-4 bg-white rounded shadow mt-6">
  <h5 class="text-md font-bold text-gray-800 mb-3">Transaction History</h5>

  @if($history->isNotEmpty())
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b">
          <tr>
            <th class="px-2 py-1 text-left">Type</th>
            <th class="px-2 py-1 text-left">Date</th>
            <th class="px-2 py-1 text-left">Remarks</th>
          </tr>
        </thead>
        <tbody>
          @foreach($history as $h)
            @php
              $typeColor = match($h->type) {
                'IN'   => 'text-green-600',
                'LOAN' => 'text-yellow-600',
                'OUT'  => 'text-red-600',
                default => 'text-gray-600',
              };
              // strip out “(Taken by: …)”
              preg_match('/\(Taken by:\s*(.*?)\)/', $h->remarks, $m);
              $takenBy = $m[1] ?? null;
              $pure    = $takenBy
                ? trim(str_replace($m[0], '', $h->remarks))
                : $h->remarks;
            @endphp
            <tr class="border-b hover:bg-gray-50 transition-colors">
              <td class="px-2 py-1 {{ $typeColor }}">{{ $h->type }}</td>
              <td class="px-2 py-1">
                {{ \Carbon\Carbon::parse($h->transaction_date)->format('M d, Y h:i A') }}
              </td>
              <td class="px-2 py-1">
                {{ $pure }}
                @if($takenBy)
                  <br><small class="text-gray-500">Taken by: {{ $takenBy }}</small>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <p class="text-sm text-gray-500">No transaction history available.</p>
  @endif
</div>