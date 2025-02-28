@if($availableStocks->isNotEmpty())
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="availableStockTable" class="w-full border rounded-lg shadow-md bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="p-2">Serial Number</th>
                            <th class="p-2">Brand</th>
                            <th class="p-2">Model</th>
                            <th class="p-2">Category</th>
                            <th class="p-2">Product Code</th>
                            <th class="p-2">Requisition No.</th>
                            <th class="p-2">Remarks</th>
                            <th class="p-2">Updated At</th>
                            <th class="p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableStocks as $stock)
                        <tr class="border-b">
                            <td class="p-2">{{ $stock->serial_number }}</td>
                            <td class="p-2">{{ $stock->brand ?? 'N/A' }}</td>
                            <td class="p-2">{{ $stock->model }}</td>
                            <td class="p-2">{{ $stock->category }}</td>
                            <td class="p-2">{{ $stock->product_code ?? 'N/A' }}</td>
                            <td class="p-2">{{ $stock->requisition_no ?? 'N/A' }}</td>
                            <td class="p-2">{{ $stock->remarks }}</td>
                            <td class="p-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                            <td class="p-2">
                                <button type="button" class="bg-red-600 text-white px-2 py-1 rounded btn-out" data-stock-id="{{ $stock->id }}">
                                    OUT
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
    {!! $availableStocks->appends(request()->all())->links('pagination::bootstrap-4') !!}
    </div>
@else
    <p>No available stocks for this division.</p>
@endif