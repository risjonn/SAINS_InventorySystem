@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">
        Stock Transaction
        @if(isset($division))
            <span class="text-sm ml-2 text-gray-600">(Division: {{ $division->name }})</span>
        @endif
    </h2>

    <!-- Tabbed Filter Interface -->
    <div class="my-4">
        <ul class="flex border-b">
            <li class="-mb-px mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'all']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter', 'all') == 'all' ? 'text-blue-800 font-bold bg-blue-200' : 'text-gray-500 font-bold' }}">
                    All
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'IN']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter') == 'IN' ? 'text-green-800 font-bold bg-green-200' : 'text-gray-500 font-bold' }}">
                    IN
                </a>
            </li>
            <li class="mr-1">
                <a href="{{ route('stocks.transactions', ['filter' => 'OUT']) }}"
                   class="bg-white inline-block border-l border-t border-r rounded-t px-4 py-2 {{ request('filter') == 'OUT' ? 'text-red-800 font-bold bg-red-200' : 'text-gray-500 font-bold' }}">
                    OUT
                </a>
            </li>
        </ul>
    </div>

    <!-- Table for Stock Transactions -->
    <table class="w-full border rounded-lg shadow-md bg-white mt-4">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-3">Serial Number</th>
                <th class="p-3">Model</th>
                <th class="p-3">Type (IN/OUT)</th>
                <th class="p-3">Handled by</th>
                <th class="p-3">Remarks</th>
                <th class="p-3">Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $transaction->serial_number }}</td>
                <td class="p-3">{{ $transaction->stock_model }}</td>
                <td class="p-3 font-bold {{ $transaction->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type }}
                </td>
                <td class="p-3">{{ $transaction->handled_by_name }}</td>
                <td class="p-3">{{ $transaction->remarks }}</td>
                <td class="p-3">
                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y h:i A') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-gray-500">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
