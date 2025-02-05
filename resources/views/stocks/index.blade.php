@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Stock Inventory</h2>

    <!-- Category Filter -->
    <form method="GET" action="{{ route('stocks.index') }}" class="mb-4">
        <label for="category" class="block text-sm font-medium text-gray-700">Select Category:</label>
        <select name="category" id="category" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
            <option value="">All</option>
            <option value="Router" {{ request('category') == 'Router' ? 'selected' : '' }}>Router</option>
            <option value="Switch" {{ request('category') == 'Switch' ? 'selected' : '' }}>Switch</option>
            <option value="Modem" {{ request('category') == 'Modem' ? 'selected' : '' }}>Modem</option>
        </select>
    </form>

    <!-- Stock Inventory Table -->
    <table class="w-full border rounded-lg shadow-md bg-white">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Serial Number</th>
                <th class="p-2">Model</th>
                <th class="p-2">Category</th>
                <th class="p-2">Division</th>
                <th class="p-2">Remarks</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr class="border-b">
                <td class="p-2">{{ $stock->serial_number }}</td>
                <td class="p-2">{{ $stock->model }}</td>
                <td class="p-2">{{ $stock->category }}</td>
                <td class="p-2">{{ $stock->division->name }}</td>
                <td class="p-2">{{ $stock->remarks }}</td>
                <td class="p-2">
                    <a href="{{ route('stocks.edit', $stock->id) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection