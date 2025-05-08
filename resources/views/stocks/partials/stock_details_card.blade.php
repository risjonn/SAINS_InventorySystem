<div class="card p-3 shadow-sm">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Stock Details</h5>

        {{-- Basic Stock Info (always displayed) --}}
        <div class="row">
            <div class="col-md-6 mb-2">
                <p class="mb-1"><strong>Serial Number:</strong> {{ $stock->serial_number }}</p>
                <p class="mb-1"><strong>Brand:</strong> {{ $brand }}</p>
                <p class="mb-1"><strong>Model:</strong> {{ $model }}</p>
                <p class="mb-1"><strong>Product Code:</strong> {{ $productCode }}</p>
                <p class="mb-1"><strong>SLA Category:</strong> {{ $stock->sla_category ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6 mb-2">
                <p class="mb-1"><strong>Category:</strong> {{ $category }}</p>
                <p class="mb-1"><strong>Requisition No.:</strong> {{ $stock->requisition_no ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Requisition/ERP Number:</strong> {{ $stock->erp_number ?? 'N/A' }}</p>
                <p class="mb-1"><strong>SLA:</strong> {{ $stock->sla ?? 'N/A' }}</p>
                <p class="mb-1">
                    <strong>Last Update:</strong>
                    @if($stock->updated_at)
                        {{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>

        {{-- LOAN Info if status == 'LOAN' --}}
        @if(strtolower($stock->status) === 'loan')
            <hr>
            <h6 class="fw-bold mb-2">Loan Info</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <p class="mb-1">
                        <strong>Transaction Date:</strong>
                        @if($transaction && $transaction->manual_date)
                            {{ \Carbon\Carbon::parse($transaction->manual_date)->format('Y-m-d') }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p class="mb-1"><strong>Delivery Note:</strong> {{ $transaction->delivery_note ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Document Number:</strong> {{ $transaction->document_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    @php
                        // Pull "Taken by: ..." from remarks if present
                        $fullRemarks = $transaction->remarks ?? $stock->remarks ?? 'N/A';
                        $pattern = '/\(Taken by:\s*(.*?)\)/i';
                        $matches = [];
                        $takenByValue = 'N/A';
                        if (preg_match($pattern, $fullRemarks, $matches)) {
                            $takenByValue = $matches[1];
                        }
                    @endphp
                    <p class="mb-1"><strong>Name:</strong> {{ $transaction->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Taken By:</strong> {{ $takenByValue }}</p>
                    <p class="mb-1"><strong>Loan Period (days):</strong> {{ $stock->loan_period ?? 'N/A' }}</p>
                </div>
            </div>
        @endif

        {{-- OUT Info if status == 'OUT' --}}
        @if(strtolower($stock->status) === 'out')
            <hr>
            <h6 class="fw-bold mb-2">OUT Info</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <p class="mb-1">
                        <strong>Transaction Date:</strong>
                        @if($transaction && $transaction->manual_date)
                            {{ \Carbon\Carbon::parse($transaction->manual_date)->format('Y-m-d') }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p class="mb-1"><strong>Delivery Note:</strong> {{ $transaction->delivery_note ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Document Number:</strong> {{ $transaction->document_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    @php
                        // Same approach: remove "(Taken by: ...)" from remarks
                        $fullRemarks = $transaction->remarks ?? $stock->remarks ?? 'N/A';
                        $pattern = '/\(Taken by:\s*(.*?)\)/i';
                        $matches = [];
                        $takenByValue = 'N/A';
                        if (preg_match($pattern, $fullRemarks, $matches)) {
                            $takenByValue = $matches[1];
                        }
                    @endphp
                    <p class="mb-1"><strong>Name:</strong> {{ $transaction->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Ticket Number:</strong> {{ $transaction->ticket_number ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Requisition Number:</strong> {{ $transaction->manual_requisition ?? 'N/A' }}</p>
                </div>
            </div>
        @endif

        <hr>
        @php
            // For final remarks, remove any "(Taken by: ...)" from the text
            $displayRemarks = $transaction->remarks ?? $stock->remarks ?? 'N/A';
            $displayRemarks = preg_replace('/\(Taken by:\s*.*?\)/i', '', $displayRemarks);
            $displayRemarks = trim($displayRemarks);
        @endphp
        <p class="mb-1"><strong>Remarks:</strong></p>
        <p>{{ $displayRemarks }}</p>
    </div>
</div>
                        