@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">
        Stock Management
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ isset($division) ? $division->name : 'All Divisions' }})
        </span>
    </h2>

    <div class="my-4">
        <a href="{{ route('stocks.transactions.in') }}" class="bg-green-600 text-white px-4 py-2 rounded mr-2">
            IN
        </a>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Current Stocks</h3>
        @if($stocks->isNotEmpty())
        <table class="w-full border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Serial Number</th>
                    <th class="p-2">Model</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Remarks</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr class="border-b">
                    <td class="p-2">{{ $stock->serial_number }}</td>
                    <td class="p-2">{{ $stock->model }}</td>
                    <td class="p-2">{{ $stock->category }}</td>
                    <td class="p-2">{{ $stock->remarks }}</td>
                    <td class="p-2">
                        @if(empty($stock->status) || $stock->status === 'available')
                            <button type="button" class="bg-red-600 text-white px-2 py-1 rounded btn-out" data-stock-id="{{ $stock->id }}">
                                OUT
                            </button>
                        @else
                            <span class="text-gray-500 text-sm">Removed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p>No stocks available for this division.</p>
        @endif
    </div>
</div>

<!-- Modal for OUT Operation -->
<div class="modal fade" id="outModal" tabindex="-1" aria-labelledby="outModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="outForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="outModalLabel">Confirm OUT Operation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="outRemark" class="form-label">Please provide a remark for this OUT operation:</label>
                        <textarea name="remarks" id="outRemark" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm OUT</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var outModal = new bootstrap.Modal(document.getElementById('outModal'));
    document.querySelectorAll('.btn-out').forEach(function(button){
         button.addEventListener('click', function(){
             var stockId = this.getAttribute('data-stock-id');
             var formAction = '/stocks/' + stockId + '/out';
             document.getElementById('outForm').setAttribute('action', formAction);
             document.getElementById('outRemark').value = '';
             outModal.show();
         });
    });
});
</script>
    
@endsection
