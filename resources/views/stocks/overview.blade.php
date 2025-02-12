@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    @php
        $selectedDivision = session('selectedDivision');
    @endphp
    <h2 class="text-xl font-semibold mt-4">
        Stock Overview
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $selectedDivision === 'all' ? 'All Divisions' : $division->name }})
        </span>
    </h2>

    @if(session('message'))
        <div class="alert alert-warning mt-4">
            {{ session('message') }}
        </div>
    @endif

    @php
        // If a specific division is selected, filter the stocks; otherwise, use all.
        if ($selectedDivision !== 'all' && isset($division)) {
            $filteredStocks = $stocks->where('division_id', $division->id);
        } else {
            $filteredStocks = $stocks;
        }
        // Group the (filtered) stocks by category.
        $groupedStocks = $filteredStocks->groupBy('category');
    @endphp

    @if($groupedStocks->isNotEmpty())
        <table id="stocksTable" class="w-full mt-4 border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Category</th>
                    <th class="p-2">Total Quantity</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedStocks as $category => $group)
                    @php
                        $totalQuantity = $group->count();
                    @endphp
                    <tr class="border-b">
                        <td class="p-2">{{ $category }}</td>
                        <td class="p-2">{{ $totalQuantity }}</td>
                        <td class="p-2">
                            <a href="{{ route('stocks.byCategory', ['category' => $category, 'division' => $selectedDivision]) }}" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded transition duration-300 ease-in-out">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No stocks available for this division.</p>
    @endif
</div>
@endsection

@section('scripts')
    <!-- Include jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#stocksTable').DataTable({
                ordering: true,
                paging: false,    // Disable pagination if not needed
                info: false,      // Disable table info display
                searching: false, // Disable search box if not needed
                // You can optionally set an initial order, e.g. order by category (first column):
                order: [[0, "asc"]]
            });
        });
    </script>
@endsection
