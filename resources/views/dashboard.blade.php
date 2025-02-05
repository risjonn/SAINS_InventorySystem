@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Dashboard</h2>

    <!-- Division Selection -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
        <label for="division" class="block text-sm font-medium text-gray-700">Select Division:</label>
        <select name="division" id="division" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
            @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ $selectedDivision == $division->id ? 'selected' : '' }}>
                    {{ $division->name }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-100 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Total Stocks</h3>
            <p class="text-2xl font-bold">{{ $totalStocks }}</p>
        </div>

        <div class="bg-yellow-100 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Low Stock Items</h3>
            <p class="text-2xl font-bold">{{ $lowStockItems }}</p>
        </div>

        <div class="bg-green-100 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Total Users</h3>
            <p class="text-2xl font-bold">{{ $userCount }}</p>
        </div>

        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Recent Updates</h3>
            <p class="text-sm text-gray-600">Latest stock transactions</p>
        </div>
    </div>

    <!-- Stock Summary Table -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold">Stock Summary by Category ({{ $divisions->find($selectedDivision)->name }})</h3>
        <table class="w-full mt-2 border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Category</th>
                    <th class="p-2">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockSummary as $stock)
                <tr class="border-b">
                    <td class="p-2">{{ $stock->category }}</td>
                    <td class="p-2">{{ $stock->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recent Transactions Table -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold">Recent Transactions ({{ $divisions->find($selectedDivision)->name }})</h3>
        <table class="w-full mt-2 border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Serial Number</th>
                    <th class="p-2">Model</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Handled By</th>
                    <th class="p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $transaction)
                <tr class="border-b">
                    <td class="p-2">{{ $transaction->serial_number }}</td>
                    <td class="p-2">{{ $transaction->stock->model }}</td>
                    <td class="p-2 font-bold {{ $transaction->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type }}
                    </td>
                    <td class="p-2">{{ $transaction->user->name }}</td>
                    <td class="p-2">{{ $transaction->transaction_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection