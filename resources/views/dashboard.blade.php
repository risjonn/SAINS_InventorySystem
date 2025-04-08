@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Dashboard</h2>

    <!-- Division Selection: only for Super Admin -->
    @if(auth()->user()->role === 'Super Admin')
        <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
            <label for="division" class="block text-sm font-medium text-gray-700">Select Division:</label>
            <select name="division" id="division" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
                <option value="all" {{ $selectedDivision === 'all' ? 'selected' : '' }}>All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ $selectedDivision == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @else
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Division:</label>
            <p class="mt-1 p-2 border rounded bg-gray-100">{{ $divisions->find($selectedDivision)->name }}</p>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Low Stock Items Card -->
        @if(auth()->user()->role === 'Super Admin' && $selectedDivision === 'all')
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 text-yellow-800">Low Stock Items by Division</h2>
            <div class="overflow-y-auto" style="max-height: 300px;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Division</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Low Stock Items</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($divisionsSummary as $division)
                            <tr class="hover:bg-yellow-100 cursor-pointer" onclick="window.location.href='{{ route('stocks.low', ['division' => $division['id']]) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-yellow-900">{{ $division['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full 
                                        {{ $division['critical'] > 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                        Low: {{ $division['nonCriticalLow'] }}, Critical: {{ $division['critical'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- New button to navigate to the detailed summary page -->
            <div class="mt-4 text-center">
                <a href="{{ route('stocks.low', ['division' => 'all']) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors duration-200">
                    View Full Summary
                </a>
            </div>
        </div>
        @else
            <a href="{{ route('stocks.low', ['division' => auth()->user()->division_id]) }}" class="block">
                <div class="relative p-6 bg-yellow-100 rounded-xl shadow-lg transition transform hover:scale-105 hover:shadow-2xl">
                    @if($lowStockItems > 0)
                        <div class="absolute inset-0 rounded-xl animate-glow pointer-events-none" style="z-index: -1;"></div>
                    @endif
                    <div class="relative z-10">
                        <div class="flex items-center">
                            <h3 class="text-xl font-semibold text-yellow-800">Low Stock Items</h3>
                            <i class="fas fa-exclamation-circle ml-2 text-yellow-800"></i>
                        </div>
                        <p class="mt-2 text-3xl font-bold text-yellow-900">{{ $lowStockItems }}</p>
                        <p class="mt-1 text-sm text-yellow-700">Click for details</p>
                    </div>
                </div>
            </a>
        @endif

        <!-- Optionally, you can add another card here for KPIs or key metrics if needed -->
        <!-- For example, a card for "Total Stocks" or "Recent Activity" could be added -->
    </div>

    <!-- Stock Summary Table -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold">
            Stock Summary by Category ({{ $selectedDivision === 'all' ? 'All Divisions' : $divisions->find($selectedDivision)->name }})
        </h3>
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
                        <td class="p-2">{{ $stock['category'] }}</td>
                        <td class="p-2">{{ $stock['quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recent Transactions Table -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold">
            Recent Transactions ({{ $selectedDivision === 'all' ? 'All Divisions' : $divisions->find($selectedDivision)->name }})
        </h3>
        <table class="w-full mt-2 border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Serial Number</th>
                    <th class="p-2">Model</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Handled By</th>
                    <th class="p-2">Date</th>
                    @if($selectedDivision === 'all')
                        <th class="p-2">Division</th>
                    @endif
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
                        <td class="p-2">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}</td>
                        @if($selectedDivision === 'all')
                            <td class="p-2">{{ $transaction->stock->division->name }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
