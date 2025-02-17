@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    <h2 class="text-xl font-semibold mt-4">
        Stock Overview
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ $divisionId === 'all' ? 'All Divisions' : $divisionName }})
        </span>
    </h2>

    @if(session('message'))
        <div class="alert alert-warning mt-4">
            {{ session('message') }}
        </div>
    @endif

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
                @foreach($groupedStocks as $group)
                    <tr class="border-b">
                        <td class="p-2">{{ $group->category }}</td>
                        <td class="p-2">{{ $group->total }}</td>
                        <td class="p-2">
                            <a href="{{ route('stocks.byCategory', ['category' => $group->category, 'division' => $divisionId]) }}" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded transition duration-300 ease-in-out">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination links for aggregated groups -->
        <div class="mt-4">
            {{ $groupedStocks->appends(request()->input())->links() }}
        </div>
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
                paging: false,    // Disable DataTables pagination since Laravel's pagination is used.
                info: false,
                searching: false,
                order: [[0, "asc"]]
            });
        });
    </script>
@endsection
