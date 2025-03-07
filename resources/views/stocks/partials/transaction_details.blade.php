<!-- Transaction Details (top) -->
<div class="mb-4 p-3 border shadow-sm">
    <h5 class="fw-bold text-primary">Transaction Details</h5>

    <!-- Requisition No. -->
    <p>
        <strong>Requisition #:</strong>
        {{ $transaction->requisition_no }}
    </p>

    <!-- Serial Number -->
    <p>
        <strong>Serial Number:</strong>
        {{ $transaction->serial_number }}
    </p>

    <!-- Brand -->
    <p>
        <strong>Brand:</strong>
        {{ $transaction->brand ?? 'N/A' }}
    </p>

    <!-- New: Product Code -->
    <p>
        <strong>Product Code:</strong>
        {{ $transaction->product_code ?? 'N/A' }}
    </p>

    <!-- Model -->
    <p>
        <strong>Model:</strong>
        {{ $transaction->stock_model }}
    </p>

    <!-- Category (optional) -->
    @if(!empty($transaction->category))
        <p>
            <strong>Category:</strong>
            {{ $transaction->category }}
        </p>
    @endif

    <!-- Remarks -->
    <p>
        <strong>Remarks:</strong>
        {{ $transaction->remarks ?? 'N/A' }}
    </p>

    <hr>
    <!-- Transaction Date -->
    <p class="text-muted mb-0">
        <strong>Transaction Date:</strong>
        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
    </p>
</div>

<!-- Transaction History (bottom) -->
<div class="border shadow-sm p-3">
    <h5 class="fw-bold mb-3">Transaction History</h5>
    @if($history->isNotEmpty())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 10%">Type</th>
                    <th style="width: 25%">Date</th>
                    <th style="width: 65%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $h)
                    @php
                        // Simple color label for type
                        $typeColor = match($h->type) {
                            'IN'   => 'text-success',  // green
                            'LOAN' => 'text-warning',  // yellow
                            'OUT'  => 'text-danger',   // red
                            default => 'text-secondary',
                        };

                        // Remove "Taken by" from remarks if present
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
                    <tr>
                        <!-- Type with color -->
                        <td class="{{ $typeColor }}">
                            {{ $h->type }}
                        </td>

                        <!-- Date -->
                        <td>
                            {{ \Carbon\Carbon::parse($h->transaction_date)->format('M d, Y h:i A') }}
                        </td>

                        <!-- Remarks + "Taken by" if applicable -->
                        <td>
                            {{ $pureRemarks }}
                            @if($takenByValue)
                                <br><small class="text-muted">Taken by: {{ $takenByValue }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No transaction history available.</p>
    @endif
</div>
