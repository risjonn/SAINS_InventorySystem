<!-- Transaction Summary -->
<div class="p-6 space-y-4">
  <div class="p-4 bg-white rounded shadow">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-semibold text-gray-700">Requisition No:</span>
      <span class="text-sm text-gray-900">{{ $transaction->requisition_no ?? 'Not Available' }}</span>
    </div>
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-semibold text-gray-700">Serial Number:</span>
      <span class="text-sm text-gray-900">{{ $transaction->serial_number ?? 'Not Available' }}</span>
    </div>
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-semibold text-gray-700">Brand:</span>
      <span class="text-sm text-gray-900">{{ $transaction->brand ?? 'N/A' }}</span>
    </div>
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-semibold text-gray-700">Product Code:</span>
      <span class="text-sm text-gray-900">{{ $transaction->product_code ?? 'N/A' }}</span>
    </div>
    <div class="flex items-center justify-between">
      <span class="text-sm font-semibold text-gray-700">Model:</span>
      <span class="text-sm text-gray-900">{{ $transaction->stock_model ?? 'Not Available' }}</span>
    </div>
    @if(!empty($transaction->category))
    <div class="flex items-center justify-between mt-2">
      <span class="text-sm font-semibold text-gray-700">Category:</span>
      <span class="text-sm text-gray-900">{{ $transaction->category }}</span>
    </div>
    @endif
    <div class="mt-3">
      <span class="block text-sm font-semibold text-gray-700">Remarks:</span>
      <p class="text-sm text-gray-900">{{ $transaction->remarks ?? 'N/A' }}</p>
    </div>
    <div class="mt-3">
      <span class="block text-xs text-gray-500">Transaction Date: {{ isset($transaction->transaction_date) ? \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') : 'Not Available' }}</span>
    </div>
  </div>
  
  <!-- Transaction History Section -->
  <div class="p-4 bg-white rounded shadow">
    <h5 class="text-md font-bold text-gray-800 mb-3">Transaction History</h5>
    @if(isset($history) && $history->isNotEmpty())
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="border-b">
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
              $pureRemarks = $h->remarks;
              $takenByValue = '';
              if (in_array($h->type, ['LOAN','OUT']) && $h->remarks) {
                  preg_match('/\(Taken by:\s*(.*?)\)/', $h->remarks, $match);
                  if (isset($match[1])) {
                      $takenByValue = $match[1];
                      $pureRemarks = trim(str_replace($match[0], '', $h->remarks));
                  }
              }
            @endphp
            <tr class="border-b">
              <td class="px-2 py-1 {{ $typeColor }}">{{ $h->type }}</td>
              <td class="px-2 py-1">{{ \Carbon\Carbon::parse($h->transaction_date)->format('M d, Y h:i A') }}</td>
              <td class="px-2 py-1">
                  {{ $pureRemarks }}
                  @if($takenByValue)
                    <br><small class="text-gray-500">Taken by: {{ $takenByValue }}</small>
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
</div>