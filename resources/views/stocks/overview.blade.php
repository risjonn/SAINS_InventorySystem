@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">
        Stock Overview
        @if(isset($division))
            <span class="text-sm ml-2 text-gray-600">(Division: {{ $division->name }})</span>
        @endif
    </h2>

    @if(session('message'))
        <div class="alert alert-warning mt-4">
            {{ session('message') }}
        </div>
    @endif

    @php
        // If a division is provided, filter the stocks by the division's id.
        if(isset($division)) {
            $stocks = $stocks->where('division_id', $division->id);
        }
        // Group the (filtered) stocks by category.
        $groupedStocks = $stocks->groupBy('category');
    @endphp

    <!-- Stock Overview Table -->
    @if($groupedStocks->isNotEmpty())
        <table class="w-full border rounded-lg shadow-md bg-white mt-4">
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
                        // Calculate the total quantity by counting the stock records in this group.
                        $totalQuantity = $group->count();
                    @endphp
                    <tr class="border-b">
                        <td class="p-2">{{ $category }}</td>
                        <td class="p-2">{{ $totalQuantity }}</td>
                        <td class="p-2">
                        <a href="{{ route('stocks.byCategory', ['category' => $category, 'division' => $division->id]) }}" class="text-blue-600 hover:underline">
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
