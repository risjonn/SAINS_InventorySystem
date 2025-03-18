@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Dashboard</h2>

    <!-- Division Selection: only show for Super Admin -->
    @if(auth()->user()->role === 'Super Admin')
        <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
            <label for="division" class="block text-sm font-medium text-gray-700">Select Division:</label>
            <select name="division" id="division" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
                <!-- "All Divisions" option -->
                <option value="all" {{ $selectedDivision === 'all' ? 'selected' : '' }}>All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ $selectedDivision == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @else
        <!-- For regular admins, show their division (read-only) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Division:</label>
            <p class="mt-1 p-2 border rounded bg-gray-100">{{ $divisions->find($selectedDivision)->name }}</p>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Low Stock Items Card (Clickable) -->
    <a href="{{ route('stocks.low', ['division' => 'all']) }}" class="block">
        <div class="relative p-6 rounded-xl shadow-lg bg-yellow-100 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
            <!-- Glowing border layer (only when low stock exists) -->
            @if($lowStockItems > 0)
                <div class="absolute inset-0 rounded-xl animate-glow pointer-events-none"></div>
            @endif
      
            <!-- Card content -->
            <div class="relative">
                <div class="flex items-center">
                <h3 class="text-xl font-semibold {{ $lowStockItems > 0 ? 'text-yellow-800' : 'text-yellow-700' }}">
                    Low Stock Items
                </h3>
                @if($lowStockItems > 0)
                    <div class="relative ml-2">
                    <i class="fas fa-exclamation-circle text-yellow-800 text-lg animate-bounce-sync"></i>
                    </div>
                @endif
                </div>
                <p class="mt-2 text-3xl font-bold {{ $lowStockItems > 0 ? 'text-yellow-900' : 'text-yellow-800' }}">
                {{ $lowStockItems }}
                </p>
                <p class="mt-1 text-sm {{ $lowStockItems > 0 ? 'text-yellow-700' : 'text-yellow-600' }}">
                Click for details
                </p>
            </div>
        </div>
      </a>

    <!-- Recent Updates Card -->
    <div class="bg-gradient-to-br from-gray-100 to-gray-200 p-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
        <h3 class="text-xl font-semibold text-gray-800">Recent Updates</h3>
        <p class="mt-2 text-sm text-gray-600">Latest stock transactions</p>
    </div>
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
                        <td class="p-2">{{ $stock->category }}</td>
                        <td class="p-2">{{ $stock->quantity }}</td>
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
