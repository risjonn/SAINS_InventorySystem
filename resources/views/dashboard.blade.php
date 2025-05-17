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
                <h2 class="text-2xl font-semibold mb-4 text-yellow-800">Low-Stock Overview</h2>

                {{-- Breadth & Depth --}}
                <div class="flex justify-between items-center mb-6">
                    <span class="text-yellow-800 font-medium">
                    Divisions Impacted: <strong>{{ $affectedDivisions }}</strong> / {{ $totalDivisions }}
                    </span>
                    <span class="text-yellow-800 font-medium">
                    SKUs Flagged: <strong>{{ $skuCount }}</strong>
                    </span>
                </div>

                {{-- Top 3 Divisions --}}
                <div class="overflow-y-auto" style="max-height: 240px;">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-100">
                        <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase">Division</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-yellow-700 uppercase">Critical</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-yellow-700 uppercase">Low</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-yellow-700 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topDivisions as $div)
                        @php
                            $total = $div['nonCriticalLow'] + $div['critical'];
                        @endphp
                        <tr 
                            class="hover:bg-yellow-100 cursor-pointer" 
                            onclick="window.location.href='{{ route('stocks.low', ['division'=>$div['id']]) }}'"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-yellow-900">
                            {{ $div['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                            {{ $div['critical'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-yellow-800">
                            {{ $div['nonCriticalLow'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-yellow-900 font-semibold">
                            {{ $total }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>

                <div class="mt-4 text-center">
                    <a 
                    href="{{ route('stocks.low', ['division' => 'all']) }}" 
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md"
                    >
                    View Full Summary
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('stocks.low', ['division' => $selectedDivision]) }}" class="block">
                <div class="relative p-6 bg-yellow-100 rounded-2xl shadow-xl transition-transform transform hover:scale-105 hover:shadow-2xl">
                @if($lowStockItems > 0)
                    <div class="absolute inset-0 rounded-2xl animate-glow pointer-events-none" style="z-index: -1;"></div>
                @endif

                {{-- Breadth & Depth --}}
                <div class="mb-4 flex justify-between">
                    <span class="text-yellow-800 font-medium">
                    Categories: <strong>{{ $categoryCount }}</strong> / {{ $totalCategories }}
                    </span>
                    <span class="text-yellow-800 font-medium">
                    SKUs Flagged: <strong>{{ $lowStockItems }}</strong>
                    </span>
                </div>

                {{-- Low vs Critical --}}
                <div class="flex space-x-4 mb-4">
                    <div class="flex-1 text-center px-4 py-3 bg-yellow-50 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-yellow-700">Low SKUs</p>
                    <p class="text-3xl font-bold text-yellow-800">{{ $lowCount }}</p>
                    </div>
                    <div class="flex-1 text-center px-4 py-3 bg-red-50 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-red-700">Critical SKUs</p>
                    <p class="text-3xl font-bold text-red-600">{{ $criticalCount }}</p>
                    </div>
                </div>

                {{-- Top Categories --}}
                @if($topCategories->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Top Categories</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($topCategories as $cat)
                        <div class="bg-white rounded-lg shadow px-4 py-3">
                        {{-- Category name --}}
                        <div class="text-gray-800 text-center font-medium mb-2">
                            {{ $cat['category'] }}
                        </div>
                        {{-- Badges row --}}
                        <div class="flex items-center space-x-3">
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                            Critical: {{ $cat['critical_count'] }}
                            </span>
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                            Low: {{ $cat['low_count'] }}
                            </span>
                        </div>
                        </div>
                    @endforeach
                    </div>
                </div>
                @endif

                <p class="text-center text-sm text-yellow-700 font-medium">
                    Click for detailed inventory
                </p>
                </div>
            </a>
        @endif
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
